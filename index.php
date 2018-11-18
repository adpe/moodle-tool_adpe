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
 * @package   tool_adpe
 * @copyright 2018, Adrian Perez <p.adrian@gmx.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('tool_adpe');

$url = new moodle_url('/admin/tool/adpe/index.php');
$title = get_string('pluginname', 'tool_adpe');
$pagetitle = $title;
$PAGE->set_title($title);
$PAGE->set_heading($title);

// Default courseid is the frontpage.
$courseid = 1;

if (isset($_GET['id'])) {
    $courseid = $_GET['id'];
    $PAGE->set_url($url, ['id' => required_param('id', PARAM_INT)]);
}

$OUTPUT = $PAGE->get_renderer('tool_adpe');

echo $OUTPUT->header();

$sometext = get_string('sometext', 'tool_adpe');
$renderable = new \tool_adpe\output\index_page($pagetitle, $sometext);
echo $OUTPUT->render($renderable);

echo html_writer::empty_tag('hr');
echo html_writer::span(get_string('header_courseid', 'tool_adpe'));
echo html_writer::div(get_string('output_courseid', 'tool_adpe', $courseid));

echo html_writer::empty_tag('hr');
echo html_writer::span(get_string('header_simpledb', 'tool_adpe'));

$users = $DB->get_records_sql('SELECT firstname, lastname, email FROM {user}');
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

echo html_writer::empty_tag('hr');
echo html_writer::span(get_string('header_sqltable', 'tool_adpe'));

$table = new \tool_adpe\table_plugin('tool_adpe_sql');
$table->is_collapsible = false;
$table->is_sortable = true;

$table->set_sql('*', "{tool_adpe}", 'courseid = '. $courseid);
$table->define_baseurl($url);
$table->out(2, true);

echo $OUTPUT->footer();