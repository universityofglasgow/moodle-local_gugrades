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
 * Conversion
 *
 * @package    local_gugrades
 * @copyright  2024
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_gugrades;

/**
 * Various functions related to conversion tasks
 */
class conversion {

    /**
     * Get schedule a/b mapping
     * @param string $schedule
     * @return object
     */
    protected static function get_scale(string $schedule) {

        // Get the name of the class and see if it exists.
        $classname = 'local_gugrades\\conversion\\' . $schedule;
        if (!class_exists($classname, true)) {
            throw new \moodle_exception('Unknown conversion class - "' . $classname . '"');
        }

        return $classname::get_map();
    }

    /**
     * Get maps for course
     * @param int $courseid
     * @return array
     */
    public static function get_maps(int $courseid): array {
        global $DB;

        $maps = $DB->get_records('local_gugrades_map', ['courseid' => $courseid]);

        return $maps;
    }

    /**
     * Is a map being used anywhere?
     * @param $mapid
     * @return bool
     */
    public static function inuse(int $mapid) {

        // TODO: Finish this
        return false;
    }

    /**
     * Return the default mapping for the given schedule
     * @param string $schedule
     * @return array
     */
    public static function get_default_map(string $schedule) {

        // Get correct default from settings
        if ($schedule == 'schedulea') {
            $default = get_config('local_gugrades', 'mapdefault_schedulea');
        } else if ($schedule == 'scheduleb') {
            $default = get_config('local_gugrades', 'mapdefault_scheduleb');
        } else {
            throw new \moodle_exception('Invalid schedule specified in get_default map - "' . $schedule . '"');
        }

        // Get scale
        $scale = self::get_scale($schedule);

        // Unpack defaults
        $defaultpoints = array_map('trim', explode(',', $default));

        var_dump($scale);
        var_dump($defaultpoints);
    }
}