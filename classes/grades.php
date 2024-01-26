<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language EN
 *
 * @package    local_gugrades
 * @copyright  2023
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_gugrades;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/grade/lib.php');

/**
 * Class to store and manipulate grade structures for course
 */
class grades {

    /**
     * Get item name from gradeitemid
     * @param int $gradeitemid
     * @return string
     */
    public static function get_item_name_from_itemid(int $gradeitemid) {
        global $DB;

        if ($gradeitem = $DB->get_record('grade_items', ['id' => $gradeitemid])) {
            return $gradeitem->itemname;
        }

        return '';
    }

    /**
     * Get first level categories (should be summative / formative and so on)
     * Actually depth==2 in the database (1 == top level)
     * @param int $courseid
     * @return array
     */
    public static function get_firstlevel(int $courseid) {
        global $DB;

        $gradecategories = $DB->get_records('grade_categories', [
            'courseid' => $courseid,
            'hidden' => 0,
        ]);

        $cats = [];
        foreach ($gradecategories as $category) {
            if ($category->depth == 2) {
                $cats[] = $category;
            }
        }

        return $cats;
    }

    /**
     * Get the category/item tree beneath the selected depth==2 category.
     * @param int $courseid
     * @param int $categoryid
     * @return object
     */
    public static function get_activitytree(int $courseid, int $categoryid) {
        global $DB;

        $category = $DB->get_record('grade_categories', ['id' => $categoryid], '*', MUST_EXIST);
        $gradeitems = $DB->get_records('grade_items', [
            'courseid' => $courseid,
        ]);
        $gradecategories = $DB->get_records('grade_categories', [
            'courseid' => $courseid,
        ]);
        $categorytree = self::recurse_activitytree($category, $gradeitems, $gradecategories);

        return $categorytree;
    }

    /**
     * Recursive routine to build activity tree
     * Tree consists of both sub-categories and grade items
     * {
     *     category -> current category
     *     items -> array of grade items in this category
     *     categories -> array of grade categories, children of this category (recursive)
     * }
     * @param object $category
     * @param array $gradeitems
     * @param array $gradecategories
     * @return object
     */
    private static function recurse_activitytree($category, $gradeitems, $gradecategories) {
        $tree = [];

        // First find any grade items attached to the current category.
        $items = [];
        foreach ($gradeitems as $item) {
            if ($item->categoryid == $category->id) {
                $items[$item->id] = $item;
            }
        }

        // Next find any sub-categories of this category.
        $categories = [];
        foreach ($gradecategories as $gradecategory) {
            if ($gradecategory->parent == $category->id) {
                $categories[$gradecategory->id] = self::recurse_activitytree($gradecategory, $gradeitems, $gradecategories);
            }
        }

        // Add this all up
        // (array_values() to prevent arrays beening encoded as objects in JSON).
        $record = new \stdClass();
        $record->category = $category;
        $record->items = array_values($items);
        $record->categories = array_values($categories);

        return $record;
    }

    /**
     * Given gradecatoryid - get all the items in that category
     * and in an child categories (recursively)
     * (a bit like recurse_activitytree but only items)
     * We don't actually recurse - just use the path
     * @param object $gradecategory
     * @return array (of grade_items)
     */
    public static function get_gradeitems_recursive(object $gradecategory) {
        global $DB;

        // Whatever path this has will be the start of all other paths that we want.
        $path = $gradecategory->path;
        $sql = "SELECT gi.* from {grade_items} gi
            JOIN {grade_categories} gc ON gi.categoryid = gc.id
            WHERE gc.path LIKE :path";
        $items = $DB->get_records_sql($sql, ['path' => $path . '%']);

        return $items;
    }

    /**
     * Check that all grades are the same for a potential recursive import
     * For a given gradeitemid, we're looking at that items *peers* and any
     * children thereof. So we want to start with the parent category of the
     * supplied gradeitemid
     * @param int $gradeitemid
     * @return array(recursiveavailable, recursivematch)
     */
    public static function recursive_import_match(int $gradeitemid) {
        global $DB;

        $gradeitem = $DB->get_record('grade_items', ['id' => $gradeitemid], '*', MUST_EXIST);
        $courseid = $gradeitem->courseid;
        $categoryid = $gradeitem->categoryid;

        $recursiveavailable = false;
        $recursivematch = false;

        // This MUST be a 'second level' category. Which is actually the 3rd one down.
        // SO it will have a path field like /a/b/c/ or longer.
        // If not, recursive import is not available.
        $gradecategory = $DB->get_record('grade_categories', ['id' => $categoryid], '*', MUST_EXIST);

        // Trim to remove leading and trailing /, otherwise you get two extra empty fields.
        $pathcats = explode('/', trim($gradecategory->path, '/'));
        if (count($pathcats) > 2) {
            $recursiveavailable = true;

            // Get grade items.
            if ($items = self::get_gradeitems_recursive($gradecategory)) {

                // As a basic check grade min, max and scale type need to match.
                $first = array_shift($items);
                $recursivematch = true;
                foreach ($items as $item) {
                    if (
                        ($first->grademax != $item->grademax) ||
                        ($first->grademin != $item->grademin) ||
                        ($first->scaleid != $item->scaleid)
                    ) {
                        $recursivematch = false;
                    }
                }
            }
        }

        return [
            $recursiveavailable,
            $recursivematch,
        ];
    }

    /**
     * Get the grade column record for the gradetype and (optionally)
     * 'other' text
     * @param int $courseid
     * @param int $gradeitemid
     * @param string $gradetype
     * @param string $other
     * @return object
     */
    public static function get_column(int $courseid, int $gradeitemid, string $gradetype, string $other = '') {
        global $DB;

        // Check 'other' text is valid.
        $other = trim($other);
        if (($gradetype != 'OTHER') && !empty($other)) {
            throw new \moodle_exception('Other text provided for non-other gradetype');
        }
        if (($gradetype == 'OTHER') && empty($other)) {
            throw new \moodle_exception('No other text provided for other gradetype');
        }

        // Does record exist?
        if (!$other) {
            if ($column = $DB->get_record('local_gugrades_column', ['gradeitemid' => $gradeitemid, 'gradetype' => $gradetype])) {
                return $column;
            }
        } else {

            // If other text, due to sql_compare_text it all gets a bit more complicated.
            $compareother = $DB->sql_compare_text('other');
            $sql = "SELECT * FROM {local_gugrades_column}
                WHERE gradeitemid = :gradeitemid
                AND gradetype = :gradetype
                AND $compareother = :other";
            if ($column = $DB->get_record_sql($sql,
                ['gradeitemid' => $gradeitemid, 'gradetype' => $gradetype, 'other' => $other])) {
                return $column;
            }
        }

        // Failing the above, we need a new column record.
        $column = new \stdClass;
        $column->courseid = $courseid;
        $column->gradeitemid = $gradeitemid;
        $column->gradetype = $gradetype;
        $column->other = $other;
        $column->id = $DB->insert_record('local_gugrades_column', $column);

        return $column;
    }

    /**
     * Write grade to local_gugrades_grade table
     *
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $userid
     * @param string $admingrade
     * @param float $rawgrade
     * @param float $convertedgrade
     * @param string $displaygrade
     * @param float $weightedgrade
     * @param string $gradetype
     * @param string $other
     * @param bool $iscurrent
     * @param string $auditcomment
     */
    public static function write_grade(
        int $courseid,
        int $gradeitemid,
        int $userid,
        string $admingrade,
        float $rawgrade,
        float $convertedgrade,
        string $displaygrade,
        float $weightedgrade,
        string $gradetype,
        string $other,
        bool $iscurrent,
        string $auditcomment = ''
    ) {
        global $DB, $USER;

        // Get/create the column entry.
        $column = self::get_column($courseid, $gradeitemid, $gradetype, $other);

        // Does this already exist.
        $gradetypecompare = $DB->sql_compare_text('gradetype');
        $sql = 'SELECT * FROM {local_gugrades_grade}
            WHERE courseid = :courseid
            AND gradeitemid = :gradeitemid
            AND userid = :userid
            AND iscurrent = :iscurrent
            AND columnid = :columnid
            AND ' . $gradetypecompare . ' = :gradetype';
        if ($oldgrades = $DB->get_records_sql($sql, [
            'courseid' => $courseid,
            'gradeitemid' => $gradeitemid,
            'userid' => $userid,
            'iscurrent' => true,
            'columnid' => $column->id,
            'gradetype' => $gradetype,
        ])) {
            foreach ($oldgrades as $oldgrade) {

                // It's not current any more.
                $oldgrade->iscurrent = false;
                $DB->update_record('local_gugrades_grade', $oldgrade);
            }
        }

        $gugrade = new \stdClass;
        $gugrade->courseid = $courseid;
        $gugrade->gradeitemid = $gradeitemid;
        $gugrade->userid = $userid;
        $gugrade->rawgrade = $rawgrade;
        $gugrade->admingrade = $admingrade;
        $gugrade->convertedgrade = $convertedgrade;
        $gugrade->displaygrade = $displaygrade;
        $gugrade->weightedgrade = $weightedgrade;
        $gugrade->gradetype = $gradetype;
        $gugrade->other = $other;
        $gugrade->iscurrent = true;
        $gugrade->columnid = $column->id;
        $gugrade->auditby = $USER->id;
        $gugrade->audittimecreated = time();
        $gugrade->auditcomment = $auditcomment;
        $DB->insert_record('local_gugrades_grade', $gugrade);
    }

    /**
     * Get grade from array by reason
     * @param array $grades
     * @param string $reason
     * @return object
     */
    private static function get_grade_by_reason(array $grades, string $reason) {
        $grade = array_column($grades, null, 'reasonshortname')[$reason] ?? false;

        return $grade->grade;
    }

    /**
     * Work out provisional grade
     * TODO: This is just a 'dummy' - needs lots more logic
     * @param array $grades
     * @return float
     */
    private static function get_provisional_grade($grades) {

        // ATM provision grade is the same as FIRST grade.
        if ($grade = self::get_grade_by_reason($grades, 'FIRST')) {
            return $grade->grade;
        }

        return false;
    }

    /**
     * Add grades to user records for capture page
     * @param int $courseid
     * @param int $gradeitemid
     * @param array $users
     * @return array
     */
    public static function add_grades_to_user_records(int $courseid, int $gradeitemid, array $users) {
        foreach ($users as $user) {
            $usercapture = new usercapture($courseid, $gradeitemid, $user->id);
            $user->grades = $usercapture->get_grades();
            $user->alert = $usercapture->alert();
        }

        return $users;
    }

    /**
     * Is grade supported (e.g. scale with no scale mapping)
     * @param int $gradeitemid
     * @return bool
     */
    public static function is_grade_supported(int $gradeitemid) {
        global $DB;

        $gradeitem = $DB->get_record('grade_items', ['id' => $gradeitemid], '*', MUST_EXIST);
        $gradetype = $gradeitem->gradetype;
        if (($gradetype == GRADE_TYPE_NONE) || ($gradetype == GRADE_TYPE_TEXT)) {
            return false;
        }
        if ($gradetype == GRADE_TYPE_SCALE) {
            $scaleid = $gradeitem->scaleid;
            if (!$DB->record_exists_sql('select * from {local_gugrades_scalevalue} where scaleid=:scaleid',
                ['scaleid' => $scaleid])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if grade item is hidden or locked
     * @param int $gradeitemid
     * @return array [$hidden, $locked]
     */
    public static function is_grade_hidden_locked(int $gradeitemid) {
        global $DB;

        $gradeitem = $DB->get_record('grade_items', ['id' => $gradeitemid], '*', MUST_EXIST);

        return [
            $gradeitem->hidden,
            $gradeitem->locked,
        ];
    }

    /**
     * Analyse grade item. Is it...
     * - is it valid at all
     * - points value
     * - if so, is the max points 22 (proxy for 22 point scale)
     * - scale
     * - if so, is it the 22 point scale
     * TODO: Need to look at Schedule B
     *
     * Returns ['scale' | 'scale22' | 'value' | false, $gradeitem] or [false, false]
     * @param int $gradeitemid
     * @return array
     */
    public static function analyse_gradeitem(int $gradeitemid) {
        global $DB;

        // Is it valid at all?
        if (!self::is_grade_supported($gradeitemid)) {
            return [false, false];
        }

        $gradeitem = $DB->get_record('grade_items', ['id' => $gradeitemid], '*', MUST_EXIST);
        $gradetype = $gradeitem->gradetype;
        if ($gradetype == GRADE_TYPE_VALUE) {
            if ($gradeitem->grademax == 22) {

                // TODO: May change but to get it working.
                return ['value', $gradeitem];
            } else {
                return ['value', $gradeitem];
            }
        } else if ($gradetype == GRADE_TYPE_SCALE) {
            if (($gradeitem->grademin == 1) && ($gradeitem->grademax == 23)) {
                return ['scale22', $gradeitem];
            } else {
                return ['scale', $gradeitem];
            }
        }

        throw new \moodle_exception('Invalid gradeitem encountered in grades::analyse_gradeitem');
    }

    /**
     * Have any grades already been imported for gradeitem
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $groupid
     * @return bool
     */
    public static function is_grades_imported(int $courseid, int $gradeitemid, int $groupid = 0) {
        global $DB;

        if ($groupid) {
            $sql = "SELECT * FROM {local_gugrades_grade} gg
                JOIN {groups_members} gm ON gm.userid = gg.userid
                WHERE gg.gradeitemid = :gradeitemid
                AND gm.groupid = :groupid";
                $params = ['gradeitemid' => $gradeitemid, 'groupid' => $groupid];
        } else {
            $sql = 'select * from {local_gugrades_grade} where gradeitemid=:gradeitemid';
            $params = ['gradeitemid' => $gradeitemid];
        }

        return $DB->record_exists_sql($sql, $params);
    }

    /**
     * Check if gradeitem / user combo has any imported/added grades
     * @param int $gradeitemid
     * @param int $userid
     * @return bool
     */
    public static function user_has_grades(int $gradeitemid, int $userid) {
        global $DB;

        return $DB->record_exists('local_gugrades_grade', ['gradeitemid' => $gradeitemid, 'userid' => $userid]);
    }

    /**
     * Get grade capture columns
     * Get the different grade types used for this capture
     * Each gradetype == OTHER with distinct 'other' text is considered a different column
     * @param int $courseid
     * @param int $gradeitemid
     * @return array
     */
    public static function get_grade_capture_columns(int $courseid, int $gradeitemid) {
        global $DB;

        if ($columns = $DB->get_records('local_gugrades_column', ['gradeitemid' => $gradeitemid])) {

            // As there is at least one column then there must be a provisional
            // But it has to go at the end.
            $provisionalcolumn = self::get_column($courseid, $gradeitemid, 'PROVISIONAL');
            if (isset($columns[$provisionalcolumn->id])) {
                unset($columns[$provisionalcolumn->id]);
            }
            $columns = array_values($columns);
            $columns[] = $provisionalcolumn;

            // Add descriptions.
            foreach ($columns as $column) {
                if ($column->gradetype == 'OTHER') {
                    $column->description = $column->other;
                } else {
                    $column->description = gradetype::get_description($column->gradetype);
                }
            }

        } else {
            $columns = [];
        }

        // There has to be a first column.
        if (!in_array('FIRST', array_column($columns, 'gradetype'))) {
            $firstcolumn = (object)[
                'id' => 0,
                'gradetype' => 'FIRST',
                'description' => gradetype::get_description('FIRST'),
            ];
            array_unshift($columns, $firstcolumn);
        }

        // Sort columns.
        $columns = \local_gugrades\gradetype::sort($columns);

        return $columns;
    }

    /**
     * Factory for conversion class
     * TODO: May need some improvement in detecting correct/supported grade (type)
     * The name of the class is in the scaletype table.
     * @param int $courseid
     * @param int $gradeitemid
     * @return object
     */
    public static function conversion_factory(int $courseid, int $gradeitemid) {
        global $DB;

        $gradeitem = $DB->get_record('grade_items', ['id' => $gradeitemid], '*', MUST_EXIST);
        $gradetype = $gradeitem->gradetype;

        // Is it a scale of some sort?
        if ($gradetype == GRADE_TYPE_SCALE) {

            // See if scale is in our scaletype table.
            if (!$scaletype = $DB->get_record('local_gugrades_scaletype', ['scaleid' => $gradeitem->scaleid])) {
                throw new \moodle_exception('Unsupported scale in conversion_factory. ID = ' . $gradeitem->scaleid);
            }

            // Get the name of the class and see if it exists.
            $classname = 'local_gugrades\\conversion\\' . $scaletype->type;
            if (!class_exists($classname, true)) {
                throw new \moodle_exception('Unknown conversion class - "' . $scaletype->scale . '"');
            }

            return new $classname($courseid, $gradeitemid);
        } else {

            // We're assuming it's a points scale (already checked for weird types).
            return new \local_gugrades\conversion\points($courseid, $gradeitemid);
        }
    }

    /**
     * Get scale as value => name associative array
     * This is from our 'scalevalue' table
     * @param int $scaleid
     * @return array
     *
     */
    public static function get_scale(int $scaleid) {
        global $DB;

        if ($items = $DB->get_records('local_gugrades_scalevalue', ['scaleid' => $scaleid])) {
            $output = [];
            foreach ($items as $item) {
                $output[$item->value] = $item->item;
            }

            return $output;
        } else {
            throw new \moodle_exception('Invalid scaleid in grades::get_scale');
        }
    }

    /**
     * Get grades for display on Dashboard for a give gradecategoryid
     * Basically just returns realeased grades (TODO: is that correct?)
     * @param int $userid
     * @param int $gradecategoryid
     * @return array
     */
    public static function get_dashboard_grades(int $userid, int $gradecategoryid) {
        global $DB;

        // Get grades.
        $sql = "SELECT  * FROM {local_gugrades_grade} gg
            JOIN {grade_items} gi ON gi.id = gg.gradeitemid
            WHERE gi.categoryid = :gradecategoryid
            AND gg.userid = :userid
            AND gg.iscurrent = 1
            AND gg.gradetype = 'RELEASED'";
        $grades = $DB->get_records_sql($sql, ['gradecategoryid' => $gradecategoryid, 'userid' => $userid]);

        return $grades;
    }

    /**
     * Delete all data for gradeitemid
     * @param int $gradeitemid
     */
    public static function delete_grade_item(int $gradeitemid) {
        global $DB;

        $DB->delete_records('local_gugrades_grade', ['gradeitemid' => $gradeitemid]);
        $DB->delete_records('local_gugrades_audit', ['gradeitemid' => $gradeitemid]);
    }
}
