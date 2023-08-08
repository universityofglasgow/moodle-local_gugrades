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
 *
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
 * Get the information to construct add grade form
 */
class get_add_grade_form extends \external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'gradeitemid' => new external_value(PARAM_INT, 'Grade item id'),
            'userid' => new external_value(PARAM_INT, 'User id - for user we are adding grade'),
        ]);
    }

    public static function execute($courseid, $gradeitemid, $userid) {
        global $DB;

        // Security.
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'gradeitemid' => $gradeitemid,
            'userid' => $userid,
        ]);

        // Get item (if it exists)
        $item = $DB->get_record('grade_items', ['id' => $gradeitemid], '*', MUST_EXIST);

        // More security
        $context = \context_course::instance($courseid);
        self::validate_context($context);

        return \local_gugrades\api::get_add_grade_form($courseid, $gradeitemid, $userid);
    }

    public static function execute_returns() {
        return new external_single_structure([
            'gradetypes' => new external_multiple_structure(
                new external_single_structure([
                    'value' => new external_value(PARAM_TEXT, 'Short name of gradetype'),
                    'label' => new external_value(PARAM_TEXT, 'Description of gradetype'),
                ])
            ),
            'itemname' => new external_value(PARAM_TEXT, 'Grade item name'),
            'fullname' => new external_value(PARAM_TEXT, 'User full name'),
            'idnumber' => new external_value(PARAM_TEXT, 'User ID number'),
        ]);
    }

}