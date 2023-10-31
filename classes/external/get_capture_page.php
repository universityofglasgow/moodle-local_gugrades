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
 * Define function get_capture_page
 * @package    local_gugrades
 * @copyright  2023
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_gugrades\external;

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * Define function get_capture_page
 */
class get_capture_page extends \external_api {

    /**
     * Define function parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'gradeitemid' => new external_value(PARAM_INT, 'Grade item id number'),
            'firstname' => new external_value(PARAM_ALPHA, 'Firstname filter - first letter or empty for all'),
            'lastname' => new external_value(PARAM_ALPHA, 'Lastname filter - first letter or empty for all'),
            'viewfullnames' => new external_value(PARAM_BOOL, 'Show full names of students (with capability)'),
        ]);
    }

    /**
     * Execute function
     * @param int $courseid
     * @param int $gradeitemid
     * @param string $firstname
     * @param string $lastname
     * @param bool $viewfullnames
     * @return array
     */
    public static function execute($courseid, $gradeitemid, $firstname, $lastname, $viewfullnames) {

        // Security.
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'gradeitemid' => $gradeitemid,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'viewfullnames' => $viewfullnames,
        ]);

        // Security.
        $context = \context_course::instance($courseid);
        self::validate_context($context);

        return \local_gugrades\api::get_capture_page($courseid, $gradeitemid, $firstname, $lastname, $viewfullnames);
    }

    /**
     * Define result
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'users' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'User ID'),
                    'displayname' => new external_value(PARAM_TEXT, 'Name to display for this user'),
                    'pictureurl' => new external_value(PARAM_URL, 'URL of user avatar'),
                    'idnumber' => new external_value(PARAM_TEXT, 'User ID number'),
                    'alert' => new external_value(PARAM_BOOL, 'Show discrepancy alert'),
                    'grades' => new external_multiple_structure(
                        new external_single_structure([
                            'displaygrade' => new external_value(PARAM_TEXT, 'Grade for display'),
                            'gradetype' => new external_value(PARAM_TEXT, 'FIRST, SECOND and so on'),
                            'columnid' => new external_value(PARAM_INT, 'ID in column table'),
                        ])
                    ),
                ])
            ),
            'columns' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Column id'),
                    'gradetype' => new external_value(PARAM_TEXT, 'FIRST, SECOND and so on'),
                    'description' => new external_value(PARAM_TEXT, 'Heading for this grade type'),
                ])
            ),
            'hidden' => new external_value(PARAM_BOOL, 'True if student names are hidden'),
            'itemtype' => new external_value(PARAM_TEXT, 'Name of item type (quiz, assign, manual etc)'),
            'itemname' => new external_value(PARAM_TEXT, 'Name of item'),
            'gradesupported' => new external_value(PARAM_BOOL,
                'Is the selected grade type one we can handle / have configured (for scales)'),
        ]);
    }

}
