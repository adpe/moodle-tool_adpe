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
 * Page to handle the edit of course entries.
 *
 * @package   tool_adpe
 * @copyright 2019, Adrian Perez <p.adrian@gmx.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_adpe\entry_manager;

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->libdir.'/adminlib.php');

$entryid = optional_param('entryid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);

// Validate course id.
if (empty($courseid)) {
    require_login();
    $context = context_system::instance();
    $coursename = format_string($SITE->fullname, true, array('context' => $context));
    $PAGE->set_context($context);
} else {
    $course = get_course($courseid);
    require_login($course);
    $context = context_course::instance($course->id);
    $coursename = format_string($course->fullname, true, array('context' => $context));
}

require_capability('tool/adpe:edit', $context, $USER->id);

// Set up the page.
$url = new moodle_url("/admin/tool/adpe/edit.php", array('courseid' => $courseid));
$manageurl = new moodle_url("/admin/tool/adpe/index.php", array('courseid' => $courseid));
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($coursename);
$PAGE->set_heading($coursename);

// Site level report.
if (empty($courseid)) {
    admin_externalpage_setup('tooladpeeditcourses', '', null, '', array('pagelayout' => 'report'));
} else {
    // Course level report.
    $PAGE->navigation->override_active_url($manageurl);
}

// Add form.
if (!empty($entryid)) {
    $entry = entry_manager::get_entry($entryid)->get_mform_set_data();
} else {
    $entry = new stdClass();
}

$mform = new tool_adpe\edit_form(null, array('entry' => $entry, 'courseid' => $courseid));

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/adpe/index.php', array('courseid' => $courseid)));
    exit();
} else if ($mformdata = $mform->get_data()) {
    $entry = entry_manager::clean_entrydata_form($mformdata);

    if (empty($entry->id)) {
        entry_manager::add_entry($entry);
        redirect($manageurl, get_string('output_entryadded', 'tool_adpe'), null,
                \core\output\notification::NOTIFY_SUCCESS);
    } else {
        entry_manager::update_entry($entry);
        redirect($manageurl, get_string('output_entryupdated', 'tool_adpe'), null,
                \core\output\notification::NOTIFY_SUCCESS);
    }
} else {
    echo $OUTPUT->header();
    $mform->set_data($entry);
    $mform->display();
    echo $OUTPUT->footer();
    exit;
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('header_form', 'tool_adpe'));

$mform->set_data($entry);

if (!empty($entry->id)) {
    echo $OUTPUT->heading(get_string('editrule', 'tool_monitor'));
} else {
    echo $OUTPUT->heading(get_string('addrule', 'tool_monitor'));
}

$mform->display();
echo $OUTPUT->footer();
