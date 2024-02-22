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
     * @covers \local_gugrades\external\get_conversion_maps::execute
     */
    public function test_conversion_maps() {

        // Read maps for course (should be none).
        $maps = get_conversion_maps::execute($this->course->id);
        $maps = \external_api::clean_returnvalue(
            get_conversion_maps::execute_returns(),
            $maps
        );

        // Empty response.
        $this->assertEmpty($maps);
    }

    /**
     * Check reading default map
     * @covers \local_gugrades\external\get_conversion_map::execute
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

    /**
     * Check getting then writing default map
     * @covers \local_gugrades\external\get_conversion_map::execute
     * @covers \local_gugrades\external\write_conversion_map::execute
     */
    public function test_read_write_default_map() {
        global $DB;

        // Read map with id 0 (new map) for Schedule A.
        $mapstuff = get_conversion_map::execute($this->course->id, 0, 'schedulea');
        $mapstuff = \external_api::clean_returnvalue(
            get_conversion_map::execute_returns(),
            $mapstuff
        );

        // Write map back.
        $name = 'Test conversion map';
        $schedule = 'schedulea';
        $maxgrade = 100.0;
        $map = $mapstuff['map'];
        $mapid = write_conversion_map::execute($this->course->id, 0, $name, $schedule, $maxgrade, $map);
        $mapid = \external_api::clean_returnvalue(
            write_conversion_map::execute_returns(),
            $mapid
        );
        $mapid = $mapid['mapid'];

        // Check map table.
        $mapinfo = $DB->get_record('local_gugrades_map', ['id' => $mapid], '*', MUST_EXIST);
        $this->assertEquals('Test conversion map', $mapinfo->name);

        // Check map values.
        $values = array_values($DB->get_records('local_gugrades_map_value', ['mapid' => $mapid]));
        $this->assertCount(23, $values);
        $this->assertEquals(92, $values[22]->percentage);

        // Read back uploaded map.
        $mapstuff = get_conversion_map::execute($this->course->id, $mapid, 'schedulea');
        $mapstuff = \external_api::clean_returnvalue(
            get_conversion_map::execute_returns(),
            $mapstuff
        );

        // Delete map.
        $success = delete_conversion_map::execute($this->course->id, $mapid);
        $success = \external_api::clean_returnvalue(
            delete_conversion_map::execute_returns(),
            $success
        );

        // Try to read it again (should fail).
        $this->expectException('dml_missing_record_exception');
        $mapstuff = get_conversion_map::execute($this->course->id, $mapid, 'schedulea');

    }

    /**
     * Similar to above but for ScheduleB
     * @covers \local_gugrades\external\get_conversion_map::execute
     * @covers \local_gugrades\external\write_conversion_map::execute
     */
    public function test_read_write_scheduleb() {
        global $DB;

        // Read map with id 0 (new map) for Schedule B.
        $mapstuff = get_conversion_map::execute($this->course->id, 0, 'scheduleb');
        $mapstuff = \external_api::clean_returnvalue(
            get_conversion_map::execute_returns(),
            $mapstuff
        );

        // Write map back.
        $name = 'Test conversion map B';
        $schedule = 'scheduleb';
        $maxgrade = 100.0;
        $map = $mapstuff['map'];
        $mapid = write_conversion_map::execute($this->course->id, 0, $name, $schedule, $maxgrade, $map);
        $mapid = \external_api::clean_returnvalue(
            write_conversion_map::execute_returns(),
            $mapid
        );
        $mapid = $mapid['mapid'];

        // Read back uploaded map.
        $mapstuff = get_conversion_map::execute($this->course->id, $mapid, 'scheduleb');
        $mapstuff = \external_api::clean_returnvalue(
            get_conversion_map::execute_returns(),
            $mapstuff
        );

        $this->assertEquals('scheduleb', $mapstuff['schedule']);
    }

    /**
     * Test import json
     * @covers \local_gugrades\external\get_conversion_map::execute
     * @covers \local_gugrades\external\write_conversion_map::execute
     */
    public function test_import_json() {
        global $DB;

        $jsonmap = '{
            "name": "Test import map",
            "schedule": "scheduleb",
            "maxgrade": 100,
            "inuse": false,
            "map": [
                {
                    "band": "H",
                    "bound": 0,
                    "grade": 0
                },
                {
                    "band": "G0",
                    "bound": 9,
                    "grade": 2
                },
                {
                    "band": "F0",
                    "bound": 19,
                    "grade": 5
                },
                {
                    "band": "E0",
                    "bound": 29,
                    "grade": 8
                },
                {
                    "band": "D0",
                    "bound": 39,
                    "grade": 11
                },
                {
                    "band": "C0",
                    "bound": 53,
                    "grade": 14
                },
                {
                    "band": "B0",
                    "bound": 59,
                    "grade": 17
                },
                {
                    "band": "A0",
                    "bound": 69,
                    "grade": 22
                }
            ]
        }';

        $mapid = import_conversion_map::execute($this->course->id, $jsonmap);
        $mapid = \external_api::clean_returnvalue(
            import_conversion_map::execute_returns(),
            $mapid
        );
        $mapid = $mapid['mapid'];

        // Read back uploaded map.
        $mapstuff = get_conversion_map::execute($this->course->id, $mapid, '');
        $mapstuff = \external_api::clean_returnvalue(
            get_conversion_map::execute_returns(),
            $mapstuff
        );

        $this->assertEquals('Test import map', $mapstuff['name']);
    }

    /**
     * Check selecting a default map
     * @covers \local_gugrades\external\get_conversion_map::execute
     * @covers \local_gugrades\external\write_conversion_map::execute
     * @covers \local_gugrades\external\select_conversion::execute
     */
    public function test_select_map() {
        global $DB;

        // Read map with id 0 (new map) for Schedule A.
        $mapstuff = get_conversion_map::execute($this->course->id, 0, 'schedulea');
        $mapstuff = \external_api::clean_returnvalue(
            get_conversion_map::execute_returns(),
            $mapstuff
        );

        // Write map back.
        $name = 'Test conversion map';
        $schedule = 'schedulea';
        $maxgrade = 100.0;
        $map = $mapstuff['map'];
        $mapida = write_conversion_map::execute($this->course->id, 0, $name, $schedule, $maxgrade, $map);
        $mapida = \external_api::clean_returnvalue(
            write_conversion_map::execute_returns(),
            $mapida
        );
        $mapida = $mapida['mapid'];

        // Read map with id 0 (new map) for Schedule B.
        $mapstuff = get_conversion_map::execute($this->course->id, 0, 'scheduleb');
        $mapstuff = \external_api::clean_returnvalue(
            get_conversion_map::execute_returns(),
            $mapstuff
        );

        // Write map back.
        $name = 'Test conversion map';
        $schedule = 'scheduleb';
        $maxgrade = 100.0;
        $map = $mapstuff['map'];
        $mapidb = write_conversion_map::execute($this->course->id, 0, $name, $schedule, $maxgrade, $map);
        $mapidb = \external_api::clean_returnvalue(
            write_conversion_map::execute_returns(),
            $mapidb
        );
        $mapidb = $mapidb['mapid'];

        // Select this map.
        $nothing = select_conversion::execute($this->course->id, $this->gradeitemidassign1, $mapida);
        $nothing = \external_api::clean_returnvalue(
            select_conversion::execute_returns(),
            $nothing
        );

        // Check it wrote.
        $mapitems = array_values($DB->get_records('local_gugrades_map_item'));
        $this->assertEquals($this->course->id, $mapitems[0]->courseid);
        $this->assertEquals($this->gradeitemidassign1, $mapitems[0]->gradeitemid);

        // Check it's set through the API
        $mapstuff = get_selected_conversion::execute($this->course->id, $this->gradeitemidassign1);
        $mapstuff = \external_api::clean_returnvalue(
            get_selected_conversion::execute_returns(),
            $mapstuff
        );

        $this->assertEquals('Test conversion map', $mapstuff['name']);
        $this->assertEquals('schedulea', $mapstuff['scale']);

        // Select the other map
        $nothing = select_conversion::execute($this->course->id, $this->gradeitemidassign1, $mapidb);
        $nothing = \external_api::clean_returnvalue(
            select_conversion::execute_returns(),
            $nothing
        );

        // Check it updated.
        $mapitems = array_values($DB->get_records('local_gugrades_map_item'));
        $this->assertCount(1, $mapitems);
        $this->assertEquals($mapidb, $mapitems[0]->mapid);

        // Remove maps
        $nothing = select_conversion::execute($this->course->id, $this->gradeitemidassign1, 0);
        $nothing = \external_api::clean_returnvalue(
            select_conversion::execute_returns(),
            $nothing
        );

        // Check it removed.
        $mapitems = array_values($DB->get_records('local_gugrades_map_item'));
        $this->assertCount(0, $mapitems);
    }
}
