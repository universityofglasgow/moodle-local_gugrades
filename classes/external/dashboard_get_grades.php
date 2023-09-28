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
 * Get the data associated with a grade item
 */
class dashboard_get_grades extends \external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, 'User to fetch courses for'),
            'gradecategoryid' => new external_value(PARAM_INT, 'Grade category ID'),
        ]);
    }

    public static function execute($userid, $gradecategoryid) {

        // Security.
        $params = self::validate_parameters(self::execute_parameters(), ['userid' => $userid, 'gradecategoryid' => $gradecategoryid]);

        $context = \context_system::instance();
        self::validate_context($context);

        return \local_gugrades\api::dashboard_get_grades($userid, $gradecategoryid);
    }

    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT, 'Course ID'),
                'shortname' => new external_value(PARAM_TEXT, 'Short name of course'),
                'fullname' => new external_value(PARAM_TEXT, 'Fullname of course'),
                'startdate' => new external_value(PARAM_INT, 'Start date (unix timestamp)'),
                'enddate' => new external_value(PARAM_INT, 'End date (unix timestamp)'),   
                'firstlevel' => new external_multiple_structure(
                    new external_single_structure([
                        'id' => new external_value(PARAM_INT, 'Category ID'),
                        'fullname' => new external_value(PARAM_TEXT, 'Full name of grade category'),
                    ])
                ),
            ])
        );
    }

}