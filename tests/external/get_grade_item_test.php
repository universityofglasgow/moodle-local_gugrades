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
 * Test get_grade_item web service
 * @package    local_gugrades
 * @copyright  2024
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
 * Test import_grades_users web service.
 */
final class get_grade_item_test extends \local_gugrades\external\gugrades_advanced_testcase {

    /**
     * Get the types and check
     *
     * @covers \local_gugrades\external\import_grades_users::execute
     */
    public function test_get_grade_item(): void {
        global $DB;

        // Use the test teacher.
        $this->setUser($this->teacher->id);

        $item = get_grade_item::execute($this->gradeitemidassign1);
        $item = external_api::clean_returnvalue(
            get_grade_item::execute_returns(),
            $item
        );

        $this->assertEquals('Assignment 1', $item['itemname']);
        $this->assertFalse($item['isscale']);
        $this->assertEquals(100, $item['grademax']);

        $item = get_grade_item::execute($this->gradeitemidassign2);
        $item = external_api::clean_returnvalue(
            get_grade_item::execute_returns(),
            $item
        );

        $this->assertEquals('Assignment 2', $item['itemname']);
        $this->assertTrue($item['isscale']);
        $this->assertEquals(23, $item['grademax']);
        $this->assertEquals('UofG 22 point scale', $item['scalename']);

        // Manual item.
        $item = get_grade_item::execute($this->gradeitemsecond1);
        $item = external_api::clean_returnvalue(
            get_grade_item::execute_returns(),
            $item
        );

        $this->assertEquals('Grade item 1', $item['itemname']);
        $this->assertFalse($item['isscale']);
        $this->assertEquals(100.0, $item['grademax']);
        $this->assertEquals('', $item['scalename']);
    }
}
