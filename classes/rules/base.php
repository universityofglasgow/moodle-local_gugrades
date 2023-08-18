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

namespace local_gugrades\rules;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for acquisition and aggregation rules
 */
class base {

    protected int $courseid;

    protected int $gradeitemid;

    protected $gradeitem;

    /**
     * Constructor. Get grade info
     * @param int $courseid
     * @param int $gradeitemid
     */
    public function __construct(int $courseid, int $gradeitemid) {
        global $DB;

        $this->courseid = $courseid;
        $this->gradeitemid = $gradeitemid;

        $this->gradeitem = $DB->get_record('grade_items', ['id' => $gradeitemid], '*', MUST_EXIST);
    }

    /**
     * Return name of rule set
     * @return string
     */
    public function get_name() {
        return get_string('rulesdefault', 'local_gugrades');
    }

    /**
     * Get the provisional grade given the array of active grades
     * Default is just to return the most recent one
     * TODO: Figure out what to do with admin grades
     * @param array $grades
     * @return object
     */
    public function get_provisional(array $grades) {

        // We're just going to assume that the grades are in ascending date order
        $provisional = clone end($grades);
        $provisional->gradetype = 'PROVISIONAL';

        return $provisional;
    }

}