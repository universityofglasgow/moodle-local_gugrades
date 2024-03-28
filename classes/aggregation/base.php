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
 * Base for aggregation class
 * This class defines basic functional logic.
 * It could be overriden for custom instances.
 *
 * @package    local_gugrades
 * @copyright  2024
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_gugrades\aggregation;

/**
 * aggregation 'rules'
 */
class base {

    /**
     * @var int $courseid
     */
    protected int $courseid;

    /**
     * Constructor
     * @param int $courseid
     */
    public function __construct(int $courseid) {
        $this->courseid = $courseid;
    }

    /**
     * Get the provisional/released grade
     * @param int $gradeitemid
     * @param int $userid
     * @return float, string
     */
    public function get_provisional(int $gradeitemid, int $userid) {
        global $DB;

        $grades = $DB->get_records('local_gugrades_grade', [
            'gradeitemid' => $gradeitemid,
            'userid' => $userid,
            'iscurrent' => 1,
        ], 'audittimecreated ASC');

        // Work out / add provisional grade.
        if ($grades) {
            $lastgrade = end($grades);
            $grade = $lastgrade->convertedgrade;
            $display = $lastgrade->displaygrade;

            return [$grade, $display];
        } else {
            return null;
        }
    }

}
