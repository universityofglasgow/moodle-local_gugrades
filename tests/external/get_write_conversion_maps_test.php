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
 * Test get_conversion_maps AND write_conversion_maps
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
 * Test get_activities web service.
 */
class get_write_conversion_maps_test extends \local_gugrades\external\gugrades_advanced_testcase {

    /**
     * Check writing and reading data
     */
    public function test_conversion_maps() {

        // Read maps for course (should be none).
        $maps = get_conversion_maps::execute($this->course->id);
        $maps = \external_api::clean_returnvalue(
            get_conversion_maps::execute_returns(),
            $maps
        );

        // Empty response
        $this->assertEmpty($maps);
    }

    /**
     * Check reading default map
     */
    public function test_get_default_map() {

        // Read map with id 0 (new map) for Schedule A.
        $mapstuff = get_conversion_map::execute($this->course->id, 0, 'schedulea');
        $mapstuff = \external_api::clean_returnvalue(
            get_conversion_map::execute_returns(),
            $mapstuff
        );

        $this->assertEquals(100, $mapstuff['maxgrade']);
        $this->assertArrayHasKey('map', $mapstuff);
        $map = $mapstuff['map'];
        $this->assertCount(23, $map);
        $this->assertEquals('H', $map[0]['band']);
        $this->assertEquals(0, $map[0]['grade']);
        $this->assertEquals('A1', $map[22]['band']);
        $this->assertEquals(22, $map[22]['grade']);

        // Read map with id 0 (new map) for Schedule B.
        $mapstuff = get_conversion_map::execute($this->course->id, 0, 'scheduleb');
        $mapstuff = \external_api::clean_returnvalue(
            get_conversion_map::execute_returns(),
            $mapstuff
        );

        $this->assertEquals(100, $mapstuff['maxgrade']);
        $this->assertArrayHasKey('map', $mapstuff);
        $map = $mapstuff['map'];
        $this->assertCount(8, $map);
        $this->assertEquals('H', $map[0]['band']);
        $this->assertEquals(0, $map[0]['grade']);
        $this->assertEquals('A0', $map[7]['band']);
        $this->assertEquals(22, $map[7]['grade']);

    }
}
