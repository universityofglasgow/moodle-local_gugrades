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

        $sql = "SELECT *, gi.id AS gradeitemid FROM {grade_categories} gc
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
                'gradeitemid' => $gradecategory->gradeitemid,
                'categoryid' => $gradecategory->id,
                'shortname' => $gradecategory->shortname,
                'fullname' => $gradecategory->fullname
            ];
        }
        foreach ($gradeitems as $gradeitem) {
            $columns[] = (object)[
                'gradeitemid' => $gradeitem->gradeitemid,
                'categoryid' => 0,
                'shortname' => $gradeitem->shortname,
                'fullname' => $gradeitem->itemname,
            ];
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

}