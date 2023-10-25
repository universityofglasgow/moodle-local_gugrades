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
 * Language EN
 *
 * @package    local_gugrades
 * @copyright  2023
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_gugrades;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/grade/lib.php');

/**
 * Represents a single row of capture data for a user
 */
class usercapture {

    protected int $courseid;

    protected int $gradeitemid;

    protected int $userid;

    protected $grades;

    protected $provisional;

    protected $gradesbygradetype;

    protected $rules;

    protected bool $alert;

    /**
     * Constructor
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $userid
     */
    public function __construct(int $courseid, int $gradeitemid, int $userid) {
        $this->courseid = $courseid;
        $this->gradeitemid = $gradeitemid;
        $this->userid = $userid;

        $this->rules = new \local_gugrades\rules\base($this);

        $this->read_grades();
        $this->get_gradesbygradetype();
    }

    /**
     * Organise grades by gradetype.
     * For this we ignore OTHER types as they are not needed
     * (e.g. for applying grade rules)
     */
    protected function find_gradesbygradetype($grades) {
        $this->gradesbygradetype = [];
        foreach ($grades as $grade) {
            if ($grade->gradetype != 'OTHER') {
                $this->gradesbygradetype[$grade->gradetype] = $grade;
            }
        }
    }

    /**
     * Acquire and check grades in database
     *
     */
    private function read_grades() {
        global $DB;

        $this->provisional = null;

        $grades = $DB->get_records('local_gugrades_grade', [
            'gradeitemid' => $this->gradeitemid,
            'userid' => $this->userid,
            'iscurrent' => 1,
        ], 'audittimecreated ASC');

        // Work out / add provisional grade.
        if ($grades) {
            $provisionalcolumn = \local_gugrades\grades::get_column($this->courseid, $this->gradeitemid, 'PROVISIONAL');
            $provisional = $this->rules->get_provisional($grades);

            $provisional->columnid = $provisionalcolumn->id;
            $this->provisional = $provisional;
            $grades[] = $provisional;
        }

        // Organise by gradetype.
        $this->find_gradesbygradetype($grades);

        // Check if there should be an alert.
        $this->alert = $this->rules->is_alert();

        $this->grades = $grades;
    }

    /**
     * Get released grade
     *
     */
    public function get_released() {

        // Released grade is probably just the provisional grade,
        // but just in case there's something different...
        $released = $this->rules->get_released();

        return $released;
    }

    /**
     * Get the grade array
     * @return array
     */
    public function get_grades() {
        return $this->grades;
    }

    /**
     * Get the gradesbygradetype array
     * @return array
     */
    public function get_gradesbygradetype() {
        return $this->gradesbygradetype;
    }

    /**
     * Get provisional grade
     * @return object
     */
    public function get_provisional() {
        return $this->provisional;
    }

    /**
     * Get alert status
     * @return boolean
     *
     */
    public function alert() {
        return $this->alert;
    }

}
