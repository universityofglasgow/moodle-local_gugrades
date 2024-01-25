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
 * Admin settings for local_gugrades
 *
 * @package    local_gugrades
 * @copyright  2023
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category('local_gugrades_settings', new lang_string('pluginname', 'local_gugrades')));
    $settingspage = new admin_settingpage('managelocalgugrades', new lang_string('manage', 'local_gugrades'));

    if ($ADMIN->fulltree) {
        require_once(dirname(__FILE__) . '/locallib.php');
        $settingspage->add(new admin_setting_heading('local_gugrades/headingscales',
            new lang_string('scalevalues', 'local_gugrades'),
            new lang_string('scalevaluesinfo', 'local_gugrades')));

        // Get current site-wide settings.
        $scales = $DB->get_records('scale', ['courseid' => 0]);
        foreach ($scales as $scale) {
            $name = "local_gugrades/scalevalue_" . $scale->id;

            $items = explode(',', $scale->scale);
            $default = '';
            $typedefault = '';

            // If id = 1 or 2 then leave them blank (built in scales).
            if ($scale->id > 2) {
                if (count($items) == 23) {
                    $values = range(0, 23);
                    $typedefault = 'schedulea';
                } else if (count($items) == 8) {
                    $values = [0, 2, 5, 8, 11, 14, 17, 22];
                    $typedefault = 'scheduleb';
                }
                foreach ($items as $item) {
                    $value = array_shift($values);
                    $default .= $item . ', ' . $value . PHP_EOL;
                }
            }
            $scalesetting = new admin_setting_configtextarea($name, $scale->name,
                new lang_string('scalevalueshelp', 'local_gugrades'),
                $default, PARAM_RAW, 30, count($items) + 1);
            $scalesetting->set_updatedcallback('scale_setting_updated');
            $settingspage->add($scalesetting);

            // Add option for type.
            $typename  = "local_gugrades/scaletype_" . $scale->id;
            $typesetting = new admin_setting_configtext($typename, $scale->name . ' type',
                new lang_string('scaletypehelp', 'local_gugrades'),
                $typedefault, PARAM_ALPHA, 25);
            $typesetting->set_updatedcallback('scale_setting_updated');
            $settingspage->add($typesetting);
        }
    }

    $ADMIN->add('localplugins', $settingspage);
}

