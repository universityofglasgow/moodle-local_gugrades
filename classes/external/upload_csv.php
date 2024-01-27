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
 * Define function csv_upload
 * @package    local_gugrades
 * @copyright  2024
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
 * Define function get_audit
 */
class upload_csv extends \external_api {

    /**
     * Define function parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'gradeitemid' => new external_value(PARAM_INT, 'Grade item ID'),
            'groupid' => new external_value(PARAM_INT, 'Group ID'),
            'testrun' => new external_value(PARAM_BOOL, 'If true, only test data and return. Do not write'),
            'reason' => new external_value(PARAM_ALPHA, 'Reason (SECOND, THIRD and so on)'),
            'csv' => new external_value(PARAM_TEXT, 'Raw CSV file data'),
        ]);
    }

    /**
     * Execute function
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $groupid
     * @param int $testrun
     * @param string $reason
     * @param string $csv
     * @return array
     */
    public static function execute($courseid, $gradeitemid, $groupid, $testrun, $reason, $csv) {

        // Security.
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'gradeitemid' => $gradeitemid,
            'groupid' => $groupid,
            'testrun' => $testrun,
            'reason' => $reason,
            'csv' => $csv,
        ]);

        // Security.
        $context = \context_course::instance($courseid);
        self::validate_context($context);

        $lines = \local_gugrades\api::csv_upload($courseid, $gradeitemid, $groupid, $testrun, $reason, $csv);
        return $lines;
    }

    /**
     * Define function result
     * @return external_multiple_structure
     */
    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'name' => new external_value(PARAM_TEXT, 'User name'),
                'idnumber' => new external_value(PARAM_TEXT, 'User ID number'),
                'grade' => new external_value(PARAM_TEXT, 'Grade to assign'),
                'gradevalue' => new external_value(PARAM_FLOAT, 'Grade value'),
                'error' => new external_value(PARAM_TEXT, 'Any error condition'),
            ])
        );
    }
}
