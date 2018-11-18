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
 * This file contains the language strings of the plugin.
 *
 * @package   tool_adpe
 * @copyright 2018, Adrian Perez <p.adrian@gmx.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'My first Moodle admin plugin';
$string['sometext'] = 'This is my first try with mustache template.';
$string['header_courseid'] = '<h4>This is a simple text output with a variable:</h4>';
$string['output_courseid'] = 'The site was open from course with id: <b>{$a}.</b>';
$string['header_simpledb'] = '<h4>This is a simple DB output of all users on this Moodle platform:</h4>';
$string['output_simpledb_usercnt'] = '<b>User {$a}:</b><br />';
$string['output_simpledb_firstname'] = 'Firstname: {$a}<br />';
$string['output_simpledb_lastname'] = 'Lastname: {$a}<br />';
$string['output_simpledb_email'] = 'Email: {$a}';
$string['header_sqltable'] = '<h4>This is the plugin data table for this course:</h4>';
$string['output_sqltable_yes'] = "Yes";
$string['output_sqltable_no'] = "No";
