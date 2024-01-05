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
 * Test dashboard_get_courses web service
 * @package    local_gugrades
 * @copyright  2023
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_gugrades\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/local/gugrades/tests/external/gugrades_advanced_testcase.php');

/**
 * Test get_activities web service.
 */
class dashboard_get_courses_test extends \local_gugrades\external\gugrades_advanced_testcase {

    /**
     * Check that weird current/past filter works properly
     * Note that past/future 'cutoff' date is 30 days in the future
     *
     * @covers \local_gugrades\external\dashboard_get_courses::execute
     */
    public function test_filter_courses_by_date() {
        global $DB;

        // We're the test student
        $this->setUser($this->student->id);

        // Create some courses with suitable end dates.
        $oneyear = 86400 * 365;

        // Create courses with end date in the 'future'.
        $currentcourse1 = $this->getDataGenerator()->create_course([
            'fullname' => 'Current Course One',
            'startdate' => time() - $oneyear,
            'enddate' => time() + $oneyear,
        ]);
        $currentcourse2 = $this->getDataGenerator()->create_course([
            'fullname' => 'Current Course Two',
            'startdate' => time() - $oneyear,
            'enddate' => time() + 86400,
        ]);

        // Create courses with end date in the 'past'
        $pastcourse1 = $this->getDataGenerator()->create_course([
            'fullname' => 'Past Course One',
            'startdate' => time() - (2 * $oneyear),
            'enddate' => time() - $oneyear,
        ]);
        $pastcourse2 = $this->getDataGenerator()->create_course([
            'fullname' => 'Past Course Two',
            'startdate' => time() - (2 * $oneyear),
            'enddate' => time() - (30 * 86400), // Last possible day!
        ]);

        // Enable gugrades in some of the courses
        $this->enable_dashboard($currentcourse1->id, true);
        $this->enable_dashboard($pastcourse2->id, true);

        // Enable GUGCAT in some of the courses
        $this->enable_gugcat_dashboard($currentcourse1->id, true);
        $this->enable_gugcat_dashboard($pastcourse1->id, true);

        // Enrol student on all of the above
        // Note - the student is enrolled on a 5th course in setUp().
        $studentid = $this->student->id;
        $this->getDataGenerator()->enrol_user($studentid, $currentcourse1->id);
        $this->getDataGenerator()->enrol_user($studentid, $currentcourse2->id);
        $this->getDataGenerator()->enrol_user($studentid, $pastcourse1->id);
        $this->getDataGenerator()->enrol_user($studentid, $pastcourse2->id);

        // Check that all courses are returned if no past/current
        $courses = dashboard_get_courses::execute($studentid, false, false);
        $courses = \external_api::clean_returnvalue(
            dashboard_get_courses::execute_returns(),
            $courses
        );
        $this->assertIsArray($courses);
        $this->assertCount(5, $courses);

        // Check GUGCAT enabled
        $this->assertTrue($courses[1]['gcatenabled']);
        $this->assertTrue($courses[3]['gcatenabled']);

        // Get only 'current' courses
        // Default course should be included as enddate is disabled (= 0).
        $courses = dashboard_get_courses::execute($studentid, true, false);
        $courses = \external_api::clean_returnvalue(
            dashboard_get_courses::execute_returns(),
            $courses
        );
        $this->assertIsArray($courses);
        $this->assertCount(3, $courses);
        $this->assertEquals('Current Course Two', $courses[0]['fullname']);

        // Get only 'past' courses
        $courses = dashboard_get_courses::execute($studentid, false, true);
        $courses = \external_api::clean_returnvalue(
            dashboard_get_courses::execute_returns(),
            $courses
        );
        $this->assertIsArray($courses);
        $this->assertEquals('Past Course Two', $courses[0]['fullname']);

        // Check the courses that should be enabled for MyGrades.
        $this->assertTrue($courses[0]['gugradesenabled']);
        $this->assertNotTrue($courses[1]['gugradesenabled']);

    }

}
