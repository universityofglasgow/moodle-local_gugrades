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
 * Define function get aggregation page
 * @package    local_gugrades
 * @copyright  2024
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_gugrades\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;

defined('MOODLE_INTERNAL') || die();

/**
 * Define function get_aggregation_page
 */
class get_aggregation_page extends external_api {

    /**
     * Define function parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'gradecategoryid' => new external_value(PARAM_INT, 'Grade category id number'),
            'firstname' => new external_value(PARAM_ALPHA, 'Firstname filter - first letter or empty for all'),
            'lastname' => new external_value(PARAM_ALPHA, 'Lastname filter - first letter or empty for all'),
            'groupid' => new external_value(PARAM_INT, 'Group ID to filter - 0 means everybody'),
            'aggregate' => new external_value(PARAM_BOOL, '(force) aggregation calculations'),
        ]);
    }

    /**
     * Execute function
     * @param int $courseid
     * @param int $gradecategoryid
     * @param string $firstname
     * @param string $lastname
     * @param int $groupid
     * @param bool $aggregate
     * @return array
     */
    public static function execute($courseid, $gradecategoryid, $firstname, $lastname, $groupid, $aggregate) {

        // Security.
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'gradecategoryid' => $gradecategoryid,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'groupid' => $groupid,
            'aggregate' => $aggregate,
        ]);

        // Security.
        $context = \context_course::instance($courseid);
        self::validate_context($context);

        $page = \local_gugrades\api::get_aggregation_page(
            $courseid, $gradecategoryid, $firstname, $lastname, $groupid, $aggregate);

        return $page;
    }

    /**
     * Define result
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'toplevel' => new external_value(PARAM_BOOL, 'Is this the topmost level?'),
            'allscales' => new external_value(PARAM_BOOL, '(Top level only) are all grades (converted to) scales?'),
            'users' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'User ID'),
                    'displayname' => new external_value(PARAM_TEXT, 'Name to display for this user'),
                    'pictureurl' => new external_value(PARAM_URL, 'URL of user avatar'),
                    'idnumber' => new external_value(PARAM_TEXT, 'User ID number'),
                    'resitrequired' => new external_value(PARAM_BOOL, 'Is resit required?'),
                    'completed' => new external_value(PARAM_INT, '%age of course completed'),
                    'coursetotal' => new external_value(PARAM_TEXT, 'Content for course total column'),
                    'fields' => new external_multiple_structure(
                        new external_single_structure([
                            'fieldname' => new external_value(PARAM_TEXT, 'Identifier for column'),
                            'itemname' => new external_value(PARAM_TEXT, 'Shortened item name (for debugging, mostly)'),
                            'display' => new external_value(PARAM_TEXT, 'Grade for display'),
                        ])
                    ),
                ])
            ),
            'categories' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Grade category ID'),
                    'shortname' => new external_value(PARAM_TEXT, 'Shortened version of fullname'),
                    'fullname' => new external_value(PARAM_TEXT, 'Full name of grade category'),
                    'weight' => new external_value(PARAM_FLOAT, 'Weighting of category'),
                ])
            ),
            'items' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Grade item ID'),
                    'shortname' => new external_value(PARAM_TEXT, 'Shortened version of itemname'),
                    'itemname' => new external_value(PARAM_TEXT, 'Full name of grade category'),
                    'weight' => new external_value(PARAM_FLOAT, 'Weighting of category'),
                ])
            ),
            'columns' => new external_multiple_structure(
                new external_single_structure([
                    'fieldname' => new external_value(PARAM_TEXT, 'Identifier for column'),
                    'gradeitemid' => new external_value(PARAM_INT, 'Grade item id (even for categories'),
                    'categoryid' => new external_value(PARAM_INT, 'Category ID, or 0'),
                    'shortname' => new external_value(PARAM_TEXT, 'Short name'),
                    'fullname' => new external_value(PARAM_TEXT, 'Full name'),
                    'weight' => new external_value(PARAM_INT, 'Weighting as percentage'),
                    'gradetype' => new external_value(PARAM_TEXT, 'Name of scale or points'),
                    'grademax' => new external_value(PARAM_INT, 'Maximum grade'),
                    'isscale' => new external_value(PARAM_BOOL, 'True if a scale, otherwise points'),
                ])
            ),
            'breadcrumb' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Grade category id'),
                    'shortname' => new external_value(PARAM_TEXT, 'Grade category shortname'),
                ])
            ),
        ]);
    }

}
