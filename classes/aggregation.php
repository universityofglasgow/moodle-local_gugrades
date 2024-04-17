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
 * Aggregation functions
 *
 * @package    local_gugrades
 * @copyright  2024
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_gugrades;

defined('MOODLE_INTERNAL') || die();

/**
 * Ideal number of characters for shortening grade category
 * and grade item names.
 */
define('SHORTNAME_LENGTH', 20);

require_once($CFG->dirroot . '/grade/lib.php');

/**
 * Class to store and manipulate grade structures for course
 */
class aggregation {

    /**
     * Factory for aggregation rule set
     * @param int $courseid
     * @return \local_gugrades\aggregation\base
     */
    public static function aggregation_factory(int $courseid) {

        // Just base at the moment, but other variations could exist.
        $aggregation = new \local_gugrades\aggregation\base($courseid);

        return $aggregation;
    }

    /**
     * Get grade items and grade categories for supplied gradecategoryid
     * @param int $courseid
     * @param int $gradecategoryid
     * @return array
     */
    public static function get_level(int $courseid, int $gradecategoryid) {
        global $DB;

        $sql = "SELECT *, gi.id AS gradeitemid, gc.id AS gradecategoryid FROM {grade_categories} gc
            JOIN {grade_items} gi ON gi.iteminstance = gc.id
            WHERE gi.itemtype = 'category'
            AND gc.courseid = :courseid
            AND gc.parent = :parent
            AND gc.hidden = :hidden";
        $gradecategories = $DB->get_records_sql($sql, [
            'courseid' => $courseid,
            'parent' => $gradecategoryid,
            'hidden' => 0,
        ]);

        // Run over and create short name to make table sane.
        $gradecategories = array_map(function($gc) {
            $gc->shortname = shorten_text($gc->fullname, SHORTNAME_LENGTH);
            $gc->weight = $gc->aggregationcoef;
            return $gc;
        }, $gradecategories);

        $sql = "SELECT * FROM {grade_items}
            WHERE (itemtype = 'mod' OR itemtype = 'manual')
            AND courseid = :courseid
            AND categoryid = :categoryid";
        $gradeitems = $DB->get_records_sql($sql, [
            'courseid' => $courseid,
            'categoryid' => $gradecategoryid,
        ]);

        // Short names for items.
        $gradeitems = array_map(function($gi) {
            $gi->shortname = shorten_text($gi->itemname, SHORTNAME_LENGTH);
            $gi->weight = $gi->aggregationcoef;
            $gi->gradeitemid = $gi->id;
            return $gi;
        }, $gradeitems);

        // Columns are a mix of grade categories and items.
        $columns = [];
        foreach ($gradecategories as $gradecategory) {
            $columns[] = (object)[
                'fieldname' => 'AGG_' . $gradecategory->gradeitemid,
                'gradeitemid' => $gradecategory->gradeitemid,
                'categoryid' => $gradecategory->gradecategoryid,
                'shortname' => $gradecategory->shortname,
                'fullname' => $gradecategory->fullname,

                // TODO - may not be so simple.
                'weight' => round($gradecategory->weight * 100),
            ];
        }
        foreach ($gradeitems as $gradeitem) {
            $columns[] = (object)[
                'fieldname' => 'AGG_' . $gradeitem->gradeitemid,
                'gradeitemid' => $gradeitem->gradeitemid,
                'categoryid' => 0,
                'shortname' => $gradeitem->shortname,
                'fullname' => $gradeitem->itemname,

                // TODO - may not be so simple.
                'weight' => round($gradeitem->weight * 100),
            ];
        }

        // Add gradetypes and maximum points to columns.
        foreach ($columns as $column) {
            $conversion = \local_gugrades\grades::conversion_factory($courseid, $column->gradeitemid);
            $column->gradetype = $conversion->name();
            $column->grademax = $conversion->get_maximum_grade();
            $column->isscale = $conversion->is_scale();
        }

        return [$gradecategories, $gradeitems, $columns];
    }

    /**
     * Is resit required?
     * TODO: Placeholder only
     * @param int $courseid
     * @param int $userid
     */
    protected static function is_resit_required(int $courseid, int $userid) {
        global $DB;

        return $DB->record_exists('local_gugrades_resitrequired', ['courseid' => $courseid, 'userid' => $userid]);
    }

    /**
     * Calculate course completion %age for user
     * TODO - just a placeholder. Needs funcionality.
     * @param int $courseid
     * @param int $userid
     * @return int
     */
    protected static function completed(int $courseid, int $userid) {
        return 100;
    }

    /**
     * Get students - with some filtering
     * $firstname and $lastname are single initial character only.
     * @param int $courseid
     * @param string $firstname
     * @param string $lastname
     * @param int $groupid
     * @return array
     */
    public static function get_users(int $courseid, string $firstname, string $lastname, int $groupid) {
        $context = \context_course::instance($courseid);
        $users = \local_gugrades\users::get_gradeable_users($context, $firstname,
            $lastname, $groupid);

        // Add aditional fields.
        foreach ($users as $user) {
            $user->displayname = fullname($user);
            $user->resitrequired = self::is_resit_required($courseid, $user->id);

            // TODO - just a placeholder at the moment.
            $user->coursetotal = 'Missing grades';

            // TODO - just a placeholder at the moment.
            $user->completed = self::completed($courseid, $user->id);
        }

        // Pictures.
        $users = \local_gugrades\users::add_pictures_to_user_records($users);

        return array_values($users);
    }

    /**
     * Get aggregation grade
     * Current provisional/released grade for grade item
     * TODO: Or aggregagated grade for sub-category
     * @param int $gradeitemid
     * @param int $userid
     * @return float
     */
    protected static function get_aggregation_grade(int $gradeitemid, int $userid) {
        global $DB;

    }

    /**
     * Add aggregation data to users.
     * Each user record contains list based on columns
     * Formatted to survive web services (will need reformatted for EasyDataTable)
     * @param int $courseid
     * @param array $users
     * @param array $columns
     * @return array
     */
    public static function add_aggregation_fields_to_users(int $courseid, array $users, array $columns) {
        $aggregation = self::aggregation_factory($courseid);
        foreach ($users as $user) {
            $fields = [];
            foreach ($columns as $column) {

                // Field identifier based on gradeitemid (which is unique even for categories).
                $provisional = $aggregation->get_provisional($column->gradeitemid, $user->id);
                if ($provisional) {
                    list($grade, $displaygrade) = $provisional;
                } else {
                    $displaygrade = 'No data';
                }
                $fieldname = 'AGG_' . $column->gradeitemid;
                $data = [
                    'fieldname' => $fieldname,
                    'display' => $displaygrade,
                ];
                $fields[] = $data;
            }

            $user->fields = $fields;
        }

        return $users;
    }

    /**
     * Get "breadcrumb" trail for given gradecategoryid
     * Return array of ['id' => ..., 'shortname' => ...]
     * @param int $gradecategoryid
     * @return array
     */
    public static function get_breadcrumb(int $gradecategoryid) {
        global $DB;

        $category = $DB->get_record('grade_categories', ['id' => $gradecategoryid], '*', MUST_EXIST);
        $path = explode('/', trim($category->path, '/'));
        array_shift($path);

        if ($path) {
            $breadcrumb = [];
            foreach ($path as $id) {
                $pathcat = $DB->get_record('grade_categories', ['id' => $id], '*', MUST_EXIST);
                $breadcrumb[] = [
                    'id' => $id,
                    'shortname' => shorten_text($pathcat->fullname, SHORTNAME_LENGTH),
                ];
            }

            return $breadcrumb;
        } else {
            return [];
        }
    }

    /**
     * Is this a "top level" category?
     * Table layout is slightly different at the toppermost level
     * @param int $gradecategoryid
     * @return bool
     */
    public static function is_top_level(int $gradecategoryid) {
        global $DB;

        $gradecategory = $DB->get_record('grade_categories', ['id' => $gradecategoryid], '*', MUST_EXIST);

        return $gradecategory->depth == 2;
    }

    /**
     * Recursive helper to build grade-item tree
     * @param int $courseid
     * @param int $gradecategoryid
     * @return object
     */
    protected static function recurse_tree(int $courseid, int $gradecategoryid) {
        global $DB;

        // Get the category and corresponding instance.
        $gcat = $DB->get_record('grade_categories', ['id' => $gradecategoryid], '*', MUST_EXIST);
        $gradeitem = $DB->get_record('grade_items', ['iteminstance' => $gradecategoryid, 'itemtype' => 'category'], '*', MUST_EXIST);
        $categorynode = (object)[
            'iscategory' => true,
            'categoryid' => $gradecategoryid,
            'itemid' => $gradeitem->id,
            'name' => $gcat->fullname,
            'keephigh' => $gcat->keephigh,
            'droplow' => $gcat->droplow,
            'aggregation' => $gcat->aggregation,
            'weight' => $gradeitem->aggregationcoef,
            'isscale' => false,  // TODO - need to figure this out properly.
            'children' => [],
        ];

        // Get any categories at this level (and recurse into them).
        // Categories are stored in the grade_items table but (for some reason)
        // the (parent) categoryid field is null. So...
        $childcategories = $DB->get_records('grade_categories', ['parent' => $gradecategoryid]);
        foreach ($childcategories as $childcategory) {
            $categorynode->children[] = self::recurse_tree($courseid, $childcategory->id);
        }

        // Get grade items in this grade category.
        $items = $DB->get_records('grade_items', ['categoryid' => $gradecategoryid]);
        foreach ($items as $item) {

            // Get the conversion object, so we can tell what sort of grade we're dealing with.
            $conversion = \local_gugrades\grades::conversion_factory($courseid, $item->id);
            $node = (object)[
                'itemid' => $item->id,
                'name' => $item->itemname,
                'iscategory' => false,
                'isscale' => $conversion->is_scale(),
                'weight' => $item->aggregationcoef,
            ];

            $categorynode->children[] = $node;
        }

        return $categorynode;
    }

    /**
     * Use the array of items for a given gradecategory and produce
     * an aggregated grade (or not).
     * The category object is provided to identify aggregation settings
     * and so on
     * Note that this will be for one gradecategory for one user, only.
     * @param oject $category
     * @param array $items
     *
     */
    protected static function aggregate_user_category(object $category, array $items) {

        $keephigh = $category->keephigh;
        $droplow = $category->droplow;
        $aggregation = $category->aggregation;

        // Quick check - all items must have a grade
        foreach ($items as $item) {
            if ($item->grademissing) {
                return null;
            }
        }
    }

    /**
     * Aggregate user data recursively
     * (starting with current category)
     * Returning array of category totals for that user
     * $allitems 'collects' the various totals to display on the aggregation table
     * Returns aggregated total or null if data is incomplete
     * @param int $courseid
     * @param object $category
     * @param object $user
     * @param array $allitems
     * @return float or null
     */
    protected static function aggregate_user(int $courseid, object $category, object $user, array &$allitems) {

        // Information about the category is in the param
        // The field 'children' holds all the sub-items and sub-categories that
        // we need to 'add up'.
        // Get array of data to aggregate for this 'level' and then send off to
        // the aggregation function
        $children = $category->children;
        $items = [];
        foreach ($children as $child) {

            // If this is itself a grade category then we need to recurse to get the details
            // of this.
            if ($child->iscategory) {
                $childcategorytotal = self::aggregate_user($courseid, $child, $user, $allitems);
                $item = (object)[
                    'itemid' => $child->itemid,
                    'categoryid' => $child->categoryid,
                    'iscategory' => true,
                    'isscale' => $child->isscale,
                    'grademissing' => $childcategorytotal == null,
                    'grade' => $childcategorytotal,
                ];
                //var_dump($childcategory);
            } else {

                // Is there a grade (in MyGrades) for this user?
                // Provisional will be null if nothing has been imported.
                $usercapture = new \local_gugrades\usercapture($courseid, $child->itemid, $user->id);
                $provisional = $usercapture->get_provisional();
                if ($provisional) {
                    $item = (object)[
                        'itemid' => $child->itemid,
                        'iscategory' => false,
                        'grademissing' => false,
                        'grade' => $provisional->convertedgrade,
                        'displaygrade' => $provisional->displaygrade,
                        'isscale' => $child->isscale,
                    ];
                } else {
                    $item = (object)[
                        'itemid' => $child->itemid,
                        'iscategory' => false,
                        'grademissing' => true,
                    ];
                }
                //var_dump($provisional);
            }
            $items[$child->itemid] = $item;
            $allitems[$child->itemid] = $item;
        }

        // List of items should hold list for this gradecategory only, ready
        // to aggregate.
        $total = self::aggregate_user_category($category, $items);

        return $total;

    }

    /**
     * Entry point for calculating aggregations
     * @param int $courseid
     * @param int $gradecategoryid
     * @param array $users
     */
    public static function aggregate(int $courseid, int $gradecategoryid, array $users) {

        // First get category structure
        $toplevel = self::recurse_tree($courseid, $gradecategoryid);

        // Run through each user and aggregate their grades
        foreach ($users as $user) {
            $userallitems = [];
            $usertotal = self::aggregate_user($courseid, $toplevel, $user, $userallitems);
            //var_dump($user->id); var_dump($userallitems); var_dump($usertotal);
        }
    }

}
