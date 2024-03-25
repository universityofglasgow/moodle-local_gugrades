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
 * Test functions around get_aggregation_page
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
 * Test(s) for get_aggregation_page webservice
 */
class get_aggregation_page_test extends \local_gugrades\external\gugrades_advanced_testcase {

    /**
     * @var int $gradeitemsecondx
     */
    protected int $gradeitemsecondx;

    /**
     * Called before every test
     */
    protected function setUp(): void {
        parent::setUp();

        // Final item has an invalid grade type.
        $seconditemx = $this->getDataGenerator()->create_grade_item(
            ['courseid' => $this->course->id, 'gradetype' => GRADE_TYPE_TEXT]
        );
        $this->move_gradeitem_to_category($seconditemx->id, $this->gradecatsecond->id);

        $this->gradeitemsecondx = $seconditemx->id;
    }

    /**
     * Checking basic (good) get page
     *
     * @covers \local_gugrades\external\get_aggregation_page::execute
     */
    public function test_basic_aggregation_page() {
        global $DB;

        // Make sure that we're a teacher.
        $this->setUser($this->teacher);

        // Import grades.
        $userlist = [
            $this->student->id,
            $this->student2->id,
        ];

        // Assign2 (which is useing scale).
        $status = import_grades_users::execute($this->course->id, $this->gradeitemidassign2, false, $userlist);
        $status = \external_api::clean_returnvalue(
            import_grades_users::execute_returns(),
            $status
        );

        // Get first csv test string.
        $page = get_aggregation_page::execute($this->course->id, $this->gradecatsumm->id, '', '', 0, false);
        $page = \external_api::clean_returnvalue(
            get_aggregation_page::execute_returns(),
            $page
        );

        var_dump($page);
    }

}
