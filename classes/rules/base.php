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

/**
 * Base class for acquisition and aggregation rules
 */
class base {

    /**
     * @var int $courseid
     */
    protected int $courseid;


    /**
     * @var int $gradeitemid
     */
    protected int $gradeitemid;

    /**
     * @var object $gradeitem
     */
    protected $gradeitem;

    /**
     * @var \local_gugrades\usercapture $usercapture
     */
    protected $usercapture;

    /**
     * Constructor. Get grade info
     * @param \local_gugrades\usercapture $usercapture
     */
    public function __construct(\local_gugrades\usercapture $usercapture) {
        global $DB;

        $this->usercapture = $usercapture;
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
     * (Grades array is required as object field hasn't been assigned yet)
     * @param array $grades
     * @return mixed
     */
    public function get_provisional(array $grades) {

        // We're just going to assume that the grades are in ascending date order.
        if ($grades) {
            $provisional = clone end($grades);
            $provisional->gradetype = 'PROVISIONAL';

            return $provisional;
        } else {

            return null;
        }
    }

    /**
     * Get the released grade. For base this is exactly the same as provisional
     * @return object
     */
    public function get_released() {
        $released = $this->usercapture->get_provisional();
        if ($released) {
            $released->gradetype = 'RELEASED';
        }

        return $released;
    }

    /**
     * Determine if we need to place an alert on the capture row
     * For example, 1st and 2nd grade not matching plus no agreed grade
     * @return boolean
     */
    public function is_alert() {
        $gradesbygt = $this->usercapture->get_gradesbygradetype();

        // 1st, 2nd and 3rd grade have to agree
        // unless there is an agreed grade
        if (array_key_exists('AGREED', $gradesbygt)) {
            return false;
        }

        // The -1 if they don't exist (not existing is proxy for equal).
        $first = array_key_exists('FIRST', $gradesbygt) ? $gradesbygt['FIRST'] : -1;
        $second = array_key_exists('SECOND', $gradesbygt) ? $gradesbygt['SECOND'] : -1;
        $third = array_key_exists('THIRD', $gradesbygt) ? $gradesbygt['THIRD'] : -1;

        // Only 1st grade is acceptable.
        if (($second == -1) && ($third == -1)) {
            return false;
        }

        // Failing all of above, must agree.
        if (($first == $second) && ($second == $third)) {
            return false; // All equal.
        } else {
            return true; // Not all equal.
        }
    }

}
