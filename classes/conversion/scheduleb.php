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
 * Conversion class for Schedule B
 *
 * @package    local_gugrades
 * @copyright  2024
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_gugrades\conversion;

/**
 * Handle Schedule B
 */
class scheduleb extends base {

    /**
     * @var array $scaleitems
     */
    protected array $scaleitems = [];

    /**
     * @var array $items
     */
    protected array $items = [];

    /**
     * Constructor. Get grade info
     * @param int $courseid
     * @param int $gradeitemid
     */
    public function __construct(int $courseid, int $gradeitemid) {
        global $DB;

        parent::__construct($courseid, $gradeitemid);

        // Get scale.
        $scale = $DB->get_record('scale', ['id' => $this->gradeitem->scaleid], '*', MUST_EXIST);
        $this->scaleitems = array_map('trim', explode(',', $scale->scale));

        // Get scale conversion.
        $items = $DB->get_records('local_gugrades_scalevalue', ['scaleid' => $this->gradeitem->scaleid]);
        foreach ($items as $item) {
            $this->items[$item->item] = $item->value;
        }
    }

    /**
     * Define scale mapping
     * @return array
     */
    public function get_map() {
        return [
            0 => 'H',
            2 => 'G0',
            5 => 'F0',
            8 => 'E0',
            11 => 'D0',
            14 => 'C0',
            17 => 'B0',
            22 => 'A0',
        ];
    }

    /**
     * Get the scale item
     * @param $
     */

    /**
     * Handle imported grade
     * Create both converted grade (actual value) and display grade
     * @param float $grade
     * @return [float, string]
     */
    public function import(float $grade) {
        global $DB;

        // Get scale (scales start at 1 not 0).
        if (isset($this->scaleitems[$grade - 1])) {
            $scaleitem = $this->scaleitems[$grade - 1];
        } else {
            throw new \moodle_exception('Scale item does not exist. Scale id = ' .
                $this->gradeitem->scaleid . ', value = ' . $grade);
        }

        // Convert to value using scalevalue.
        if (array_key_exists($scaleitem, $this->items)) {
            $converted = $this->items[$scaleitem];
        } else {
            throw new \moodle_exception('Scale item "' . $scaleitem . '" does not exist in scale id = ' .
                $this->gradeitem->scaleid);
        }

        return [$converted, $scaleitem];
    }

}
