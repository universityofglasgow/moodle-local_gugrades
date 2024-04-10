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
 * Custom advanced_testcase which includes setting up the course, activities and gradebook
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
class gugrades_advanced_testcase extends gugrades_base_testcase {

    /**
     * @var object $gradcatsumm
     */
    protected $gradecatsumm;

    /**
     * @var object $gradecatform
     */
    protected $gradecatform;

    /**
     * @var object $gradecatsecond
     */
    protected $gradecatsecond;

    /**
     * @var int $gradeitemidassign1
     */
    protected int $gradeitemidassign1;

    /**
     * @var int $gradeitemidassign2
     */
    protected int $gradeitemidassign2;

    /**
     * @var int $gradeitemsecond1
     */
    protected int $gradeitemsecond1;

    /**
     * @var int $gradeitemsecond2
     */
    protected int $gradeitemsecond2;


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

        // Add grade categories.
        $gradecatsumm = $this->getDataGenerator()->create_grade_category(['courseid' => $course->id, 'fullname' => 'Summative']);
        $gradecatform = $this->getDataGenerator()->create_grade_category(['courseid' => $course->id, 'fullname' => 'Formative']);

        // Add some assignments.
        $assign1 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $assign2 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $assign3 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // Get gradeitemids.
        $this->gradeitemidassign1 = $this->get_grade_item('', 'assign', $assign1->id);
        $this->gradeitemidassign2 = $this->get_grade_item('', 'assign', $assign2->id);
        $this->gradeitemidassign3 = $this->get_grade_item('', 'assign', $assign3->id);

        // Modify assignment 2 to use scale.
        $gradeitem2 = $DB->get_record('grade_items', ['id' => $this->gradeitemidassign2], '*', MUST_EXIST);
        $gradeitem2->gradetype = GRADE_TYPE_SCALE;
        $gradeitem2->grademax = 23.0;
        $gradeitem2->grademin = 1.0;
        $gradeitem2->scaleid = $scale->id;
        $DB->update_record('grade_items', $gradeitem2);

        // Modify assignment 3 to grade out of 23.
        $gradeitem3 = $DB->get_record('grade_items', ['id' => $this->gradeitemidassign3], '*', MUST_EXIST);
        $gradeitem3->grademax = 23.0;
        $gradeitem3->grademin = 0.0;
        $DB->update_record('grade_items', $gradeitem3);

        // Add assignment grades.
        $this->add_assignment_grade($assign1->id, $student->id, 95.5);
        $this->add_assignment_grade($assign1->id, $student2->id, 33);
        $this->add_assignment_grade($assign2->id, $student->id, 21);
        $this->add_assignment_grade($assign2->id, $student2->id, 11);
        $this->add_assignment_grade($assign3->id, $student->id, 0);
        $this->add_assignment_grade($assign3->id, $student2->id, 23.0);

        // Move the assignments to summative grade category (we only have one course).
        $items = $DB->get_records('grade_items', ['courseid' => $course->id, 'itemmodule' => 'assign']);
        foreach ($items as $item) {
            $item->categoryid = $gradecatsumm->id;
            $DB->update_record('grade_items', $item);
        }

        // Create a "second level" grade category and pu some iems in it.
        $gradecatsecond = $this->getDataGenerator()->create_grade_category(
            ['courseid' => $course->id, 'fullname' => 'Second Level', 'parent' => $gradecatsumm->id]);
        $seconditem1 = $this->getDataGenerator()->create_grade_item(['courseid' => $course->id, 'fullname' => 'Second item 1']);
        $this->move_gradeitem_to_category($seconditem1->id, $gradecatsecond->id);
        $seconditem2 = $this->getDataGenerator()->create_grade_item(['courseid' => $course->id, 'fullname' => 'Second item 2']);
        $this->move_gradeitem_to_category($seconditem2->id, $gradecatsecond->id);

        $this->gradecatsumm = $gradecatsumm;
        $this->gradecatform = $gradecatform;
        $this->gradecatsecond = $gradecatsecond;
        $this->gradeitemsecond1 = $seconditem1->id;
        $this->gradeitemsecond2 = $seconditem2->id;
    }
}
