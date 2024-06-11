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

use core_external\external_api;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/local/gugrades/tests/external/gugrades_advanced_testcase.php');

/**
 * Test get_activities web service.
 */
final class dashboard_get_courses_test extends \local_gugrades\external\gugrades_advanced_testcase {

    /**
     * Check that weird current/past filter works properly
     * Note that past/future 'cutoff' date is 30 days in the future
     *
     * @covers \local_gugrades\external\dashboard_get_courses::execute
     */
    public function test_filter_courses_by_date(): void {
        global $DB;

        // We're the test student.
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

        // Create courses with end date in the 'past'.
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

        // Enable GUGCAT in some of the courses.
        $this->enable_gugcat_dashboard($currentcourse1->id, true);
        $this->enable_gugcat_dashboard($pastcourse1->id, true);

        // Enrol student on all of the above
        // Note - the student is enrolled on a 5th course in setUp().
        $studentid = $this->student->id;
        $this->getDataGenerator()->enrol_user($studentid, $currentcourse1->id);
        $this->getDataGenerator()->enrol_user($studentid, $currentcourse2->id);
        $this->getDataGenerator()->enrol_user($studentid, $pastcourse1->id);
        $this->getDataGenerator()->enrol_user($studentid, $pastcourse2->id);

        // Check that all courses are returned if no past/current.
        $courses = dashboard_get_courses::execute($studentid, false, false, '');
        $courses = external_api::clean_returnvalue(
            dashboard_get_courses::execute_returns(),
            $courses
        );
        $this->assertIsArray($courses);
        $this->assertCount(5, $courses);

        // Check top-level grade categories.
        $catcourse = $courses[4];
        $this->assertCount(2, $catcourse['firstlevel']);
        $this->assertEquals('Summative', $catcourse['firstlevel'][0]['fullname']);
        $this->assertIsInt($catcourse['firstlevel'][0]['id']);
        $this->assertEquals('Formative', $catcourse['firstlevel'][1]['fullname']);

        // Check GUGCAT enabled.
        $this->assertTrue($courses[1]['gcatenabled']);
        $this->assertTrue($courses[3]['gcatenabled']);

        // Get only 'current' courses
        // Default course should be included as enddate is disabled (= 0).
        $courses = dashboard_get_courses::execute($studentid, true, false, '');
        $courses = external_api::clean_returnvalue(
            dashboard_get_courses::execute_returns(),
            $courses
        );
        $this->assertIsArray($courses);
        $this->assertCount(3, $courses);
        $this->assertEquals('Current Course Two', $courses[0]['fullname']);

        // Get only 'past' courses.
        $courses = dashboard_get_courses::execute($studentid, false, true, '');
        $courses = external_api::clean_returnvalue(
            dashboard_get_courses::execute_returns(),
            $courses
        );
        $this->assertIsArray($courses);
        $this->assertEquals('Past Course Two', $courses[0]['fullname']);

        // Check the courses that should be not enabled for MyGrades.
        $this->assertFalse($courses[0]['gugradesenabled']);
        $this->assertFalse($courses[1]['gugradesenabled']);

        // Check sorting.
        $courses = dashboard_get_courses::execute($studentid, true, false, 'enddate');
        $courses = external_api::clean_returnvalue(
            dashboard_get_courses::execute_returns(),
            $courses
        );
        $this->assertIsArray($courses);
        $this->assertCount(3, $courses);
        $this->assertEquals('Current Course Two', $courses[1]['fullname']);

        // MyGrades is enabled by releasing grades for a course.
        $userlist = [
            $this->student->id,
            $this->student2->id,
        ];
        $status = import_grades_users::execute($this->course->id, $this->gradeitemidassign2, false, false, $userlist);
        $status = external_api::clean_returnvalue(
            import_grades_users::execute_returns(),
            $status
        );
        $status = release_grades::execute($this->course->id, $this->gradeitemidassign2, 0, false);
        $status = external_api::clean_returnvalue(
            release_grades::execute_returns(),
            $status
        );

        // Check again for mygrades.
        $courses = dashboard_get_courses::execute($studentid, true, false, '');
        $courses = external_api::clean_returnvalue(
            dashboard_get_courses::execute_returns(),
            $courses
        );
        $this->assertCount(3, $courses);
        $this->assertTrue($courses[2]['gugradesenabled']);

        // Switch off the course.
        $this->disable_dashboard($this->course->id, true);

        // Check again for mygrades (above should have switched it off, despite release).
        $courses = dashboard_get_courses::execute($studentid, true, false, '');
        $courses = external_api::clean_returnvalue(
            dashboard_get_courses::execute_returns(),
            $courses
        );
        $this->assertCount(3, $courses);
        $this->assertFalse($courses[2]['gugradesenabled']);
    }

}
