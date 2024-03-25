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

                // TODO - may not be so simple
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

                // TODO - may not be so simple
                'weight' => round($gradeitem->weight * 100),
            ];
        }

        // Add gradetypes and maximum points to columns
        foreach ($columns as $column) {
            $conversion = \local_gugrades\grades::conversion_factory($courseid, $column->gradeitemid);
            $column->gradetype = $conversion->name();
            $column->grademax = $conversion->get_maximum_grade();
            $column->isscale = $conversion->is_scale();
        }

        return [$gradecategories, $gradeitems, $columns];
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

        // Displayname.
        foreach ($users as $user) {
            $user->displayname = fullname($user);
        }

        // Pictures.
        $users = \local_gugrades\users::add_pictures_to_user_records($users);

        return array_values($users);
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

                // Field identifier based on gradeitemid (which is unique even for categories)
                $fieldname = 'AGG_' . $column->gradeitemid;
                $data = [
                    'fieldname' => $fieldname,
                    'display' => 'No data'
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

}