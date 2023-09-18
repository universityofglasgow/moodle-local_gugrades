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

class api {

    /**
     * Get activities
     * @param int $courseid
     * @param int $categoryid
     * @return object List of activities/subcategories in
     */
    public static function get_activities(int $courseid, int $categoryid) {
        $tree = \local_gugrades\grades::get_activitytree($courseid, $categoryid);

        return $tree;
    }

    /**
     * Get capture page
     * @param int $courseid
     * @param int $gradeitemid
     * @param string $firstname (first letter of)
     * @param string $lastname (last letter of)
     * @return array
     */
    public static function get_capture_page(int $courseid, int $gradeitemid, string $firstname, string $lastname) {

        // Sanity checks for selected grade item
        if (!\local_gugrades\grades::is_grade_supported($gradeitemid)) {
            return [
                'users' => json_encode([]),
                'hidden' => false,
                'itemtype' => '',
                'itemname' => '',
                'gradesupported' => false,
            ];
        }

        // Instantiate object for this activity type
        $activity = \local_gugrades\users::activity_factory($gradeitemid, $courseid);
        $activity->set_name_filter($firstname, $lastname);

        // Get list of users.
        // Will be everybody for 'manual' grades or filtered list for modules.
        $users = $activity->get_users();
        $users = \local_gugrades\grades::add_grades_to_user_records($courseid, $gradeitemid, $users);
        $users = \local_gugrades\users::add_pictures_to_user_records($users);
        $columns = \local_gugrades\grades::get_grade_capture_columns($courseid, $gradeitemid);

        return [
            'users' => $users,
            'columns' => $columns,
            'hidden' => $activity->is_names_hidden(),
            'itemtype' => $activity->get_itemtype(),
            'itemname' => $activity->get_itemname(),
            'gradesupported' => true,
        ];
    }

    /**
     * Get grade item
     * @param int $itemid
     * @return array
     */
    public static function get_grade_item(int $itemid) {
        global $DB;

        // Get item (if it exists)
        $item = $DB->get_record('grade_items', ['id' => $itemid], '*', MUST_EXIST);

        return [
            'id' => $item->id,
            'courseid' => $item->courseid,
            'categoryid' => $item->categoryid,
            'itemname' => $item->itemname,
            'itemtype' => $item->itemtype,
            'itemmodule' => $item->itemmodule,
            'iteminstance' => $item->iteminstance,            
        ];
    }

    /**
     * get_levelonecategories
     * @param int $courseid
     * @return array
     */
    public static function get_levelonecategories(int $courseid) {
        $results = [];
        $categories = \local_gugrades\grades::get_firstlevel($courseid);
        foreach ($categories as $category) {
            $results[] = [
                'id' => $category->id,
                'fullname' => $category->fullname,
            ];
        }

        return $results;
    }

    /**
     * Import grade
     * @param int $courseid
     * @param int $gradeitemid
     * @param \local_gugrades\conversion\base $conversion
     * @param int $userid
     */
    public static function import_grade(int $courseid, int $gradeitemid, \local_gugrades\conversion\base $conversion, int $userid) {

        // Instantiate object for this activity type
        $activity = \local_gugrades\users::activity_factory($gradeitemid, $courseid);

        // Ask activity for grade
        $rawgrade = $activity->get_first_grade($userid);

        // Ask conversion object for converted grade and display grade
        if (($rawgrade !== false) && $conversion->validate($rawgrade)) {
            
            list($convertedgrade, $displaygrade) = $conversion->import($rawgrade);

            \local_gugrades\grades::write_grade(
                $courseid,
                $gradeitemid,
                $userid,
                '',
                $rawgrade,
                $convertedgrade,
                $displaygrade,
                0,
                'FIRST',
                '',
                1,
                get_string('import', 'local_gugrades')
            );
        }
    }

    /**
     * Get user picture url
     * @param int $userid
     * @return \moodle_url
     */
    public static function get_user_picture_url(int $userid) {
        global $DB, $PAGE;

        $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
        $user_picture = new \user_picture($user);

        return $user_picture->get_url($PAGE);
    }

    /**
     * Get user grades
     * Get site-wide grades for dashboard / Glasgow life / testing / etc. 
     * @param int $userid
     * @return array
     */
    public static function get_user_grades(int $userid) {
        global $DB;

        // Load *current* grades for this user
        if (!$grades = $DB->get_records('local_gugrades_grade', ['userid' => $userid, 'iscurrent' => 1])) {
            return [];
        }

        // "cache" course objects so we don't keep looking them up
        $courses = [];

        // Iterate over grades adding additional information
        $newgrades = [];
        foreach ($grades as $grade) {
            $courseid = $grade->courseid;

            // Find course or just skip if it doesn't exist (deleted?)
            if (array_key_exists($courseid, $courses)) {
                $course = $courses[$courseid];
            } else {
                if (!$course = $DB->get_record('course', ['id' => $courseid])) {
                    continue;
                }
                $courses[$courseid] = $course;
            }

            // Add course data
            $grade->coursefullname = $course->fullname;
            $grade->courseshortname = $course->shortname;

            // Additional grade data
            $gradetype = $DB->get_record('local_gugrades_gradetype', ['id' => $grade->reason], '*', MUST_EXIST);
            $grade->reasonname = $gradetype->fullname;

            // Item into
            $grade->itemname = grades::get_item_name_from_itemid($grade->gradeitemid);

            $newgrades[] = $grade;
        }

        return $newgrades;
    }

    /**
     * Get grade history for given user / grade item
     * @param int $gradeitemid
     * @param int $userid
     * @return array
     */
    public static function get_history(int $gradeitemid, int $userid) {
        global $DB;

        $sql = "SELECT gg.*, gc.other FROM {local_gugrades_grade} gg
            JOIN {local_gugrades_column} gc ON gc.id = gg.columnid
            WHERE gg.userid = :userid
            AND gg.gradeitemid = :gradeitemid
            ORDER BY audittimecreated DESC";
        if (!$grades = $DB->get_records_sql($sql, ['userid' => $userid, 'gradeitemid' => $gradeitemid])) {
            return [];
        }

        // Additional info
        $newgrades = [];
        foreach ($grades as $grade) {
            $grade->description = gradetype::get_description($grade->gradetype);
            $grade->time = userdate($grade->audittimecreated);
            $grade->current = $grade->iscurrent ? get_string('yes') : get_string('no');
            if ($audituser = $DB->get_record('user', ['id' => $grade->auditby])) {
                $grade->auditbyname = fullname($audituser);
            } else {
                $grade->auditbyname = '-';
            }

            if ($grade->gradetype == 'OTHER') {
                $grade->description .= ' (' . $grade->other . ')';
            }

            $newgrades[] = $grade;
        }

        return $newgrades;
    }

    /**
     * Get audit history
     * @param int $courseid
     * @return array
     */
    public static function get_audit(int $courseid) {
        global $USER, $DB;

        $context = \context_course::instance($courseid);
        if (has_capability('local/gugrades:readotheraudit', $context)) {
            $audit = \local_gugrades\audit::read($courseid);
        } else {
            $audit = \local_gugrades\audit::read($courseid, $USER->id);
        }

        return $audit;
    }

    /**
     * Has anything been defined for gradeitemid
     * @param int $courseid
     * @param int $gradeitemid
     * @return boolean
     */
    public static function is_grades_imported(int $courseid, int $gradeitemid) {
        return \local_gugrades\grades::is_grades_imported($courseid, $gradeitemid);
    }

    /** 
     * Get all the strings for this plugin as array of objects
     * @return array
     */
    public static function get_all_strings() {
        $stringmanager = get_string_manager();
        $lang = current_language();
        $cstrings = $stringmanager->load_component_strings('local_gugrades', $lang);

        $strings = [];
        foreach ($cstrings as $tag => $stringvalue) {
            $strings[] = [
                'tag' => $tag,
                'stringvalue' => $stringvalue,
            ];
        }

        return $strings;
    }

    /**
     * Convert to array to FormKit menu
     * @param array $inputarray
     * @param bool $reverse
     * @return array (of objects)
     */
    private static function formkit_menu(array $inputarray, bool $reverse = false) {
        $menu = array_map(function($key, $value) {
            $item = new \stdClass;
            $item->value = $key;
            $item->label = $value;
            return $item;
        }, array_keys($inputarray), array_values($inputarray));

        if ($reverse) {
            $menu = array_reverse($menu, true);
        }

        return $menu;
    }

    /**
     * Get add grade form
     * Various 'stuff' to construct the form
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $userid
     * @return array
     */
    public static function get_add_grade_form(int $courseid, int $gradeitemid, int $userid) {
        global $DB;

        // Get gradetype
        $gradetypes = \local_gugrades\gradetype::get_menu();
        $wsgradetypes = self::formkit_menu($gradetypes);

        // Username
        $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

        // Gradeitem
        list($itemtype, $gradeitem) = \local_gugrades\grades::analyse_gradeitem($gradeitemid);
        $grademax = ($gradeitem->gradetype == GRADE_TYPE_VALUE) ? $gradeitem->grademax : 0;

        // scale
        if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
            $scale = \local_gugrades\grades::get_scale($gradeitem->scaleid);
            $scalemenu = self::formkit_menu($scale, true);
        } else {
            $scalemenu = [];
        }

        // Administrative grades
        $admingrades = \local_gugrades\admin_grades::get_menu();
        $adminmenu = self::formkit_menu($admingrades, true);

        //
        return [
            'gradetypes' => $wsgradetypes,
            'rawgradetypes' => $gradetypes,
            'itemname' => $gradeitem->itemname,
            'fullname' => fullname($user),
            'idnumber' => $user->idnumber,
            'usescale' => ($itemtype == 'scale') || ($itemtype == 'scale22'),
            'grademax' => $grademax,
            'scalemenu' => $scalemenu,
            'adminmenu' => $adminmenu,
        ];
    }

    /**
     * Write additional grade
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $userid
     * @param string $reason
     * @param string $other
     * @param string $admingrade
     * @param int $scale
     * @param float $grade
     * @param string $notes
     */
    public static function write_additional_grade(int $courseid, int $gradeitemid, int $userid, string $reason, string $other, string $admingrade, int $scale, float $grade, string $notes) {

        // Conversion class
        $conversion = \local_gugrades\grades::conversion_factory($courseid, $gradeitemid);

        // Get the stuff we used to build the form for validation
        $form = self::get_add_grade_form($courseid, $gradeitemid, $userid);

        // Check 'reason' is valid
        $gradetypes = $form['rawgradetypes'];
        if (!array_key_exists($reason, $gradetypes)) {
            throw new \moodle_exception('Attempting to write invalid reason - "' . $reason . '"');
        }

        // Check 'other' is valid
        if ($other && ($reason != 'OTHER')) {
            throw new \moodle_exception('Attemting to write invalid other text when reason is not other');
        }
        if (!$other && ($reason == 'OTHER')) {
            throw new \moodle_exception('Attempting to write empty other text when reason is other');
        }

        // Check 'scale' is valid
        $usescale = $form['usescale'];
        if (!$usescale && ($scale != 0)) {
            throw new \moodle_exception('Attempting to write scale value when item is not a scale');
        }

        // Check if 'grade' is valid
        if ($usescale && ($grade != 0)) {
            throw new \moodle_exception('Attempting to write non-zero grade when item type is a scale');
        }

        // Get converted and display grade
        if (!empty($admingrade)) {
            $rawgrade = 0;
            $convertedgrade = 0.0;
            $displaygrade = $admingrade;
        } else if ($usescale) {

            // TODO: Check! +1 because internal values are 1 - based, our form is 0 - based
            list($convertedgrade, $displaygrade) = $conversion->import($scale + 1);
            $rawgrade = $scale + 1;
        } else {
            list($convertedgrade, $displaygrade) = $conversion->import($grade);
            $rawgrade = $grade;
        }

        // Happy as we're going to get, so write the new data
        \local_gugrades\grades::write_grade(
            $courseid,
            $gradeitemid,
            $userid,
            $admingrade,
            $rawgrade,
            $convertedgrade,
            $displaygrade,
            0,
            $reason,
            $other,
            true,
            $notes
        );
    }

    /**
     * Save settings
     * Options in tool UI (not sitewide settings)
     * @param int $courseid
     * @param int $gradeitemid (0 if you don't need it)
     * @param array $settings
     */
    public static function save_settings(int $courseid, int $gradeitemid, array $settings) {
        global $DB;

        foreach ($settings as $setting) {
            $config = $DB->get_record('local_gugrades_config', [
                'courseid' => $courseid,
                'gradeitemid' => $gradeitemid,
                'name' => $setting['name'],
            ]);
            if ($config) {
                $config->value = $setting->value;
                $DB->update_record('local_gugrades_config', $config);
            } else {
                $config = new \stdClass;
                $config->courseid = $courseid;
                $config->gradeitemid = $gradeitemid;
                $config->name = $setting['name'];
                $config->value = $setting['value'];
                $DB->insert_record('local_gugrades_config', $config);
            }
        }
    }

    /**
     * Get settings
     * @param int $courseid
     * @param int $gradeitemid (probably 0)
     * @return array
     */
    public static function get_settings(int $courseid, int $gradeitemid) {
        global $DB;

        $configs = $DB->get_records('local_gugrades_config', ['courseid' => $courseid, 'gradeitemid' => $gradeitemid]);
        $settings = [];
        foreach ($configs as $config) {
            $settings[] = [
                'name' => $config->name,
                'value' => $config->value,
            ];
        }

        return $settings;
    }
}