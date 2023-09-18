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

    protected $courseid;
    
    protected $gradeitemid;

    protected $userid;

    protected $grades;

    protected $gradesbygradetype;

    protected $rules;

    protected $alert;

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

        $this->rules = new \local_gugrades\rules\base($courseid, $gradeitemid);

        $this->read_grades();
    }

    /**
     * Organise grades by gradetype.
     * For this we ignore OTHER types as they are not needed
     * (e.g. for applying grade rules)
     * @param array $grades
     */
    protected function get_gradesbygradetype($grades) {
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

        $grades = $DB->get_records('local_gugrades_grade', [
            'gradeitemid' => $this->gradeitemid,
            'userid' => $this->userid,
            'iscurrent' => 1,
        ], 'audittimecreated ASC');

        // Work out / add provisional grade
        if ($grades) {
            $provisionalcolumn = \local_gugrades\grades::get_column($this->courseid, $this->gradeitemid, 'PROVISIONAL');
            $provisional = $this->rules->get_provisional($grades);
            
            $provisional->columnid = $provisionalcolumn->id;
            $grades[] = $provisional;
        }

        // organise by gradetype
        $this->get_gradesbygradetype($grades);

        // Check if there should be an alert
        $this->alert = $this->rules->is_alert($this->gradesbygradetype);

        $this->grades = $grades;
    }

    /**
     * Get the grade array
     * @return array
     */
    public function get_grades() {
        return $this->grades;
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