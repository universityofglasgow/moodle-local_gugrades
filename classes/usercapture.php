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

    protected $gradeitemid;

    protected $userid;

    protected $grades;

    /**
     * Constructor
     * @param int $gradeitemid
     * @param int $userid
     */
    public function __construct(int $gradeitemid, $userid) {
        $this->gradeitemid = $gradeitemid;
        $this->userid = $userid;

        $this->read_grades();
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

        $this->grades = $grades;
    }

    /**
     * Get the grade array
     * @return array
     */
    public function get_grades() {
        return $this->grades;
    }

}