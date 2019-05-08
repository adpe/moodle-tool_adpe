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
 * Show all Moodle users on a simple way.
 *
 * @package   tool_adpe
 * @copyright 2018, Adrian Perez <p.adrian@gmx.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$context = context_system::instance();
$coursename = format_string($SITE->fullname, true, array('context' => $context));
$PAGE->set_context($context);

require_login();
require_capability('tool/adpe:view', $context, $USER->id);

// Set up the page.
$url = new moodle_url('/admin/tool/adpe/index.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($coursename);
$PAGE->set_heading($coursename);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('header_simpledb', 'tool_adpe'));

$users = $DB->get_records_sql('SELECT id, firstname, lastname, email FROM {user}');
$cnt = 1;
foreach ($users as $user) {
    echo html_writer::start_div();
    echo html_writer::label(get_string('output_simpledb_usercnt', 'tool_adpe', $cnt), 'userinfo'.$cnt);
    echo html_writer::start_tag('p', array('class' => 'userinfo'.$cnt));
    echo html_writer::span(get_string('output_simpledb_firstname', 'tool_adpe', $user->firstname));
    echo html_writer::span(get_string('output_simpledb_lastname', 'tool_adpe', $user->lastname));
    echo html_writer::span(get_string('output_simpledb_email', 'tool_adpe', $user->email));
    echo html_writer::end_tag('p');
    echo html_writer::end_div();
    $cnt++;
}

echo $OUTPUT->footer();
