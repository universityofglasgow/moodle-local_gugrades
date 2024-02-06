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
 * Define web services
 *
 * @package    local_gugrades
 * @copyright  2023
 * @author     Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$services = [
    'local_gugrades' => [
        'functions' => [
            'local_gugrades_get_levelonecategories',
            'local_gugrades_get_activities',
            'local_gugrades_get_user_grades',
            'local_gugrades_get_capture_page',
            'local_gugrades_get_grade_item',
            'local_gugrades_import_grade',
            'local_gugrades_import_grades_users',
            'local_gugrades_import_grades_recursive',
            'local_gugrades_get_user_picture_url',
            'local_gugrades_get_user_grades',
            'local_gugrades_get_history',
            'local_gugrades_get_audit',
            'local_gugrades_has_capability',
            'local_gugrades_is_grades_imported',
            'local_gugrades_get_all_strings',
            'local_gugrades_get_add_grade_form',
            'local_gugrades_get_gradetypes',
            'local_gugrades_write_additional_grade',
            'local_gugrades_save_settings',
            'local_gugrades_get_settings',
            'local_gugrades_dashboard_get_courses',
            'local_gugrades_dashboard_get_grades',
            'local_gugrades_release_grades',
            'local_gugrades_reset',
            'local_gugrades_get_groups',
            'local_gugrades_get_csv_download',
            'local_gugrades_get_capture_cell_form',
            'local_gugrades_write_column',
        ],
        'requiredcapability' => 'local/gugrades:view',
        'restrictedusers' => 1,
        'enabled' => 1,
    ],
];

$functions = [
    'local_gugrades_get_levelonecategories' => [
        'classname' => 'local_gugrades\external\get_levelonecategories',
        'description' => 'Gets first level categories (should be summative, formative and so on)',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_get_activities' => [
        'classname' => 'local_gugrades\external\get_activities',
        'description' => 'Gets individual activities or nth level categories (sub - category grades)',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_get_capture_page' => [
        'classname' => 'local_gugrades\external\get_capture_page',
        'description' => 'Get grade capture table given activity, ',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_get_grade_item' => [
        'classname' => 'local_gugrades\external\get_grade_item',
        'description' => 'Gets the activity information for a given grade item id',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_import_grade' => [
        'classname' => 'local_gugrades\external\import_grade',
        'description' => 'Import 1st grade from activity or grade item for given user',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_import_grades_users' => [
        'classname' => 'local_gugrades\external\import_grades_users',
        'description' => 'Import 1st grade from activity or grade item for list of users',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_gugrades_import_grades_recursive' => [
        'classname' => 'local_gugrades\external\import_grades_recursive',
        'description' => 'Import 1st grade from activity and all its peers and children.',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_gugrades_get_user_picture_url' => [
        'classname' => 'local_gugrades\external\get_user_picture_url',
        'description' => 'Get the URL of the user picture',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_get_user_grades' => [
        'classname' => 'local_gugrades\external\get_user_grades',
        'description' => 'Get all user grades for given user (site-wide)',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_get_history' => [
        'classname' => 'local_gugrades\external\get_history',
        'description' => 'Get the grade history for given user / grade item.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_get_audit' => [
        'classname' => 'local_gugrades\external\get_audit',
        'description' => 'Get the audit trail for given or current user.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_has_capability' => [
        'classname' => 'local_gugrades\external\has_capability',
        'description' => 'Check if current user has a given capability.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_is_grades_imported' => [
        'classname' => 'local_gugrades\external\is_grades_imported',
        'description' => 'Check if selected grade item has had any grades imported.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_get_all_strings' => [
        'classname' => 'local_gugrades\external\get_all_strings',
        'description' => 'Load all the strings for this plugin.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_get_add_grade_form' => [
        'classname' => 'local_gugrades\external\get_add_grade_form',
        'description' => 'Get the stuff to construct the add grade form.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_get_capture_cell_form' => [
        'classname' => 'local_gugrades\external\get_capture_cell_form',
        'description' => 'Get the stuff to construct the small forms for bulk grade editing.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_get_gradetypes' => [
        'classname' => 'local_gugrades\external\get_gradetypes',
        'description' => 'Get list of gradetypes (in menu format).',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_write_additional_grade' => [
        'classname' => 'local_gugrades\external\write_additional_grade',
        'description' => 'Write a new grade for a given grade item / user.',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_gugrades_save_settings' => [
        'classname' => 'local_gugrades\external\save_settings',
        'description' => 'Save the settings page.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/gugrades:changesettings',
    ],
    'local_gugrades_get_settings' => [
        'classname' => 'local_gugrades\external\get_settings',
        'description' => 'Get/read the settings page.',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => 'local/gugrades:changesettings',
    ],
    'local_gugrades_dashboard_get_courses' => [
        'classname' => 'local_gugrades\external\dashboard_get_courses',
        'description' => 'Get the list of courses and top-level categories for give user.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_dashboard_get_grades' => [
        'classname' => 'local_gugrades\external\dashboard_get_grades',
        'description' => 'Get the list of grades for a given user and grade category. Also returns sub-categories (if any).',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_release_grades' => [
        'classname' => 'local_gugrades\external\release_grades',
        'description' => 'Get the list of grades for a given user and grade category. Also returns sub-categories (if any).',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_gugrades_reset' => [
        'classname' => 'local_gugrades\external\reset',
        'description' => 'Completely deletes MyGrades data for given course. Process cannot be undone!',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_gugrades_get_groups' => [
        'classname' => 'local_gugrades\external\get_groups',
        'description' => 'Get the list of groups for the course (if any).',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_get_csv_download' => [
        'classname' => 'local_gugrades\external\get_csv_download',
        'description' => 'Get the contents of CSV add-grades pro-forma.',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_gugrades_upload_csv' => [
        'classname' => 'local_gugrades\external\upload_csv',
        'description' => 'Upload / test csv add grades in bulk.',
        'type' => 'write',
        'ajax' => true,
    ],
    'local_gugrades_write_column' => [
        'classname' => 'local_gugrades\external\write_column',
        'description' => 'Create column in capture table (if not existing).',
        'type' => 'write',
        'ajax' => true,
    ],
];
