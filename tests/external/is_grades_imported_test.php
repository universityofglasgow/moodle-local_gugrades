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
 * Test has_capability web service
 * @package    local_gugrades
 * @copyright  2024
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_gugrades\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/local/gugrades/tests/external/gugrades_advanced_testcase.php');

/**
 * Test has_capability web service.
 */
class is_grades_imported_test extends \local_gugrades\external\gugrades_advanced_testcase {

    /**
     * Check that a top=level activiy shows recursiveavailable = false
     *
     * @covers \local_gugrades\external\has_capability::execute
     */
    public function test_recursiveavailable_false() {

        // Log in as teacher
        $this->setUser($this->teacher);

        $gradesimported = is_grades_imported::execute($this->course->id, $this->gradeitemidassign1, 0);
        $gradesimported = \external_api::clean_returnvalue(
            is_grades_imported::execute_returns(),
            $gradesimported
        );

        // Check recursiveavailable field
        $this->assertArrayHasKey('recursiveavailable', $gradesimported);
        $this->assertFalse($gradesimported['recursiveavailable']);

        // Check recursivematch field
        $this->assertArrayHasKey('recursivematch', $gradesimported);
        $this->assertFalse($gradesimported['recursivematch']);
    }

    /**
     * Check that a top=level activiy shows recursiveavailable = true
     *
     * @covers \local_gugrades\external\has_capability::execute
     */
    public function test_recursiveavailable_true() {

        // Log in as teacher
        $this->setUser($this->teacher);

        $gradesimported = is_grades_imported::execute($this->course->id, $this->gradeitemsecond1, 0);
        $gradesimported = \external_api::clean_returnvalue(
            is_grades_imported::execute_returns(),
            $gradesimported
        );

        // Check recursiveavailable field
        $this->assertArrayHasKey('recursiveavailable', $gradesimported);
        $this->assertTrue($gradesimported['recursiveavailable']);

        // Check recursivematch field
        $this->assertArrayHasKey('recursivematch', $gradesimported);
        $this->assertTrue($gradesimported['recursivematch']);
    }
}
