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
     * @var object $gradecatsecond
     */
    protected $gradecatsecond;

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
     * @var int $gradeitemsecond1
     */
    protected int $gradeitemsecond1;

    /**
     * @var int $gradeitemsecond2
     */
    protected int $gradeitemsecond2;

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
     * @param string $type
     */
    protected function fill_scalevalue($scale, $scaleid, $type) {
        global $DB;

        $items = explode(',', $scale);
        foreach ($items as $value => $item) {
            $scalevalue = new \stdClass;
            $scalevalue->scaleid = $scaleid;
            $scalevalue->item = trim($item);
            $scalevalue->value = $value;
            $DB->insert_record('local_gugrades_scalevalue', $scalevalue);
        }

        $scaletype = new \stdClass;
        $scaletype->scaleid = $scaleid;
        $scaletype->type = $type;
        $DB->insert_record('local_gugrades_scaletype', $scaletype);
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
     * Enable/disable dashboard for MyGrades
     * @param int $courseid
     * @param bool $enable
     */
    protected function enable_dashboard(int $courseid, bool $enable) {
        global $DB;

        $value = $enable ? 1 : 0;
        if ($config = $DB->get_record('local_gugrades_config', ['courseid' => $courseid, 'name' => 'enabledashboard'])) {
            $config->value = $value;
            $DB->update_record('local_gugrades_config', $config);
        } else {
            $config = new \stdClass();
            $config->courseid = $courseid;
            $config->gradeitemid = 0;
            $config->name = 'enabledashboard';
            $config->value = $value;
            $DB->insert_record('local_gugrades_config', $config);
        }
    }

    /**
     * Enable/disable (old) GUGCAT dashboard
     * @param int $courseid
     * @param bool $enable
     */
    protected function enable_gugcat_dashboard(int $courseid, bool $enable) {
        global $DB;

        // Custom field category.
        if (!$customfieldcategory = $DB->get_record('customfield_category', ['name' => 'GCAT Options'])) {
            $customfieldcategory = new \stdClass();
            $customfieldcategory->name = 'GCAT Options';
            $customfieldcategory->component = 'core_course';
            $customfieldcategory->area = 'course';
            $customfieldcategory->timecreated = time();
            $customfieldcategory->timemodified = time();
            $customfieldcategoryid = $DB->insert_record('customfield_category', $customfieldcategory);
        } else {
            $customfieldcategoryid = $customfieldcategory->id;
        }

        // Create/Get custom field field id.
        if (!$customfieldfield = $DB->get_record('customfield_field', ['categoryid' => $customfieldcategoryid])) {
            $configdata = '{"required":"0","uniquevalues":"0","checkbydefault":"0","locked":"0","visibility":"0"}';
            $category = \core_customfield\category_controller::create($customfieldcategoryid);
            $field = \core_customfield\field_controller::create(0, (object)[
                'type' => 'checkbox',
                'configdata' => $configdata,
            ], $category);

            $handler = $field->get_handler();
            $handler->save_field_configuration($field, (object)[
                'name' => 'Show assessments on Student Dashboard',
                'shortname' => 'show_on_studentdashboard',
            ]);
            $fieldid = $field->get('id');
        } else {
            $fieldid = $customfieldfield->id;
        }

        // Course context.
        $context = \context_course::instance($courseid, MUST_EXIST);

        // Value to update/write.
        $value = $enable ? 1 : 0;

        // Update / insert field data.
        if ($data = $DB->get_record('customfield_data', ['fieldid' => $fieldid, 'instanceid' => $courseid])) {
            $data->intvalue = $value;
            $data->value = $value;
            $DB->update_record('customfield_data', $data);
        } else {
            $data = new \stdClass();
            $data->fieldid = $fieldid;
            $data->instanceid = $courseid;
            $data->intvalue = $value;
            $data->value = $value;
            $data->valueformat = 0;
            $data->timecreated = time();
            $data->timemodified = time();
            $data->contextid = $context->id;
            $DB->insert_record('customfield_data', $data);
        }

    }

    /**
     * Move grade item into specified category
     * @param int $gradeitemid
     * @param int $gradecategoryid
     */
    protected function move_gradeitem_to_category(int $gradeitemid, int $gradecategoryid) {
        global $DB;

        $gradeitem = $DB->get_record('grade_items', ['id' => $gradeitemid], '*', MUST_EXIST);
        $gradeitem->categoryid = $gradecategoryid;
        $DB->update_record('grade_items', $gradeitem);
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
        $this->fill_scalevalue($scaleitems, $scale->id, 'schedulea');

        // Add another scale.
        $scaleitemsb = 'H, G0, F0, E0, D0, C0, B0, A0';
        $scaleb = $this->getDataGenerator()->create_scale([
            'name' => 'UofG Schedule B',
            'scale' => $scaleitems,
            'courseid' => $course->id,
        ]);
        $this->fill_scalevalue($scaleitemsb, $scaleb->id, 'scheduleb');

        // Add a teacher to the course.
        // Teacher will be logged in unless changed in tests.
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $this->setUser($teacher);

        // Add some students to the course.
        $student = $this->getDataGenerator()->create_user(['idnumber' => '1234567', 'firstname' => 'Fred', 'lastname' => 'Bloggs']);
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $student2 = $this->getDataGenerator()->create_user(['idnumber' => '1234560', 'firstname' => 'Juan', 'lastname' => 'Perez']);
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');

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

        $this->course = $course;
        $this->teacher = $teacher;
        $this->student = $student;
        $this->student2 = $student2;
        $this->gradecatsumm = $gradecatsumm;
        $this->gradecatform = $gradecatform;
        $this->gradecatsecond = $gradecatsecond;
        $this->gradeitemsecond1 = $seconditem1->id;
        $this->gradeitemsecond2 = $seconditem2->id;
    }
}
