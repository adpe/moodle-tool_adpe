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
$courseid = $_GET['id'];
$PAGE->set_url($url, ['id' => required_param('id', PARAM_INT)]);
$PAGE->set_title($title);
$PAGE->set_heading($title);

$OUTPUT = $PAGE->get_renderer('tool_adpe');

echo $OUTPUT->header();

$renderable = new \tool_adpe\output\index_page($pagetitle, 'This is the sub-heading');
echo $OUTPUT->render($renderable);

echo html_writer::div(get_string('helloworld', 'tool_adpe'));
echo html_writer::div(get_string('courseid', 'tool_adpe', $courseid));

echo $OUTPUT->footer();