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
 * Handles custom gradetypes all in one place
 */
class gradetype {

    /**
     * Define the different types of grade
     */
    private static function define() {
        $gradetypes = [
            'FIRST' => get_string('gradetypefirst', 'local_gugrades'),
            'SECOND' => get_string('gradetypesecond', 'local_gugrades'),
            'THIRD' => get_string('gradetypethird', 'local_gugrades'),
            'AGREED' => get_string('gradetypeagreed', 'local_gugrades'),
            'MODERATED' => get_string('gradetypemoderated', 'local_gugrades'),
            'LATE' => get_string('gradetypelate', 'local_gugrades'),
            'GOODCAUSE' => get_string('gradetypegoodcause', 'local_gugrades'),
            'CAPPED' => get_string('gradetypecapped', 'local_gugrades'),
            'CONDUCT' => get_string('gradetypeconduct', 'local_gugrades'),
            'OTHER' => get_string('gradetypeother', 'local_gugrades')
        ];

        return $gradetypes;
    }

    /**
     * Get description
     * @param string $gradetype
     * @return string
     */
    public static function get_description(string $gradetype) {
        $gradetypes = self::define();
        return $gradetypes[$gradetype] ?? '[[' . $gradetype . ']]';
    }

    /**
     * Get gradetypes for menu
     * @return array
     */
    public static function get_menu() {
        $gradetypes = self::define();
        
        // The menu doesn't include FIRST grades
        unset($gradetypes['FIRST']);

        return $gradetypes;
    }

}