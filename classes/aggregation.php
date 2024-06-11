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
     * @param string $atype
     * @return \local_gugrades\aggregation\base
     */
    public static function aggregation_factory(int $courseid, string $atype) {

        // Just base at the moment, but other variations could exist.
        $aggregation = new \local_gugrades\aggregation\base($courseid, $atype);

        return $aggregation;
    }

    /**
     * Get aggregation tale columns for supplied gradecategoryid
     * @param int $courseid
     * @param int $gradecategoryid
     * @return [array, string]
     */
    public static function get_columns(int $courseid, int $gradecategoryid) {
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
            $column->schedule = $conversion->get_schedule();
        }

        // Get aggregation type for these columns (i.e. this grade category)
        $atype = self::get_aggregation_type($columns);

        return [$columns, $atype];
    }

    /**
     * Is resit required?
     * @param int $courseid
     * @param int $userid
     * @return boolean
     */
    protected static function is_resit_required(int $courseid, int $userid) {
        global $DB;

        return $DB->record_exists('local_gugrades_resitrequired', ['courseid' => $courseid, 'userid' => $userid]);
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
            $user->total = get_string('gradesmissing', 'local_gugrades');
            $user->completed = 0;
            $user->error = get_string('gradesmissing', 'local_gugrades');
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
     * @param array $users
     * @param array $columns
     * @return array
     */
    public static function add_aggregation_fields_to_users(array $users, array $columns) {
        foreach ($users as $user) {
            $fields = [];
            foreach ($columns as $column) {

                // Field identifier based on gradeitemid (which is unique even for categories).
                $provisional = \local_gugrades\grades::get_provisional_from_id($column->gradeitemid, $user->id);
                if ($provisional) {
                    [$grade, $displaygrade] = $provisional;
                } else {
                    $displaygrade = get_string('nodata', 'local_gugrades');
                }
                $fieldname = 'AGG_' . $column->gradeitemid;
                $data = [
                    'fieldname' => $fieldname,
                    'itemname' => $column->shortname,
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
     * Determine type of aggregated result for a given grade category. Possibilities
     * 1. If all columns are points then result is points
     * 2. If there are a mix of points and scales then it's an error
     * 3. If all Schedule A then result is Schedule A
     * 4. If all Schedule B then result is Schedule B
     * 5. If mix of Schedule A/B then Schedule A if >=50% by weight is Sched A, otherwise Sched B (see MGU-812)
     * @param array $items
     * @return array(string, string)
     */
    public static function get_aggregation_type(array $items) {
        $sumofweights = 0;
        $sumscheduleaweights = 0;
        $sumschedulebweights = 0;
        $countpoints = 0;
        foreach ($items as $item) {

            // If any item schedule is an error, then the result is as well.
            if ($item->schedule == 'E') {
                $atype = \local_gugrades\GRADETYPE_ERROR;
                return $atype;
            }
            $sumofweights += $item->weight;
            if ($item->schedule == 'A') {
                $sumscheduleaweights += $item->weight;
            } else if ($item->schedule == 'B') {
                $sumschedulebweights += $item->weight;
            } else {
                $countpoints++;
            }
        }

        // Schedule to label category for further aggregation
        // A, B or empty
        $atype = \local_gugrades\GRADETYPE_POINTS;

        // If sumofweights is zero, we're going to get divide-by-zero
        // errors down the line.
        if ($sumofweights == 0) {
            $atype = \local_gugrades\GRADETYPE_ERROR;
            return $atype;
        }

        // Now work out what we have
        if ($countpoints == count($items)) {
            $atype = \local_gugrades\GRADETYPE_POINTS;
        } else if ($countpoints != 0) {
            $atype = \local_gugrades\GRADETYPE_ERROR;
            $schedule = 'E';
        } else if ($sumscheduleaweights >= ($sumofweights / 2)) {
            $atype = \local_gugrades\GRADETYPE_SCHEDULEA;
        } else {
            $atype = \local_gugrades\GRADETYPE_SCHEDULEB;
        }

        return $atype;
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
        $gradeitem = $DB->get_record('grade_items',
            ['iteminstance' => $gradecategoryid, 'itemtype' => 'category'], '*', MUST_EXIST);
        $categorynode = (object)[
            'iscategory' => true,
            'categoryid' => $gradecategoryid,
            'itemid' => $gradeitem->id,
            'name' => $gcat->fullname,
            'keephigh' => $gcat->keephigh,
            'droplow' => $gcat->droplow,
            'aggregation' => $gcat->aggregation,
            'weight' => $gradeitem->aggregationcoef,
            'grademax' => $gradeitem->grademax,
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
                'schedule' => $conversion->get_schedule(),
                'weight' => $item->aggregationcoef,
                'grademax' => $item->grademax,
            ];

            $categorynode->children[] = $node;
        }

        // Process $categorynode->children such that we know what the category's
        // aggregation type is (Schedule A, B, POINTS).
        $atype = self::get_aggregation_type($categorynode->children);
        $categorynode->atype = $atype;
        $categorynode->schedule = $atype;

        return $categorynode;
    }

    /**
     * Use the array of items for a given gradecategory and produce
     * an aggregated grade (or not).
     * The category object is provided to identify aggregation settings
     * and so on
     * Note that this will be for one gradecategory for one user, only.
     * @param int $courseid
     * @param object $category
     * @param array $items
     * @param int $level
     * @return array [grade, completion, error]
     */
    protected static function aggregate_user_category(int $courseid, object $category, array $items, int $level) {

        // Get appropriate aggregation 'rule' set
        $aggregation = self::aggregation_factory($courseid, $category->atype);

        // Get basic data about aggregation
        // (this is also a check that it actually exists).
        $keephigh = $category->keephigh;
        $droplow = $category->droplow;
        $aggmethod = $category->aggregation;

        // 0 based keys, please.
        $items = array_values($items);

        // If level 1 then calculate completion %age.
        // This can be calculated even though we can't run rest of aggregation (incomplete).
        $completion = 0;
        if ($level == 1) {
            $completion = $aggregation->completion($items);
        }

        // Get the correct aggregation function.
        $aggfunction = $aggregation->strategy_factory($aggmethod);

        // Quick check - all items must have a grade.
        foreach ($items as $item) {
            if ($item->grademissing) {
                return [null, $completion, get_string('gradesmissing', 'local_gugrades')];
            }
        }

        // Pre-process.
        $items = $aggregation->pre_process_items($items);

        // The first item will tell us if we are dealing with scale or points.
        $isscale = $items[0]->isscale;

        // Now check that all items are the same.
        foreach ($items as $item) {
            if ($item->isscale != $isscale) {
                return [null, $completion, get_string('gradesnotmatching', 'local_gugrades')];
            }
        }

        // Now call the appropriate aggregation function to do the sums.
        $aggregatedgrade = call_user_func([$aggregation, $aggfunction], $items);

        return [$aggregatedgrade, $completion, ''];
    }

    /**
     * Write aggregated category into gugrades_grades table
     * ONLY write if it hasn't changed (otherwise table just fills up)
     * TODO - need to handle errors
     * @param int $courseid
     * @param object $category
     * @param object $user
     */
    protected static function write_aggregated_category(int $courseid, object $category, object $user) {
        global $DB;

        // Aggregation function returns null in error condition but write_grade expects a float.
        if (is_null($category->grade)) {
            $grade = 0.0;
            if (!$category->error) {
                throw new \moodle_exception('No error text when grade=null');
            }
            $iserror = true;
            $displaygrade = $category->error;
        } else {
            $iserror = false;
            $displaygrade = $category->grade; // TODO ?
            $grade = $category->grade;
        }

        // Does this category grade already exist?
        // Give up, if not.
        if ($DB->record_exists('local_gugrades_grade', [
            'gradetype' => 'CATEGORY',
            'gradeitemid' => $category->itemid,
            'userid' => $user->id,
            'rawgrade' => $grade,
            'iserror' => $iserror,
            'iscurrent' => 1,
        ])) {
            return;
        }

        \local_gugrades\grades::write_grade(
            courseid:       $courseid,
            gradeitemid:    $category->itemid,
            userid:         $user->id,
            admingrade:     '',
            rawgrade:       $grade,
            convertedgrade: $grade, // TODO?
            displaygrade:   $displaygrade, // TODO?
            weightedgrade:  0,
            gradetype:      'CATEGORY',
            other:          '',
            iscurrent:      true,
            iserror:        $iserror,
            auditcomment:   '',  // TODO?
            ispoints:       !$category->isscale,
        );
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
     * @param int $level
     * @return array [total, completion, error]
     */
    protected static function aggregate_user(
        int $courseid,
        object $category,
        object $user,
        array &$allitems,
        int $level
        ) {

        // Information about the category is in the param
        // The field 'children' holds all the sub-items and sub-categories that
        // we need to 'add up'.
        // Get array of data to aggregate for this 'level' and then send off to
        // the aggregation function
        $children = $category->children;
        $items = [];
        foreach ($children as $child) {

            // If this is itself a grade category then we need to recurse to get the aggregated total
            // of this category (and any error). Call with the 'child' segment of the category tree.
            if ($child->iscategory) {
                [$childcategorytotal, $completion, $error] = self::aggregate_user(
                    $courseid, $child, $user, $allitems, $level + 1
                );
                $item = (object)[
                    'itemid' => $child->itemid,
                    'categoryid' => $child->categoryid,
                    'iscategory' => true,
                    'isscale' => $child->isscale,
                    'grademissing' => $childcategorytotal == null,
                    'grade' => $childcategorytotal,
                    'admingrade' => '',
                    'grademax' => $child->grademax,
                    'weight' => $child->weight,
                    'error' => $error,
                ];

                // When we aggregate a category, potentially write into gugrades_grades so it
                // can be displayed.
                self::write_aggregated_category($courseid, $item, $user);
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
                        'admingrade' => $provisional->admingrade,
                        'grademax' => $child->grademax,
                        'weight' => $child->weight,
                        'displaygrade' => $provisional->displaygrade,
                        'isscale' => $child->isscale,
                    ];
                } else {
                    $item = (object)[
                        'itemid' => $child->itemid,
                        'iscategory' => false,
                        'grademissing' => true,
                        'weight' => $child->weight,
                    ];
                }
            }
            $items[$child->itemid] = $item;
            $allitems[$child->itemid] = $item;
        }

        // List of items should hold list for this gradecategory only, ready
        // to aggregate.
        [$total, $completion, $error] = self::aggregate_user_category($courseid, $category, $items, $level);

        return [$total, $completion, $error];
    }

    /**
     * Entry point for calculating aggregations
     * Returns completion
     * @param int $courseid
     * @param int $gradecategoryid
     * @param array $users
     * @return array
     */
    public static function aggregate(int $courseid, int $gradecategoryid, array $users) {
        global $DB;

        // First get category tree structure, including all required
        // weighting drop high/low and so on. So we only have to do it once.
        $toplevel = self::recurse_tree($courseid, $gradecategoryid);
        //var_dump($toplevel); die;

        // Run through each user and aggregate their grades.
        foreach ($users as $user) {
            $userallitems = [];

            // 1 = level 1 (we need to know what level we're at). Level is incremented
            // as call recurses.
            [$usertotal, $completion, $error] = self::aggregate_user($courseid, $toplevel, $user, $userallitems, 1);
            $user->total = $usertotal;
            $user->completed = $completion;
            $user->error = $error;
        }

        return $users;
    }

}
