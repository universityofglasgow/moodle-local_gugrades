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
    private int $courseid;

    /**
     * @var string $atype
     */

    /**
     * Constructor
     * @param int $courseid
     * @param string $atype
     */
    public function __construct(int $courseid, string $atype) {
        $this->courseid = $courseid;
        $this->atype = $atype;
    }

    /**
     * Pre-process grades for aggregation.
     * Allows grades to be 'normalised' prior to aggregation.
     * @param array $items
     * @return array
     */
    public function pre_process_items(array $items) {

        return $items;
    }

    /**
     * Calculate completion %age for items
     * Need to be "sympathetic" with rounding on this as
     * stuff will be blocked if completion != 100%
     *
     * Completion is...
     * (sum of weights of completed items) * 100 / (sum of all weights)
     *
     * NOTE: Points grades do NOT count. Admingrades do NOT count.
     * @param array $items
     * @return int
     */
    public function completion($items) {
        $totalweights = 0.0;
        $countall = 0;
        $totalcompleted = 0.0;
        $countcompleted = 0;

        foreach ($items as $item) {
            $totalweights += $item->weight;
            $countall++;
            if (!$item->grademissing && !$item->admingrade && $item->isscale) {
                $totalcompleted += $item->weight;
                $countcompleted++;
            }
        }

        // Calculation and rounding.
        // If $totalweights == 0 then there are no weights, then use
        // counts instead.
        if ($totalweights == 0) {
            $raw = $countcompleted * 100 / $countall;
        } else {
            $raw = $totalcompleted * 100 / $totalweights;
        }

        return round($raw, 0, PHP_ROUND_HALF_UP);
    }

    /**
     * Round to a specific number of decimal places.
     * Spec says 5, but giving the opportunity to change.
     * @param float $value
     * @return float
     */
    public function round_float(float $value) {
        return round($value, 5);
    }

    //
    // Following are functions for all the basic aggregation strategies. These mostly
    // replicate what core Moodle Gradebook does and are as specified in the Moodle docs.
    //

    /**
     * Choose aggregation strategy method
     * @param int $aggregationid
     * @return string
     */
    public function strategy_factory(int $aggregationid) {

        // Array defines which aggregation type calls which function.
        $lookup = [
            \GRADE_AGGREGATE_MEAN => 'mean',
            \GRADE_AGGREGATE_MEDIAN => 'median',
            \GRADE_AGGREGATE_MIN => 'min',
            \GRADE_AGGREGATE_MAX => 'max',
            \GRADE_AGGREGATE_MODE => 'mode',
            \GRADE_AGGREGATE_WEIGHTED_MEAN => 'weighted_mean',
            \GRADE_AGGREGATE_WEIGHTED_MEAN2 => 'weighted_mean2',
            \GRADE_AGGREGATE_EXTRACREDIT_MEAN => 'extracredit_mean',
            \GRADE_AGGREGATE_SUM => 'sum',
        ];
        if (array_key_exists($aggregationid, $lookup)) {
            $agf = $lookup[$aggregationid];
        } else {
            throw new \moodle_exception('Unknown aggregation strategy');
        }

        // TODO - force everything to me mean for testing, for now.
        $agf = 'mean';

        return "strategy_" .$agf;
    }

    /**
     * Establish the maximum grade according to $atype (the aggregated type)
     */
    protected function get_max_grade() {
        if (($this->atype == \local_gugrades\GRADETYPE_SCHEDULEA) || ($this->atype == \local_gugrades\GRADETYPE_SCHEDULEB)) {
            return 22;
        }
        if ($this->atype == \local_gugrades\GRADETYPE_POINTS) {
            return 100;
        }

        // If we get here, $atype was presumably ERROR (or something we don't know about).
        throw new \moodle_exception('Unhandled aggregation type - ' . $this->atype);
    }

    /**
     * Strategy - mean of grades
     * @param array $items
     * @return float
     */
    public function strategy_mean(array $items) {
        $sum = 0.0;
        $count = 0;
        $maxgrade = $this->get_max_grade();
        foreach ($items as $item) {
            $sum += $item->grade / $item->grademax;
            $count++;
        }

        return $this->round_float($sum * $maxgrade / $count);
    }

    /**
     * Convert numeric 0-22 to Schedule A
     * @param float $rawgrade
     * @return [string, int]
     */
    protected function convert_schedulea(float $rawgrade) {
        $schedulea = [
            0 => 'H',
            1 => 'G2',
            2 => 'G1',
            3 => 'F3',
            4 => 'F2',
            5 => 'F1',
            6 => 'E3',
            7 => 'E2',
            8 => 'E1',
            9 => 'D3',
            10 => 'D2',
            11 => 'D1',
            12 => 'C3',
            13 => 'C2',
            14 => 'C1',
            15 => 'B3',
            16 => 'B2',
            17 => 'B1',
            18 => 'A5',
            19 => 'A4',
            20 => 'A3',
            21 => 'A2',
            22 => 'A1',
        ];

        // This MATTERS - round the float rawgrade to an integer
        // "15.5 and all higher values less than 16.5 should become 16
        // [Guide to code of assessment].
        $grade = round($rawgrade, 0, PHP_ROUND_HALF_UP);

        if (!array_key_exists($grade, $schedulea)) {
            throw new \moodle_exception('Raw grade out of valid range - ' . $rawgrade);
        }

        return [$schedulea[$grade], $grade];
    }

    /**
     * Convert numeric 0-22 to Schedule B
     * @param float $rawgrade
     * @return [string, int]
     */
    protected function convert_scheduleb(float $rawgrade) {
        if ($rawgrade < 1) {
            return ['H', 0];
        } else if ($rawgrade < 3) {
            return ['G0', 2];
        } else if ($rawgrade < 6) {
            return ['F0', 5];
        } else if ($rawgrade < 9) {
            return ['E0', 8];
        } else if ($rawgrade < 12) {
            return ['D0', 11];
        } else if ($rawgrade < 15) {
            return ['C0', 14];
        } else if ($rawgrade < 18) {
            return ['B0', 17];
        } else if ($rawgrade <= 22) {
            return ['A0', 22];
        } else {
            throw new \moodle_exception('Raw grade out of valid range - ' . $rawgrade);
        }
    }

    /**
     * Convert float grade to Schedule A / B
     * @param float $rawgrade
     * @param string $atype
     * @return [string, int]
     */
    public function convert($rawgrade, $atype) {
        if ($atype == \local_gugrades\GRADETYPE_SCHEDULEA) {
            return $this->convert_schedulea($rawgrade, $atype);
        } else if ($atype == \local_gugrades\GRADETYPE_SCHEDULEB) {
            return $this->convert_scheduleb($rawgrade, $atype);
        } else {
            throw new \moodle_exception('Invalid atype - ' . $atype);
        }
    }

    /**
     * Which grade is 'passed up' from aggregation when converting to scale
     * The 'raw' grade or the graded point after conversion?
     * This is here in case there are different views about this
     * See MGU-821
     * @param float $rawgrade
     * @param int $gradepoint
     * @return float|int
     */
    public function get_grade_for_parent(float $rawgrade, int $gradepoint) {

        // Finger in the air - and use $gradepoint. If you want raw grade
        // just return the other value.
        return $gradepoint;
    }

    /**
     * Format displaygrade for Schedule A / B
     * @param string $convertedgrade
     * @param float $rawgrade
     * @param float $gradepoint
     * @return string
     */
    public function format_displaygrade(string $convertedgrade, float $rawgrade, float $gradepoint) {

        return $convertedgrade . " ($rawgrade)";
    }
}
