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

require_once(dirname(__FILE__) . '/constants.php');

/**
 * Actual implementation of all the external functions
 */
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
     * @param int $groupid
     * @param bool $viewfullnames
     * @return array
     */
    public static function get_capture_page(int $courseid, int $gradeitemid,
        string $firstname, string $lastname, int $groupid, bool $viewfullnames) {

        // Sanity checks for selected grade item.
        if (!\local_gugrades\grades::is_grade_supported($gradeitemid)) {
            return [
                'users' => [],
                'columns' => [],
                'hidden' => false,
                'itemtype' => '',
                'itemname' => '',
                'gradesupported' => false,
                'gradesimported' => false,
                'gradehidden' => false,
                'gradelocked' => false,
                'showconversion' => false,
                'converted' => false,
                'released' => false,
            ];
        }

        // Hidden or locked in gradebook?
        [$gradehidden, $gradelocked] = \local_gugrades\grades::is_grade_hidden_locked($gradeitemid);

        // Instantiate object for this activity type.
        $activity = \local_gugrades\users::activity_factory($gradeitemid, $courseid, $groupid);
        $activity->set_name_filter($firstname, $lastname);
        $activity->set_viewfullnames($viewfullnames);

        // Should the conversion button be shown.
        $showconversion = \local_gugrades\grades::showconversion($gradeitemid);

        // Get list of users.
        // Will be everybody for 'manual' grades or filtered list for modules.
        $users = $activity->get_users();
        $users = \local_gugrades\grades::add_grades_to_user_records($courseid, $gradeitemid, $users);
        $users = \local_gugrades\users::add_pictures_to_user_records($users);
        $users = \local_gugrades\users::add_gradehidden_to_user_records($users, $gradeitemid);
        $columns = \local_gugrades\grades::get_grade_capture_columns($courseid, $gradeitemid);
        $gradesimported = \local_gugrades\grades::is_grades_imported($courseid, $gradeitemid);
        $converted = \local_gugrades\conversion::is_conversion_applied($courseid, $gradeitemid);
        $released = \local_gugrades\grades::is_grades_released($courseid, $gradeitemid);

        return [
            'users' => $users,
            'columns' => $columns,
            'hidden' => $activity->is_names_hidden() || self::is_gradeitem_hidden($gradeitemid),
            'itemtype' => $activity->get_itemtype(),
            'itemname' => $activity->get_itemname(),
            'gradesupported' => true,
            'gradesimported' => $gradesimported,
            'gradehidden' => $gradehidden ? true : false,
            'gradelocked' => $gradelocked ? true : false,
            'showconversion' => $showconversion && $gradesimported,
            'converted' => $converted,
            'released' => $released,
        ];
    }

    /**
     * Unpack a (string) CSV file
     * @param string $csv
     * @return array
     */
    public static function unpack_csv($csv) {

        // First split into lines.
        $lines = explode(PHP_EOL, $csv);

        $data = [];
        foreach ($lines as $line) {
            if (trim($line)) {
                $items = array_map('trim', explode(',', $line));
                $data[] = $items;
            }
        }

        return $data;
    }

    /**
     * Get CSV download
     * Contents of pro-forma CSV file
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $groupid
     * @return string
     */
    public static function get_csv_download(int $courseid, int $gradeitemid, int $groupid) {

        // Get activity object.
        $activity = \local_gugrades\users::activity_factory($gradeitemid, $courseid, $groupid);

        // Get array of users.
        $users = $activity->get_users();

        // Build CSV file.
        $csv = '';
        $csv .= get_string('name', 'local_gugrades') . ',' . get_string('idnumber', 'local_gugrades') . ',' .
            get_string('grade', 'local_gugrades') . PHP_EOL;
        foreach ($users as $user) {
            $csv .= $user->displayname . ',' . $user->idnumber . ',' . PHP_EOL;
        }

        return $csv;
    }

    /**
     * CSV upload
     * Upload the data or (optionaly) just do a check
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $groupid
     * @param bool $testrun
     * @param string $reason
     * @param string $other
     * @param string $csv
     * @return array [$testrunlines, $errorcount, $addcount]
     */
    public static function csv_upload(int $courseid, int $gradeitemid, int $groupid,
        bool $testrun, string $reason, string $other, string $csv) {

        // Turn csv into an array - and ditch first line.
        $lines = self::unpack_csv($csv);
        array_shift($lines);

        // Get the possible users for this grade item. And re-key by idnumber.
        $activity = \local_gugrades\users::activity_factory($gradeitemid, $courseid, $groupid);
        $users = $activity->get_users();
        $idusers = [];
        foreach ($users as $user) {
            if (!empty($user->idnumber)) {
                $idusers[$user->idnumber] = $user;
            }
        }

        // Get the grade conversion class.
        $conversion = \local_gugrades\grades::conversion_factory($courseid, $gradeitemid);

        // Only for testrun, accumulate output.
        $testrunlines = [];

        // Count success and misery (not all errors are fatal).
        $addcount = 0;
        $errorcount = 0;

        // Iterate over CSV lines, checking and (optionally) adding new grade.
        foreach ($lines as $line) {

            // Need prefilled to keep web service return check happy.
            $testrunline = [
                'name' => '',
                'idnumber' => '',
                'grade' => '',
                'gradevalue' => 0.0,
                'state' => 0,
                'error' => '',
            ];

            // We just need the idnumber, so must have at least two entries.
            if (count($line) < 2) {
                $testrunline['error'] = get_string('csvtoofewitems', 'local_gugrades');
                $testrunline['state'] = -1;
                $testrunlines[] = $testrunline;
                $errorcount++;
                continue;
            }

            // Get the data.
            $username = $line[0];
            $testrunline['name'] = $username;
            $idnumber = $line[1];
            $testrunline['idnumber'] = $idnumber;
            $grade = isset($line[2]) ? $grade = $line[2] : '';
            $testrunline['grade'] = $grade;

            // Check we have a (valid) idnumber.
            if (!isset($idusers[$idnumber])) {
                $testrunline['error'] = get_string('csvidinvalid', 'local_gugrades');
                $testrunline['state'] = -1;
                $testrunlines[] = $testrunline;
                $errorcount++;
                continue;
            }
            $user = $idusers[$idnumber];

            // Check if valid grade.
            if ($grade) {
                list($gradevalid, $gradevalue) = $conversion->csv_value($grade);
                if (!$gradevalid) {
                    $testrunline['error'] = get_string('csvgradeinvalid', 'local_gugrades');
                    $testrunlines[] = $testrunline;
                    $errorcount++;
                    continue;
                }
                $testrunline['gradevalue'] = $gradevalue;
                $testrunline['state'] = 1;
            } else {
                $testrunline['error'] = get_string('csvnograde', 'local_gugrades'); // Warning.
                $testrunlines[] = $testrunline;
                continue;
            }

            $testrunlines[] = $testrunline;

            // If we get to here and not a testrun, we can actually save the data.
            if (!$testrun) {
                \local_gugrades\grades::write_grade(
                    courseid:       $courseid,
                    gradeitemid:    $gradeitemid,
                    userid:         $user->id,
                    admingrade:     '',
                    rawgrade:       $gradevalue,
                    convertedgrade: $gradevalue,
                    displaygrade:   $grade,
                    weightedgrade:  0.0,
                    gradetype:      $reason,
                    other:          $other,
                    iscurrent:      true,
                    iserror:      false,
                    auditcomment:   'CSV import',
                    ispoints:       !$conversion->is_scale(),
                );
                $addcount++;
            }
        }

        return [$testrunlines, $errorcount, $addcount];
    }

    /**
     * Get grade item
     * @param int $itemid
     * @return array
     */
    public static function get_grade_item(int $itemid) {
        global $DB;

        // Get item.
        $item = $DB->get_record('grade_items', ['id' => $itemid], '*', MUST_EXIST);

        // If the type is a category, get that as well.
        if ($item->itemtype == 'category') {
            $category = $DB->get_record('grade_categories', ['id' => $item->iteminstance], '*', MUST_EXIST);
            $itemname = $category->fullname;
        } else {
            $itemname = $item->itemname;
        }

        // Get the scale name.
        if ($item->scaleid) {
            $scale = $DB->get_record('scale', ['id' => $item->scaleid], '*', MUST_EXIST);
            $scalename = $scale->name;
        } else {
            $scalename = '';
        }

        return [
            'id' => $item->id,
            'courseid' => $item->courseid,
            'categoryid' => $item->categoryid,
            'itemname' => $itemname,
            'itemtype' => get_string($item->itemtype, 'local_gugrades'),
            'itemmodule' => $item->itemmodule,
            'iteminstance' => $item->iteminstance,
            'isscale' => !empty($item->scaleid),
            'scalename' => $scalename,
            'grademax' => $item->grademax,
            'weight' => round($item->aggregationcoef * 100),
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
     * @param \local_gugrades\activities\base $activity
     * @param int $userid
     * @param bool $additional
     * @param bool $fillns
     * @return bool - was a grade imported
     */
    public static function import_grade(
        int $courseid,
        int $gradeitemid,
        \local_gugrades\conversion\base $conversion,
        \local_gugrades\activities\base $activity,
        int $userid,
        bool $additional,
        bool $fillns) {

        // If additional selected then skip users who already have data.
        if ($additional && \local_gugrades\grades::user_has_grades($gradeitemid, $userid)) {
            return false;
        }

        // Ask activity for grade.
        $rawgrade = $activity->get_first_grade($userid);

        // Ask conversion object for converted grade and display grade.
        if ($rawgrade !== false) {

            // Can (sometimes) come back as string, for some reason.
            $rawgrade = floatval($rawgrade);

            if ($conversion->validate($rawgrade)) {
                [$convertedgrade, $displaygrade] = $conversion->import($rawgrade);

                // TODO: Is rawgrade correct? For scheduleB this will be completely
                // unrelated. E.g. rawgrade 6 = converted grade = 14.
                \local_gugrades\grades::write_grade(
                    courseid:       $courseid,
                    gradeitemid:    $gradeitemid,
                    userid:         $userid,
                    admingrade:     '',
                    rawgrade:       $rawgrade,
                    convertedgrade: $convertedgrade,
                    displaygrade:   $displaygrade,
                    weightedgrade:  0,
                    gradetype:      'FIRST',
                    other:          '',
                    iscurrent:      true,
                    iserror:        false,
                    auditcomment:   get_string('import', 'local_gugrades'),
                    ispoints:       !$conversion->is_scale(),
                );

                return true;
            }
        } else if ($fillns) {

            // If there's no grade and fillns is enabled, write
            // an NS grade, instead.

            \local_gugrades\grades::write_grade(
                courseid:       $courseid,
                gradeitemid:    $gradeitemid,
                userid:         $userid,
                admingrade:     'NS',
                rawgrade:       0,
                convertedgrade: 0,
                displaygrade:   'NS',
                weightedgrade:  0,
                gradetype:      'FIRST',
                other:          '',
                iscurrent:      true,
                iserror:        false,
                auditcomment:   get_string('import', 'local_gugrades'),
                ispoints:       false,
            );

            return true;
        }

        return false;
    }

    /**
     * Get user picture url
     * @param int $userid
     * @return \moodle_url
     */
    public static function get_user_picture_url(int $userid) {
        global $DB, $PAGE;

        $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
        $userpicture = new \user_picture($user);

        return $userpicture->get_url($PAGE);
    }

    /**
     * Get user grades
     * Get site-wide grades for dashboard / Glasgow life / testing / etc.
     * @param int $userid
     * @return array
     */
    public static function get_user_grades(int $userid) {
        global $DB;

        // Load *current* grades for this user.
        if (!$grades = $DB->get_records('local_gugrades_grade', ['userid' => $userid, 'iscurrent' => 1])) {
            return [];
        }

        // We "cache" course objects so we don't keep looking them up.
        $courses = [];

        // Iterate over grades adding additional information.
        $newgrades = [];
        foreach ($grades as $grade) {
            $courseid = $grade->courseid;

            // Find course or just skip if it doesn't exist (deleted?).
            if (array_key_exists($courseid, $courses)) {
                $course = $courses[$courseid];
            } else {
                if (!$course = $DB->get_record('course', ['id' => $courseid])) {
                    continue;
                }
                $courses[$courseid] = $course;
            }

            // Add course data.
            $grade->coursefullname = $course->fullname;
            $grade->courseshortname = $course->shortname;

            // Additional grade data.
            $gradetype = $DB->get_record('local_gugrades_gradetype', ['id' => $grade->reason], '*', MUST_EXIST);
            $grade->reasonname = $gradetype->fullname;

            // Item into.
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

        // Additional info.
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

        $context = \context_course::instance($courseid, true);
        if (has_capability('local/gugrades:readotheraudit', $context)) {
            $audit = \local_gugrades\audit::read($courseid);
        } else {
            $audit = \local_gugrades\audit::read($courseid, $USER->id);
        }

        return $audit;
    }

    /**
     * Has anything been defined for gradeitemid?
     * Are the grades going to "match" for a recursive import?
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $groupid
     * @return array
     */
    public static function is_grades_imported(int $courseid, int $gradeitemid, $groupid) {
        $imported = \local_gugrades\grades::is_grades_imported($courseid, $gradeitemid, $groupid);
        list($recursiveavailable, $recursivematch, $allgradesvalid) = \local_gugrades\grades::recursive_import_match($gradeitemid);

        return [
            'imported' => $imported,
            'recursiveavailable' => $recursiveavailable,
            'recursivematch' => $recursivematch,
            'allgradesvalid' => $allgradesvalid,
        ];
    }

    /**
     * Import grades recursively. A basic import for all peers and children
     * of the supplied gradeitemid.
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $groupid
     * @param bool $additional
     * @param bool $fillns
     * @return array [itemcount, gradecount]
     */
    public static function import_grades_recursive(int $courseid, int $gradeitemid, int $groupid, bool $additional, bool $fillns) {
        global $DB;

        // Check!
        list($recursiveavailable, $recursivematch, $allgradesvalid) = \local_gugrades\grades::recursive_import_match($gradeitemid);
        if (!$recursiveavailable) {
            throw new \moodle_exception("import_grades_recursive called for <2nd level grade item. ID = " . $gradeitemid);
        }

        // Get parent grade category.
        $gradeitem = $DB->get_record('grade_items', ['id' => $gradeitemid], '*', MUST_EXIST);
        $categoryid = $gradeitem->categoryid;
        $gradecategory = $DB->get_record('grade_categories', ['id' => $categoryid], '*', MUST_EXIST);

        // Get a list of all the grade items under the above.
        $items = \local_gugrades\grades::get_gradeitems_recursive($gradecategory);
        $itemcount = count($items);
        $gradecount = 0;

        foreach ($items as $item) {
            $activity = \local_gugrades\users::activity_factory($item->id, $courseid, $groupid);
            $conversion = \local_gugrades\grades::conversion_factory($courseid, $item->id);

            // Get all the permitted users in this activity.
            $users = $activity->get_users();

            // Iterate over these users importing grade.
            foreach ($users as $user) {
                if (self::import_grade($courseid, $item->id, $conversion, $activity, $user->id, $additional, $fillns)) {
                    $gradecount++;
                }
            }
        }

        return [$itemcount, $gradecount];
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
     * Convert array to FormKit menu
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
     * Get capture cell form
     * Various 'stuff' to construct the editable grade cells
     * @param int $courseid
     * @param int $gradeitemid
     * @return array
     */
    public static function get_capture_cell_form(int $courseid, int $gradeitemid) {
        global $DB;

        $converted = \local_gugrades\conversion::is_conversion_applied($courseid, $gradeitemid);

        // Gradeitem.
        list($itemtype, $gradeitem) = \local_gugrades\grades::analyse_gradeitem($gradeitemid);
        if ($gradeitem == false) {
            throw new \moodle_exception('Unsupported grade item encountered in get_add_grade_form. Gradeitemid = ' . $gradeitemid);
        }
        $grademax = ($gradeitem->gradetype == GRADE_TYPE_VALUE) ? $gradeitem->grademax : 0;

        // Scale.
        if ($converted) {
            $scale = \local_gugrades\conversion::get_conversion_scale($courseid, $gradeitemid);
            $scalemenu = self::formkit_menu($scale, true);
        } else if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
            $scale = \local_gugrades\grades::get_scale($gradeitem->scaleid);
            $scalemenu = self::formkit_menu($scale, true);
        } else {
            $scalemenu = [];
        }

        // Administrative grades.
        $admingrades = \local_gugrades\admin_grades::get_menu();
        $adminmenu = self::formkit_menu($admingrades, true);

        // Is it a scale?
        if ($converted) {
            $usescale = true;
        } else {
            $usescale = ($itemtype == 'scale') || ($itemtype == 'scale22');
        }

        return [
            'usescale' => $usescale,
            'grademax' => $grademax,
            'scalemenu' => $scalemenu,
            'adminmenu' => $adminmenu,
        ];
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

        $converted = \local_gugrades\conversion::is_conversion_applied($courseid, $gradeitemid);

        // Get gradetype.
        $gradetypes = \local_gugrades\gradetype::get_menu($gradeitemid, LOCAL_GUGRADES_FORMENU);

        // If converted then we can't change existing points columns.
        if ($converted) {
            foreach ($gradetypes as $gradetype => $description) {
                if ($column = $DB->get_record('local_gugrades_column',
                    ['gradeitemid' => $gradeitemid, 'gradetype' => $gradetype])) {
                    if ($column->points) {
                        unset($gradetypes[$gradetype]);
                    }
                }
            }
        }

        $wsgradetypes = self::formkit_menu($gradetypes);

        // Username.
        $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

        // Gradeitem.
        list($itemtype, $gradeitem) = \local_gugrades\grades::analyse_gradeitem($gradeitemid);
        if ($gradeitem == false) {
            throw new \moodle_exception('Unsupported grade item encountered in get_add_grade_form. Gradeitemid = ' . $gradeitemid);
        }
        $grademax = ($gradeitem->gradetype == GRADE_TYPE_VALUE) ? $gradeitem->grademax : 0;

        // Scale.
        if ($converted) {
            $scale = \local_gugrades\conversion::get_conversion_scale($courseid, $gradeitemid);
            $scalemenu = self::formkit_menu($scale, true);
        } else if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
            $scale = \local_gugrades\grades::get_scale($gradeitem->scaleid);
            $scalemenu = self::formkit_menu($scale, true);
        } else {
            $scalemenu = [];
        }

        // Administrative grades.
        $admingrades = \local_gugrades\admin_grades::get_menu();
        $adminmenu = self::formkit_menu($admingrades, true);

        return [
            'gradetypes' => $wsgradetypes,
            'rawgradetypes' => $gradetypes,
            'itemname' => $gradeitem->itemname,
            'fullname' => fullname($user),
            'idnumber' => $user->idnumber,
            'usescale' => ($itemtype == 'scale') || ($itemtype == 'scale22') || $converted,
            'grademax' => $grademax,
            'scalemenu' => $scalemenu,
            'adminmenu' => $adminmenu,
        ];
    }

    /**
     * Get menu of gradetypes and admin grades in menu format
     * @param int $courseid
     * @param int $gradeitemid
     * @return array [$gradetypes, $admingrades]
     */
    public static function get_gradetypes(int $courseid, int $gradeitemid) {
        global $DB;

        $gradetypes = \local_gugrades\gradetype::get_menu($gradeitemid, LOCAL_GUGRADES_FORMENU);

        // If converted then we can't change existing points columns.
        $converted = \local_gugrades\conversion::is_conversion_applied($courseid, $gradeitemid);
        if ($converted) {
            foreach ($gradetypes as $gradetype => $description) {
                if ($column = $DB->get_record('local_gugrades_column',
                    ['gradeitemid' => $gradeitemid, 'gradetype' => $gradetype])) {
                    if ($column->points) {
                        unset($gradetypes[$gradetype]);
                    }
                }
            }
        }
        $wsgradetypes = self::formkit_menu($gradetypes);

        // Administrative grades.
        $admingrades = \local_gugrades\admin_grades::get_menu();
        $adminmenu = self::formkit_menu($admingrades, true);

        return [$wsgradetypes, $adminmenu];
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
    public static function write_additional_grade(
        int $courseid,
        int $gradeitemid,
        int $userid,
        string $reason,
        string $other,
        string $admingrade,
        int $scale,
        float $grade,
        string $notes
        ) {

        global $DB;

        // Conversion class.
        $conversion = \local_gugrades\grades::conversion_factory($courseid, $gradeitemid);

        // Get the stuff we used to build the form for validation.
        $form = self::get_add_grade_form($courseid, $gradeitemid, $userid);

        // Check 'reason' is valid.
        $gradetypes = $form['rawgradetypes'];
        if (!array_key_exists($reason, $gradetypes)) {
            throw new \moodle_exception('Attempting to write invalid reason - "' . $reason . '"');
        }

        // If reason looks like OTHER_XX then it's an extant other type. XX is the ID
        // in local_gugrades_column. So...
        if (str_contains($reason, 'OTHER_')) {
            $parts = explode('_', $reason);
            $reason = 'OTHER';
            $columnid = $parts[1];
            $column = $DB->get_record('local_gugrades_column', ['id' => $columnid], '*', MUST_EXIST);
            $other = $column->other;
        }

        // Check 'other' is valid.
        if ($other && ($reason != 'OTHER')) {
            throw new \moodle_exception('Attemting to write invalid other text when reason is not other');
        }
        if (!$other && ($reason == 'OTHER')) {
            throw new \moodle_exception('Attempting to write empty other text when reason is other');
        }

        // Check 'scale' is valid.
        $usescale = $form['usescale'];
        if (!$usescale && ($scale != 0)) {
            throw new \moodle_exception('Attempting to write scale value when item is not a scale');
        }

        // Check if 'grade' is valid.
        if ($usescale && ($grade != 0)) {
            throw new \moodle_exception('Attempting to write non-zero grade when item type is a scale');
        }

        // Get converted and display grade.
        if (!empty($admingrade)) {
            $rawgrade = 0;
            $convertedgrade = 0.0;
            $displaygrade = $admingrade;
        } else if ($conversion->is_conversion()) {
            list($convertedgrade, $displaygrade) = $conversion->import($scale);
            $rawgrade = $scale;
        } else if ($usescale) {

            // TODO: Check! +1 because internal values are 1 - based, our form is 0 - based.
            list($convertedgrade, $displaygrade) = $conversion->import($scale + 1);
            $rawgrade = $scale + 1;
        } else {
            list($convertedgrade, $displaygrade) = $conversion->import($grade);
            $rawgrade = $grade;
        }

        // Happy as we're going to get, so write the new data.
        \local_gugrades\grades::write_grade(
            courseid:       $courseid,
            gradeitemid:    $gradeitemid,
            userid:         $userid,
            admingrade:     $admingrade,
            rawgrade:       $rawgrade,
            convertedgrade: $convertedgrade,
            displaygrade:   $displaygrade,
            weightedgrade:  0,
            gradetype:      $reason,
            other:          $other,
            iscurrent:      true,
            iserror:        false,
            auditcomment:   $notes,
            ispoints:       !$conversion->is_scale(),
        );
    }

    /**
     * Add an aditional column (if it doesn't exist already)
     * TODO: Something about notes.
     * @param int $courseid
     * @param int $gradeitemid
     * @param string $reason
     * @param string $other
     * @param string $notes
     */
    public static function write_column(int $courseid, int $gradeitemid, string $reason, string $other, string $notes) {

        // Need column points or scale.
        // If it's converted then it must be scale, otherwise it's whatever the conversion factory thinks.
        $converted = \local_gugrades\conversion::is_conversion_applied($courseid, $gradeitemid);
        if ($converted) {
            $points = false;
        } else {
            $conversion = \local_gugrades\grades::conversion_factory($courseid, $gradeitemid);
            $points = !$conversion->is_scale();
        }
        $column = \local_gugrades\grades::get_column($courseid, $gradeitemid, $reason, $other, $points);

        return $column->id;
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
                $config->value = $setting['value'];
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

    /**
     * Is MyGrades "enabled" for this course
     * Are there any released grades and/or is MyGrades
     * disabled for this course.
     * @param int $courseid
     * @return bool
     */
    public static function is_mygrades_enabled_for_course(int $courseid) {
        global $DB;

        // Get setting. If that's set to disabled then nothing else to do.
        $sqlname = $DB->sql_compare_text('name');
        $sql = "SELECT * FROM {local_gugrades_config}
            WHERE courseid = :courseid
            AND $sqlname = :name
            AND value = :value";
        if ($DB->record_exists_sql($sql, ['courseid' => $courseid, 'name' => 'disabledashboard', 'value' => 1])) {
            return false;
        }

        // If that passes, then check if there are any 'released' grades.
        if ($DB->record_exists('local_gugrades_grade', ['courseid' => $courseid, 'gradetype' => 'RELEASED', 'iscurrent' => 1])) {
            return true;
        }

        return false;
    }

    /**
     * Get list of user's courses
     * (and first level categories)
     * @param int $userid UserID of student
     * @param bool $current Return only current courses
     * @param bool $past Return only past courses
     * @param string $sort Comma separated list of fields to sort by
     * @return array
     */
    public static function dashboard_get_courses(int $userid, bool $current, bool $past, string $sort) {
        global $DB, $USER;

        // If this isn't current user, do they have the rights to look at other users.
        $context = \context_system::instance();

        // Get basic list of enrolments for this user.
        $additionalfields = [
            'enddate',
            'showgrades',
        ];
        if (!$sort) {
            $sort = null;
        }
        $courses = enrol_get_users_courses($userid, true, $additionalfields, $sort);

        // Run through courses to establish which have gugrades/GCAT enabled
        // and also add TL grade category data.
        foreach ($courses as $id => $course) {
            $context = \context_course::instance($id, true);

            // Skip courses with showgrades == 0.
            if (!$course->showgrades) {
                unset($courses[$id]);
                continue;
            }

            // Current/past cutoff is enddate + 30 days.
            $cutoffdate = $course->enddate + (86400 * 30);

            // If current selected only return 'current' courses
            // enddate == 0 is taken to be current, regardless.
            if ($current) {
                if ($course->enddate && ($cutoffdate <= time())) {
                    unset($courses[$id]);
                    continue;
                }
            }

            // If past is selected only return past courses
            // enddate == 0 is taken to be NOT past, regardless.
            if ($past) {
                if (!$course->enddate || (time() < $cutoffdate)) {
                    unset($courses[$id]);
                    continue;
                }
            }

            // These always need to exist for the webservice checks.
            $course->gugradesenabled = false;
            $course->gcatenabled = false;
            $course->firstlevel = [];

            // Check if MyGrades is enabled for this course?
            $course->gugradesenabled = self::is_mygrades_enabled_for_course($id);

            // Check if (old) GCAT is enabled for this course?
            $sqlshortname = $DB->sql_compare_text('shortname');
            $sql = "SELECT * FROM {customfield_data} cd
                JOIN {customfield_field} cf ON cf.id = cd.fieldid
                WHERE cd.instanceid = :courseid
                AND cd.intvalue = 1
                AND $sqlshortname = 'show_on_studentdashboard'";
            if ($DB->record_exists_sql($sql, ['courseid' => $id])) {

                // TODO: does this need any capability checks?
                $course->gcatenabled = true;
            }

            // Get first-level grade categories.
            $categories = \local_gugrades\grades::get_firstlevel($id);
            foreach ($categories as $category) {
                $course->firstlevel[] = [
                    'id' => $category->id,
                    'fullname' => $category->fullname,
                ];
            }
        }

        return $courses;
    }

    /**
     * Get grades and subcategories for given user and grade category
     * TODO: This will need a bit of filling out.
     * @param int $userid
     * @param int $gradecategoryid
     * @return array
     */
    public static function dashboard_get_grades(int $userid, int $gradecategoryid) {
        global $DB, $USER;

        // Get grade category and make some basic checks.
        $gradecategory = $DB->get_record('grade_categories', ['id' => $gradecategoryid], '*', MUST_EXIST);
        $courseid = $gradecategory->courseid;
        $context = \context_course::instance($courseid, true);

        // If this isn't current user, do they have the rights to look at other users.
        if ($USER->id != $userid) {
            require_capability('local/gugrades:readotherdashboard', $context);
        } else {
            require_capability('local/gugrades:readdashboard', $context);
        }

        // TODO: Get grades.
        $grades = \local_gugrades\grades::get_dashboard_grades($userid, $gradecategoryid);

        // Get child categories.
        $childcategories = $DB->get_records('grade_categories', ['parent' => $gradecategoryid]);

        return [
            'grades' => $grades,
            'childcategories' => $childcategories,
        ];
    }

    /**
     * Release grades
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $groupid
     * @param bool $revert
     */
    public static function release_grades(int $courseid, int $gradeitemid, int $groupid, bool $revert) {
        global $DB;

        // Get list of users.
        $activity = \local_gugrades\users::activity_factory($gradeitemid, $courseid, $groupid);
        $users = $activity->get_users();

        // Iterate over users releasing/reverting grades.
        foreach ($users as $user) {
            if ($revert) {

                // If reverting, mark any grades marked RELEASED for this
                // gradeitemid / user as not current.
                $sql = 'UPDATE {local_gugrades_grade}
                    SET iscurrent = 0
                    WHERE gradetype = "RELEASED"
                    AND gradeitemid = :gradeitemid
                    AND userid = :userid';
                $DB->execute($sql, ['gradeitemid' => $gradeitemid, 'userid' => $user->id]);

                // Remove any columns that this has orphaned.
                \local_gugrades\grades::cleanup_empty_columns($gradeitemid);
            } else {
                $usercapture = new usercapture($courseid, $gradeitemid, $user->id);
                $released = $usercapture->get_released();

                if ($released) {
                    \local_gugrades\grades::write_grade(
                        courseid: $courseid,
                        gradeitemid: $gradeitemid,
                        userid: $user->id,
                        admingrade: $released->admingrade,
                        rawgrade: $released->rawgrade,
                        convertedgrade: $released->convertedgrade,
                        displaygrade: $released->displaygrade,
                        weightedgrade: $released->weightedgrade,
                        gradetype: $released->gradetype,
                        other: '',
                        iscurrent: true,
                        iserror: false,
                        auditcomment: 'Release grades',
                        ispoints: $released->points,
                    );
                }
            }
        }
    }

    /**
     * Reset MyGrades for course
     * @param int $courseid
     */
    public static function reset(int $courseid) {
        global $DB;

        // Delete grades.
        $DB->delete_records('local_gugrades_grade', ['courseid' => $courseid]);

        // Delete columns.
        $DB->delete_records('local_gugrades_column', ['courseid' => $courseid]);

        // Delete maps.
        $maps = $DB->get_records('local_gugrades_map', ['courseid' => $courseid]);
        foreach ($maps as $map) {
            $DB->delete_records('local_gugrades_map_value', ['mapid' => $map->id]);
        }
        $DB->delete_records('local_gugrades_map_item', ['courseid' => $courseid]);
        $DB->delete_records('local_gugrades_map', ['courseid' => $courseid]);

        // Clear cache.
        $cache = \cache::make('local_gugrades', 'gradeitems');
        $cache->purge();
    }

    /**
     * Get course groups
     * @param int $courseid
     * @return array
     *
     */
    public static function get_groups(int $courseid) {
        global $DB;

        $groups = $DB->get_records('groups', ['courseid' => $courseid]);

        return $groups;
    }

    /**
     * Get conversion maps
     * @param int $courseid
     * @return array
     */
    public static function get_conversion_maps(int $courseid): array {
        $maps = \local_gugrades\conversion::get_maps($courseid);
        foreach ($maps as $map) {
            $map->inuse = \local_gugrades\conversion::inuse($map->id);
        }

        return $maps;
    }

    /**
     * Get conversion map, given mapid
     * If mapid = 0 then return default mapping values
     * @param int $courseid
     * @param int $mapid
     * @param string $schedule
     * @return int
     */
    public static function get_conversion_map(int $courseid, int $mapid, string $schedule): array {

        // If mapid = 0, then get the new/default map.
        if ($mapid == 0) {
            $map = \local_gugrades\conversion::get_default_map($schedule);

            return [
                'name' => '',
                'schedule' => $schedule,
                'maxgrade' => 100,
                'inuse' => false,
                'map' => $map,
            ];
        } else {
            return \local_gugrades\conversion::get_map_for_editing($mapid);
        }
    }

    /**
     * Write conversion map, mapid=0 means a new one
     * @param int $courseid
     * @param int $mapid
     * @param string $name
     * @param string $schedule
     * @param float $maxgrade
     * @param array $map
     * @return int
     */
    public static function write_conversion_map(
        int $courseid, int $mapid, string $name, string $schedule, float $maxgrade, array $map): int {
        $mapid = \local_gugrades\conversion::write_conversion_map($courseid, $mapid, $name, $schedule, $maxgrade, $map);

        return $mapid;
    }

    /**
     * Delete conversion map
     * @param int $courseid
     * @param int $mapid
     * @return bool
     */
    public static function delete_conversion_map(int $courseid, int $mapid) {
        return \local_gugrades\conversion::delete_conversion_map($courseid, $mapid);
    }

    /**
     * Import conversion map (as a new one)
     * @param int $courseid
     * @param string $jsonmap
     * @return int
     */
    public static function import_conversion_map(int $courseid, string $jsonmap) {
        return \local_gugrades\conversion::import_conversion_map($courseid, $jsonmap);
    }

    /**
     * Select conversion (map).
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $mapid
     */
    public static function select_conversion(int $courseid, int $gradeitemid, int $mapid) {
        \local_gugrades\conversion::select_conversion($courseid, $gradeitemid, $mapid);
    }

    /**
     * get select conversion (map) info.
     * @param int $courseid
     * @param int $gradeitemid
     * @return array
     */
    public static function get_selected_conversion(int $courseid, int $gradeitemid) {
        return \local_gugrades\conversion::get_selected_conversion($courseid, $gradeitemid);
    }

    /**
     * Show/hide grade
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $userid
     * @param bool $hide
     */
    public static function show_hide_grade(int $courseid, int $gradeitemid, int $userid, bool $hide) {
        global $DB;

        if (!$hide) {
            $DB->delete_records('local_gugrades_hidden', ['gradeitemid' => $gradeitemid, 'userid' => $userid]);
        } else {
            if (!$DB->record_exists('local_gugrades_hidden', ['gradeitemid' => $gradeitemid, 'userid' => $userid])) {
                $hidden = new \stdClass();
                $hidden->courseid = $courseid;
                $hidden->gradeitemid = $gradeitemid;
                $hidden->userid = $userid;
                $DB->insert_record('local_gugrades_hidden', $hidden);
            }
        }
    }

    /**
     * Is grade item hidden in Gradebook
     * @param int $gradeitemid
     * @return bool
     */
    protected static function is_gradeitem_hidden(int $gradeitemid) {
        global $DB;

        return $DB->record_exists('grade_items', ['id' => $gradeitemid, 'hidden' => 1]);
    }

    /**
     * Check if grade is hidden
     * @param int $gradeitemid
     * @param int $userid
     * @return boolean
     */
    public static function is_grade_hidden(int $gradeitemid, int $userid) {
        global $DB;

        // Check (internal) MyGrades hidden flag.
        $mygradeshidden = $DB->record_exists('local_gugrades_hidden', ['gradeitemid' => $gradeitemid, 'userid' => $userid]);

        // Check grade item hidden in Gradebook.
        $gradeitemhidden = self::is_gradeitem_hidden($gradeitemid);

        // Check user grade hidden.
        $gradegradehidden = $DB->record_exists('grade_grades', ['itemid' => $gradeitemid, 'userid' => $userid, 'hidden' => 1]);

        // Any being hidden counts.
        return $mygradeshidden || $gradeitemhidden || $gradegradehidden;
    }

    /**
     * Get aggregation page
     * @param int $courseid
     * @param int $gradecategoryid
     * @param string $firstname
     * @param string $lastname
     * @param int $groupid
     * @param bool $aggregate
     * @return array
     */
    public static function get_aggregation_page(
        int $courseid,
        int $gradecategoryid,
        string $firstname,
        string $lastname,
        int $groupid,
        bool $aggregate
        ) {

        // Get the level 1 parent category
        $level1id = \local_gugrades\grades::get_level_one_parent($gradecategoryid);

        // build (and cache) grade structure (whole tree).
        \local_gugrades\aggregation::recurse_tree($courseid, $level1id, $aggregate);

        // Get categories and items at this level.
        [$columns, $atype, $warnings] = \local_gugrades\aggregation::get_columns($courseid, $gradecategoryid);

        // Get all the students.
        $users = \local_gugrades\aggregation::get_users($courseid, $firstname, $lastname, $groupid);

        // Recalculate?
        if ($aggregate) {
            $users = \local_gugrades\aggregation::aggregate($courseid, $gradecategoryid, $users);
        }

        // Warning message on top level.
        $istoplevel = \local_gugrades\aggregation::is_top_level($gradecategoryid);

        // Add the columns to the user fields.
        $users = \local_gugrades\aggregation::add_aggregation_fields_to_users($courseid, $gradecategoryid, $users, $columns);

        // Add pictures to user fields.
        $users = \local_gugrades\users::add_pictures_to_user_records($users);

        // Get breadcrumb trail.
        $breadcrumb = \local_gugrades\aggregation::get_breadcrumb($gradecategoryid);

        return [
            'toplevel' => $istoplevel,
            'atype' => $atype,
            'strategy' => \local_gugrades\aggregation::get_formatted_strategy($gradecategoryid),
            'warnings' => $warnings,
            'columns' => $columns,
            'users' => $users,
            'breadcrumb' => $breadcrumb,
        ];
    }

    /**
     * Resit required
     * @param int $courseid
     * @param int $userid
     * @param bool $resit
     */
    public static function resit_required(int $courseid, int $userid, bool $resit) {
        global $DB;

        if (!$resit) {
            $DB->delete_records('local_gugrades_resitrequired', ['courseid' => $courseid, 'userid' => $userid]);
        } else {
            if (!$DB->record_exists('local_gugrades_resitrequired', ['courseid' => $courseid, 'userid' => $userid])) {
                $resitrequired = new \stdClass();
                $resitrequired->courseid = $courseid;
                $resitrequired->userid = $userid;
                $DB->insert_record('local_gugrades_resitrequired', $resitrequired);
            }
        }
    }
}
