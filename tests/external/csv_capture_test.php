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
 * Test functions around csv n capture page
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
 * Test(s) for get_all_strings webservice
 */
class csv_capture_test extends \local_gugrades\external\gugrades_advanced_testcase {

    /**
     * @var array $uploaddata1
     */
    private $uploaddata1 = [
        [
            "Name",
            "ID number",
            "Grade"
        ],
        [
            "Fred Bloggs",
            "1234567",
            "F2:4"
        ],
        [
            "Juan Perez",
            "1234560",
            "A5:18"
        ]
    ];

    /**
     * @var array $uploaddata2
     */
    private $uploaddata2 = [
        [
            "Name",
            "ID number",
            "Grade"
        ],
        [
            "Fred Bloggs",
            "123456A",
            "F2:4"
        ],
        [
            "Juan Perez",
            "1234560",
            "A5:20"
        ]
    ];

    /**
     * Create csv from array
     * @param array $lines
     * @return string
     */
    protected function make_csv(array $lines) {
        $csv = '';
        foreach ($lines as $line) {
            $csv .= implode(', ', $line) . PHP_EOL;
        }

        return $csv;
    }

    /**
     * Check that CSV contents are returned
     *
     * @covers \local_gugrades\external\get_csv_download::execute
     */
    public function test_return_csv() {

        // Make sure that we're a teacher
        $this->setUser($this->teacher);

        // Check for one of the assignments
        $csv = get_csv_download::execute($this->course->id, $this->gradeitemidassign1, 0);
        $csv = \external_api::clean_returnvalue(
            get_csv_download::execute_returns(),
            $csv
        );

        // Parse csv (sort of - I know!)
        $data = \local_gugrades\api::unpack_csv($csv['csv']);

        $this->assertCount(3, $data);
        $this->assertEquals('1234560', $data[2][1]);
        $this->assertEquals('1234567', $data[1][1]);
        $this->assertEquals('Fred Bloggs', $data[1][0]);
    }

    /**
     * Checking sending CSV file
     *
     * @covers \local_gugrades\external\upload_csv::execute
     */
    public function test_upload_csv() {

        // Make sure that we're a teacher
        $this->setUser($this->teacher);

        // Get first csv test string
        $csv = $this->make_csv($this->uploaddata1);
        $lines = upload_csv::execute($this->course->id, $this->gradeitemidassign2, 0, true, 'SECOND', $csv);
        $lines = \external_api::clean_returnvalue(
            upload_csv::execute_returns(),
            $lines
        );

        $this->assertCount(2, $lines);
        $this->assertEquals(4, $lines[0]['gradevalue']);
        $this->assertEquals(18, $lines[1]['gradevalue']);

        // Get second (with errors) csv test string
        $csv = $this->make_csv($this->uploaddata2);
        $lines = upload_csv::execute($this->course->id, $this->gradeitemidassign2, 0, true, 'SECOND', $csv);
        $lines = \external_api::clean_returnvalue(
            upload_csv::execute_returns(),
            $lines
        );

        $this->assertCount(2, $lines);
        $this->assertEquals(get_string('csvidinvalid', 'local_gugrades'), $lines[0]['error']);
        $this->assertEquals(get_string('csvgradeinvalid', 'local_gugrades'), $lines[1]['error']);
    }
}
