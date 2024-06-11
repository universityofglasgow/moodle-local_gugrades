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

}
