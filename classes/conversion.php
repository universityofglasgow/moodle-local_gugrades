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
     * @param int $mapid
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
        $scaleitems = self::get_scale($schedule);

        // Unpack defaults
        $defaultpoints = array_map('trim', explode(',', $default));
        array_unshift($defaultpoints, 0);

        // Iterate over scale to add data
        $map = [];
        foreach ($scaleitems as $grade => $band) {
            
            // Get correct default point
            $default = array_shift($defaultpoints);
            if (is_null($default)) {
                $default = 0;
            }

            $map[] = [
                'band' => $band,
                'grade' => $grade,
                'bound' => $default,
            ];
        }

        return $map;
    }

        /**
     * Write conversion map, mapid=0 means a new one
     * @param int $courseid
     * @param int $mapid
     * @param string $name
     * @param string $schedule
     * @param float $maxgrade
     * @param array map
     * @return int
     */
    public static function write_conversion_map(int $courseid, int $mapid, string $name, string $schedule, float $maxgrade, array $map): int {
        global $DB, $USER;

        if ($mapid) {
            $mapinfo = $DB->get_record('local_gugrades_map', ['id' => $mapid], '*', MUST_EXIST);
            if ($courseid != $mapinfo->courseid) {
                throw new \moodle_exception('courseid does not match ' . $courseid);
            }

            // Write main record.
            // Name is the only thing you can change
            $mapinfo->name = $name;
            $mapinfo->timemodified = time();
            $DB->update_record('local_gugrades_map', $mapinfo);

            $newmapid = mapid;

        } else {
            $mapinfo = new \stdClass();
            $mapinfo->courseid = $courseid;
            $mapinfo->name = $name;
            $mapinfo->scale = $schedule;
            $mapinfo->userid = $USER->id;
            $mapinfo->timecreated = time();
            $mapinfo->timemodified = time();
            $newmapid = $DB->insert_record('local_gugrades_map', $mapinfo);

            foreach ($map as $item) {
                $value = new \stdClass();
                $value->mapid = $newmapid;
                $value->percentage = $item['bound'];
                $value->scalevalue = $item['grade'];
                $DB->insert_record('local_gugrades_map_value', $value);
            }
        }

        return $newmapid;
    }
}