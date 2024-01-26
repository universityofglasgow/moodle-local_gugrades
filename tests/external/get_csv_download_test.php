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
 * Test get_csv_download
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
class get_csv_download_test extends \local_gugrades\external\gugrades_advanced_testcase {

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
        $data = str_getcsv($csv['csv']);

        $this->assertCount(7, $data);
        $this->assertEquals('1234560', $data[3]);
    }
}
