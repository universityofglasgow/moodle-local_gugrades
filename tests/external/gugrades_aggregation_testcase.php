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
 * Custom advanced_testcase which sets up (complex) gradebook schemas and data for
 * aggregation testing
 * @package    local_gugrades
 * @copyright  2023
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_gugrades\external;

use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/local/gugrades/tests/external/gugrades_base_testcase.php');

/**
 * Test(s) for (both) save_settings and get_settings webservices
 */
class gugrades_aggregation_testcase extends gugrades_base_testcase {

    /**
     * @var array $gradeitems
     */
    protected array $gradeitems;

    /**
     * Process schema json (recursive)
     * $gradeitemid specifies where to put new grade items
     * @param array $items
     * @param int $gradeitemid
     */
    protected function build_schema(array $items, int $gradeitemid = null) {
        global $DB;

        $this->gradeitems = [];

        foreach ($items as $item) {

            // Is it a grade item?
            if (!$item->category) {
                $gradeitem = $this->getDataGenerator()->create_grade_item(
                    ['courseid' => $this->course->id, 'itemname' => $item->name]
                );
                $this->move_gradeitem_to_category($gradeitem->id, $gradeitemid);
                $this->gradeitems[] = $gradeitem;
            } else {

                // In which case it must be a grade category.
                $gradecategory = $this->getDataGenerator()->create_grade_category(
                    ['courseid' => $this->course->id, 'fullname' => $item->name, 'parent' => $gradeitemid]
                );

                // Create child items (if present).
                if (!empty($item->children)) {
                    $this->build_schema($item->children, $gradecategory->id);
                }
            }
        }
    }

    /**
     * Import json grades schema
     * Returns array of gradeitemids (probably need to run import and such)
     * @param string $name
     * @return array
     */
    public function load_schema(string $name) {
        global $CFG, $DB;

        $path = $CFG->dirroot . '/local/gugrades/tests/external/gradedata/' . $name . '.json';
        $filecontents = file_get_contents($path);

        $json = json_decode($filecontents);
        $this->build_schema($json);

        // Get gradeitems.
        $gradeitems = $DB->get_records('grade_items', ['itemtype' => 'manual']);
        return array_column($gradeitems, 'id');
    }

    /**
     * Write a grade_grade
     * One would think there should be an API for this but I can't find
     * anything that makes sense...
     * @param object $gradeitem
     * @param int $userid
     * @param float $rawgrade
     */
    protected function write_grade_grades(object $gradeitem, int $userid, float $rawgrade) {
        global $DB;

        $grade = new \stdClass();
        $grade->itemid = $gradeitem->id;
        $grade->userid = $userid;
        $grade->rawgrade = $rawgrade;
        $grade->finalgrade = $rawgrade;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = 'UnitTest grade';
        $grade->informationformat = FORMAT_PLAIN;
        $grade->feedback = 'UnitTest Feedback';
        $grade->feedbackformat = FORMAT_PLAIN;

        $DB->insert_record('grade_grades', $grade);
    }

    /**
     * Get grade category id given name of category
     * @param string $catname
     * @return int
     *
     */
    public function get_grade_category(string $catname) {
        global $DB;

        $gcat = $DB->get_record('grade_categories', ['fullname' => $catname], '*', MUST_EXIST);

        return $gcat->id;
    }

    /**
     * Import json data
     * Data refers to item names already uploaded in the schema,
     * so make sure the data matches the schema!
     * Data is imported for a single user
     * @param string $name
     * @param int $userid
     */
    public function load_data(string $name, int $userid) {
        global $CFG, $DB;

        $path = $CFG->dirroot . '/local/gugrades/tests/external/gradedata/' . $name . '.json';
        $filecontents = file_get_contents($path);

        $json = json_decode($filecontents);

        foreach ($json as $item) {

            // Look up grade item just using name
            // There's only one course, anyway.
            $gradeitem = $DB->get_record('grade_items', ['itemname' => $item->item], '*', MUST_EXIST);
            $this->write_grade_grades($gradeitem, $userid, $item->grade);
        }
    }


    /**
     * Called before every test
     * This adds example GradeBook and activity data for many of the tests.
     */
    protected function setUp(): void {
        global $DB;

        parent::setUp();
        $this->resetAfterTest(true);

        $course = $this->course;
        $scale = $this->scale;
        $scaleb = $this->scaleb;
        $student = $this->student;
        $student2 = $this->student2;
        $teacher = $this->teacher;
    }
}
