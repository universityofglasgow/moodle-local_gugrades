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

/**
 * Group functions used to manipulate user-related data
 */
class users {

    /**
     * Get course module from grade item
     * @param int $itemid Grade item ID
     * @param int $courseid
     * @return object
     */
    public static function get_cm_from_grade_item(int $itemid, int $courseid) {
        global $DB;

        $item = $DB->get_record('grade_items', ['id' => $itemid], '*', MUST_EXIST);

        // Get course module.
        $cm = get_coursemodule_from_instance($item->itemmodule, $item->iteminstance, $courseid, false, MUST_EXIST);
        $modinfo = get_fast_modinfo($courseid);
        return $modinfo->get_cm($cm->id);
    }

    /**
     * Factory to get correct class for assignment type
     * These are found in local_gugrades/classes/activities
     * Pick manual for manual grades, xxx_activity for activity xxx (if exists) or default_activity
     * for everything else
     * @param int $gradeitemid
     * @param int $courseid
     * @param int $groupid
     * @return object
     */
    public static function activity_factory(int $gradeitemid, int $courseid, int $groupid = 0) {
        global $DB;

        $item = $DB->get_record('grade_items', ['id' => $gradeitemid], '*', MUST_EXIST);
        $module = $item->itemmodule;
        if ($item->itemtype == 'manual') {
            return new \local_gugrades\activities\manual($gradeitemid, $courseid, $groupid);
        } else {
            $classname = '\\local_gugrades\\activities\\' . $module . '_activity';
            if (class_exists($classname)) {
                return new $classname($gradeitemid, $courseid, $groupid);
            } else {
                return new \local_gugrades\activities\default_activity($gradeitemid, $courseid, $groupid);
            }
        }
    }

    /**
     * Get users who can "be graded". Usually students.
     * @param \context $context
     * @param string $firstname (first letter of first name)
     * @param string $lastname (first letter of last name)
     * @param int $groupid (0 means ignore groups)
     * @return array
     */
    public static function get_gradeable_users(\context $context, $firstname = '', $lastname = '', $groupid = 0) {
        $fields = 'u.id, u.username, u.idnumber, u.firstname, u.lastname, u.email,
            u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.picture, u.imagealt';
        $users = get_enrolled_users($context, 'moodle/grade:view', $groupid, $fields);

        // Filter.
        if ($firstname || $lastname) {
            $users = array_filter($users, function($user) use ($firstname, $lastname) {
                if ($firstname && (strcasecmp(substr($user->firstname, 0, 1), $firstname))) {
                    return false;
                }
                if ($lastname && (strcasecmp(substr($user->lastname, 0, 1), $lastname))) {
                    return false;
                }
                return true;
            });
        }

        return $users;
    }

    /**
     * Get available users for given activity
     * @param object $cmi (cm_info)
     * @param \context $context
     * @param string $firstname (first letter of first name)
     * @param string $lastname (first letter of last name)
     * @param int $groupid
     * @return array
     */
    public static function get_available_users_from_cm($cmi, $context, $firstname, $lastname, $groupid) {

        // See https://moodledev.io/docs/apis/subsystems/availability.
        $info = new \core_availability\info_module($cmi);

        // Get all the possible users in this course.
        $users = self::get_gradeable_users($context, $firstname, $lastname, $groupid);

        // Filter using availability API.
        $filteredusers = $info->filter_user_list($users);

        return array_values($filteredusers);
    }

    /**
     * Add pictures to user records
     * @param array $users
     * @return array
     */
    public static function add_pictures_to_user_records(array $users) {
        global $PAGE;

        foreach ($users as $user) {
            $userpicture = new \user_picture($user);
            $user->pictureurl = $userpicture->get_url($PAGE)->out(false);
        }

        return $users;
    }

    /**
     * Add gradehidden flag to user records
     * @param array $users
     * @param int $gradeitemid
     * @return array
     */
    public static function add_gradehidden_to_user_records(array $users, int $gradeitemid) {
        global $DB;

        foreach ($users as $user) {
            $user->gradehidden = $DB->record_exists('local_gugrades_hidden',
                ['gradeitemid' => $gradeitemid, 'userid' => $user->id]);
        }

        return $users;
    }
}
