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

        // Add created by and created at.
        foreach ($maps as $map) {
            if ($user = $DB->get_record('user', ['id' => $map->userid])) {
                $map->createdby = fullname($user);
            } else {
                $map->createdby = '-';
            }
            $map->createdat = userdate($map->timecreated, get_string('strftimedatetimeshort', 'langconfig'));
        }

        return $maps;
    }

    /**
     * Is a map being used anywhere?
     * @param int $mapid
     * @return bool
     */
    public static function inuse(int $mapid) {
        global $DB;

        return $DB->record_exists('local_gugrades_map_item', ['mapid' => $mapid]);
    }

    /**
     * Return the default mapping for the given schedule
     * @param string $schedule
     * @return array
     */
    public static function get_default_map(string $schedule) {

        // Get correct default from settings.
        if ($schedule == 'schedulea') {
            $default = get_config('local_gugrades', 'mapdefault_schedulea');
        } else if ($schedule == 'scheduleb') {
            $default = get_config('local_gugrades', 'mapdefault_scheduleb');
        } else {
            throw new \moodle_exception('Invalid schedule specified in get_default map - "' . $schedule . '"');
        }

        // Get scale.
        $scaleitems = self::get_scale($schedule);

        // Unpack defaults.
        $defaultpoints = array_map('trim', explode(',', $default));
        array_unshift($defaultpoints, 0);

        // Iterate over scale to add data.
        $map = [];
        foreach ($scaleitems as $grade => $band) {

            // Get correct default point.
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
     * Get existing map for edit page
     * @param int $mapid
     * @return array
     */
    public static function get_map_for_editing(int $mapid) {
        global $DB;

        $mapinfo = $DB->get_record('local_gugrades_map', ['id' => $mapid], '*', MUST_EXIST);
        $mapvalues = $DB->get_records('local_gugrades_map_value', ['mapid' => $mapid], 'scalevalue ASC');

        $map = [];
        foreach ($mapvalues as $mapvalue) {
            $map[] = [
                'band' => $mapvalue->band,
                'grade' => $mapvalue->scalevalue,
                'bound' => $mapvalue->percentage,
            ];
        }

        return [
            'name' => $mapinfo->name,
            'schedule' => $mapinfo->scale,
            'maxgrade' => $mapinfo->maxgrade,
            'inuse' => self::inuse($mapid),
            'map' => $map,
        ];
    }

    /**
     * Find unique name
     * Add (n) on the end until it is
     * @param string $name
     * @return string
     */
    protected static function unique_name(string $name) {
        global $DB;

        $sql = 'select * from {local_gugrades_map} where ' . $DB->sql_compare_text('name') . ' = :name';
        if (!$DB->record_exists_sql($sql, ['name' => $name])) {
            return $name;
        }

        $count = 1;
        while ($DB->record_exists_sql($sql, ['name' => $name . ' (' . $count . ')'])) {
            $count++;
        }

        return $name . '(' . $count . ')';
    }

    /**
     * Write conversion map, mapid=0 means a new one
     * @param int $courseid
     * @param int $mapid
     * @param string $name
     * @param string $schedule
     * @param float $maxgrade
     * @param array $map
     * @return int
     */
    public static function write_conversion_map(
        int $courseid, int $mapid, string $name, string $schedule, float $maxgrade, array $map): int {
        global $DB, $USER;

        // Check schedule.
        if (($schedule != 'schedulea') && ($schedule != 'scheduleb')) {
            throw new \moodle_exception('Schedule parameter must be "schedulea" or "scheduleb".');
        }

        if ($mapid) {
            $mapinfo = $DB->get_record('local_gugrades_map', ['id' => $mapid], '*', MUST_EXIST);
            if ($courseid != $mapinfo->courseid) {
                throw new \moodle_exception('courseid does not match ' . $courseid);
            }

            // Write main record.
            // Name is the only thing you can change.
            $mapinfo->name = self::unique_name($name);
            $mapinfo->timemodified = time();
            $DB->update_record('local_gugrades_map', $mapinfo);

            foreach ($map as $item) {
                if ($value = $DB->get_record('local_gugrades_map_value', ['mapid' => $mapid, 'scalevalue' => $item['grade']])) {
                    $value->percentage = $item['bound'];
                    $value->scalevalue = $item['grade'];
                    $DB->update_record('local_gugrades_map_value', $value);
                }
            }

            $newmapid = $mapid;

        } else {
            $mapinfo = new \stdClass();
            $mapinfo->courseid = $courseid;
            $mapinfo->name = self::unique_name($name);
            $mapinfo->scale = $schedule;
            $mapinfo->maxgrade = $maxgrade;
            $mapinfo->userid = $USER->id;
            $mapinfo->timecreated = time();
            $mapinfo->timemodified = time();
            $newmapid = $DB->insert_record('local_gugrades_map', $mapinfo);

            foreach ($map as $item) {
                $value = new \stdClass();
                $value->mapid = $newmapid;
                $value->band = $item['band'];
                $value->percentage = $item['bound'];
                $value->scalevalue = $item['grade'];
                $DB->insert_record('local_gugrades_map_value', $value);
            }
        }

        return $newmapid;
    }

    /**
     * Delete conversion map
     * @param int $courseid
     * @param int $mapid
     * @return bool
     */
    public static function delete_conversion_map(int $courseid, int $mapid) {
        global $DB;

        // Can't delete if it's being used.
        if (self::inuse($mapid)) {
            return false;
        }

        $mapinfo = $DB->get_record('local_gugrades_map', ['id' => $mapid], '*', MUST_EXIST);
        if ($courseid != $mapinfo->courseid) {
            throw new \moodle_exception('courseid does not match ' . $courseid);
        }

        $DB->delete_records('local_gugrades_map_value', ['mapid' => $mapid]);
        $DB->delete_records('local_gugrades_map', ['id' => $mapid]);

        return true;
    }


    /**
     * Import conversion map (as a new one)
     * @param int $courseid
     * @param string $jsonmap
     * @return int
     */
    public static function import_conversion_map(int $courseid, string $jsonmap) {

        // Is JSON valid?
        if (!$mapinfo = json_decode($jsonmap, true)) {
            throw new \moodle_exception('Invalid JSON');
        }

        // Sanity checks.
        if (!array_key_exists('map', $mapinfo) || !array_key_exists('name', $mapinfo) || !array_key_exists('schedule', $mapinfo)) {
            throw new \moodle_exeption('Required fields missing in JSON');
        }

        $map = $mapinfo['map'];

        $mapid = self::write_conversion_map($courseid, 0, $mapinfo['name'], $mapinfo['schedule'], $mapinfo['maxgrade'], $map);

        return $mapid;
    }

    /**
     * Select conversion (map).
     * @param int $courseid
     * @param int $gradeitemid
     * @param int $mapid
     */
    public static function select_conversion(int $courseid, int $gradeitemid, int $mapid) {
        global $DB, $USER;

        $mapinfo = $DB->get_record('local_gugrades_map', ['id' => $mapid], '*', MUST_EXIST);
        if ($courseid != $mapinfo->courseid) {
            throw new \moodle_exception('courseid does not match ' . $courseid);
        }

        // Set link to this map.
        if (!$mapitem = $DB->get_record('local_gugrades_map_item', ['mapid' => $gradeitemid, 'gradeitemid' => $gradeitemid])) {
            $mapitem = new \stdClass();
            $mapitem->courseid = $courseid;
            $mapitem->mapid = $mapid;
            $mapitem->gradeitemid = $gradeitemid;
            $mapitem->maxgrade = $mapinfo->maxgrade;
            $mapitem->userid = $USER->id;
            $mapitem->timemodified = time();
            $DB->insert_record('local_gugrades_map_item', $mapitem);
        }
    }
}
