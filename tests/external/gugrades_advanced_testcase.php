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

/**
 * Test(s) for (both) save_settings and get_settings webservices
 */
class gugrades_advanced_testcase extends externallib_advanced_testcase {

    /**
     * @var object $course
     */
    protected $course;

    /**
     * @var object $gradcatsumm
     */
    protected $gradecatsumm;

    /**
     * @var object $gradecatform
     */
    protected $gradecatform;

    /**
     * @var object $teacher
     */
    protected $teacher;

    /**
     * @var object $student
     */
    protected $student;

    /**
     * @var object $student
     */
    protected $student2;

    /**
     * @var int $gradeitemidassign1
     */
    protected int $gradeitemidassign1;

    /**
     * @var int $gradeitemidassign2
     */
    protected int $gradeitemidassign2;

    /**
     * Get gradeitemid
     * @param string $itemtype
     * @param string $itemmodule
     * @param int $iteminstance
     * @return int
     */
    protected function get_grade_item(string $itemtype, string $itemmodule, int $iteminstance) {
        global $DB;

        $params = [
            'iteminstance' => $iteminstance,
        ];
        if ($itemtype) {
            $params['itemtype'] = $itemtype;
        }
        if ($itemmodule) {
            $params['itemmodule'] = $itemmodule;
        }
        $gradeitem = $DB->get_record('grade_items', $params, '*', MUST_EXIST);

        return $gradeitem->id;
    }

    /**
     * Fill local_gugrades_scalevalue table
     * @param string $scale
     * @param int $scaleid
     */
    protected function fill_scalevalue($scale, $scaleid) {
        global $DB;

        $items = explode(',', $scale);
        foreach ($items as $value => $item) {
            $scalevalue = new \stdClass;
            $scalevalue->scaleid = $scaleid;
            $scalevalue->item = trim($item);
            $scalevalue->value = $value;
            $DB->insert_record('local_gugrades_scalevalue', $scalevalue);
        }
    }

    /**
     * Add assignment grade
     * @param int $assignid
     * @param int $studentid
     * @param float $gradeval
     */
    protected function add_assignment_grade(int $assignid, int $studentid, float $gradeval) {
        global $USER, $DB;

        $submission = new \stdClass();
        $submission->assignment = $assignid;
        $submission->userid = $studentid;
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $submission->latest = 0;
        $submission->attemptnumber = 0;
        $submission->groupid = 0;
        $submission->timecreated = time();
        $submission->timemodified = time();
        $DB->insert_record('assign_submission', $submission);

        $grade = new \stdClass();
        $grade->assignment = $assignid;
        $grade->userid = $studentid;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->grader = $USER->id;
        $grade->grade = $gradeval;
        $grade->attemptnumber = 0;
        $DB->insert_record('assign_grades', $grade);
    }

    /**
     * Called before every test
     */
    protected function setUp(): void {
        global $DB;

        parent::setUp();
        $this->resetAfterTest(true);

        // Create a course to apply settings to.
        $course = $this->getDataGenerator()->create_course();

        // Add a scale.
        $scaleitems = 'H:0, G2:1, G1:2, F3:3, F2:4, F1:5, E3:6, E2:7, E1:8, D3:9, D2:10, D1:11,
            C3:12, C2:13, C1:14, B3:15, B2:16, B1:17, A5:18, A4:19, A3:20, A2:21, A1:22';
        $scale = $this->getDataGenerator()->create_scale([
            'name' => 'UofG 22 point scale',
            'scale' => $scaleitems,
            'courseid' => $course->id,
        ]);
        $this->fill_scalevalue($scaleitems, $scale->id);

        // Add a teacher to the course.
        // Teacher will be logged in unless changed in tests.
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $this->setUser($teacher);

        // Add some students to the course.
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $student2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');

        // Add grade categories.
        $gradecatsumm = $this->getDataGenerator()->create_grade_category(['courseid' => $course->id, 'fullname' => 'Summative']);
        $gradecatform = $this->getDataGenerator()->create_grade_category(['courseid' => $course->id, 'fullname' => 'Formative']);

        // Add some assignments.
        $assign1 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $assign2 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);

        // Get gradeitemids.
        $this->gradeitemidassign1 = $this->get_grade_item('', 'assign', $assign1->id);
        $this->gradeitemidassign2 = $this->get_grade_item('', 'assign', $assign2->id);

        // Modify assignment 2 to use scale.
        $gradeitem2 = $DB->get_record('grade_items', ['id' => $this->gradeitemidassign2], '*', MUST_EXIST);
        $gradeitem2->gradetype = GRADE_TYPE_SCALE;
        $gradeitem2->grademax = 23.0;
        $gradeitem2->grademin = 1.0;
        $gradeitem2->scaleid = $scale->id;
        $DB->update_record('grade_items', $gradeitem2);

        // Add assignment grades.
        $this->add_assignment_grade($assign1->id, $student->id, 95.5);
        $this->add_assignment_grade($assign1->id, $student2->id, 33);
        $this->add_assignment_grade($assign2->id, $student->id, 21);
        $this->add_assignment_grade($assign2->id, $student2->id, 11);

        // Move the assignments to summative grade category (we only have one course).
        $items = $DB->get_records('grade_items', ['courseid' => $course->id, 'itemmodule' => 'assign']);
        foreach ($items as $item) {
            $item->categoryid = $gradecatsumm->id;
            $DB->update_record('grade_items', $item);
        }

        $this->course = $course;
        $this->teacher = $teacher;
        $this->student = $student;
        $this->student2 = $student2;
        $this->gradecatsumm = $gradecatsumm;
        $this->gradecatform = $gradecatform;
    }
}
