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
 * Write the data from the 'add grade' button
 */
class write_additional_grade extends \external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'gradeitemid' => new external_value(PARAM_INT, 'Grade item id'),
            'userid' => new external_value(PARAM_INT, 'User id - for user we are adding grade'),
            'reason' => new external_value(PARAM_TEXT, 'Reason for grade - SECOND, AGREED etc.'),
            'other' => new external_value(PARAM_TEXT, 'Detail if reason == OTHER'),
            'scale' => new external_value(PARAM_INT, 'Scale value'),
            'grade' => new external_value(PARAM_FLOAT, 'Points grade'),
            'notes' => new external_value(PARAM_TEXT, 'Optional notes'),
        ]);
    }

    public static function execute($courseid, $gradeitemid, $userid, $reason, $other, $scale, $grade, $notes) {
        global $DB;

        // Security.
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'gradeitemid' => $gradeitemid,
            'userid' => $userid,
            'reason' => $reason,
            'other' => $other,
            'scale' => $scale,
            'grade' => $grade,
            'notes' => $notes,
        ]);

        // Get item (if it exists)
        $item = $DB->get_record('grade_items', ['id' => $gradeitemid], '*', MUST_EXIST);

        // More security
        $context = \context_course::instance($courseid);
        self::validate_context($context);

        return \local_gugrades\api::write_additional_grade($courseid, $gradeitemid, $userid, $reason, $other, $scale, $grade, $notes);
    }

    public static function execute_returns() {
        return new external_single_structure([

        ]);
    }

}