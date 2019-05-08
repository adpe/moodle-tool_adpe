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

$entryid = optional_param('entryid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$confirm = optional_param('confirm', false, PARAM_BOOL);

// Validate course id.
if (empty($courseid)) {
    $context = context_system::instance();
    $coursename = format_string($SITE->fullname, true, array('context' => $context));
    $PAGE->set_context($context);
} else {
    $course = get_course($courseid);
    require_login($course);
    $context = context_course::instance($course->id);
    $coursename = format_string($course->fullname, true, array('context' => $context));
}

require_capability('tool/adpe:view', $context, $USER->id);

// Set up the page.
$manageurl = new moodle_url("/admin/tool/adpe/index.php", array('courseid' => $courseid));
$PAGE->set_url($manageurl);
$PAGE->set_pagelayout('report');
$PAGE->set_title($coursename);
$PAGE->set_heading($coursename);

// Site level report.
if (empty($courseid)) {
    admin_externalpage_setup('tooladpeeditcourses', '', null, '', array('pagelayout' => 'report'));
}

// Copy/delete rule if needed.
if (!empty($action) && $entryid) {
    require_sesskey();

    // If the entry does not exist, then redirect back as the entry must have already been deleted.
    if (!$entry = $DB->get_record('tool_adpe', array('id' => $entryid), '*', IGNORE_MISSING)) {
        redirect(new moodle_url('/admin/tool/adpe/index.php', array('courseid' => $courseid)));
    }

    echo $OUTPUT->header();
    $entry = \tool_adpe\entry_manager::get_entry($entry);
    switch ($action) {
        case 'copy':
            // No need to check for capability here as it is done at the start of the page.
            $entry->duplicate_entry($courseid);
            echo $OUTPUT->notification(get_string('output_entrycopied', 'tool_adpe'), 'notifysuccess');
            break;
        case 'delete':
            if ($entry->can_manage_entry()) {
                $confirmurl = new moodle_url($CFG->wwwroot. '/admin/tool/adpe/index.php',
                        array('entryid' => $entryid, 'courseid' => $courseid, 'action' => 'delete',
                                'confirm' => true, 'sesskey' => sesskey()));
                $cancelurl = new moodle_url($CFG->wwwroot. '/admin/tool/adpe/index.php',
                        array('courseid' => $courseid));
                if ($confirm) {
                    $entry->delete_entry();
                    echo $OUTPUT->notification(get_string('output_entrydeleted', 'tool_adpe'), 'notifysuccess');
                } else {
                    $strconfirm = get_string('areyousure', 'core', $entry->get_name($context));
                    echo $OUTPUT->confirm($strconfirm, $confirmurl, $cancelurl);
                    echo $OUTPUT->footer();
                    exit();
                }
            } else {
                // User doesn't have permissions. Should never happen for real users.
                throw new moodle_exception('rulenopermissions', 'tool_adpe', $manageurl, $action);
            }
            break;
        default:
    }
} else {
    echo $OUTPUT->header();
}

// Render the intro.
$renderable = new \tool_adpe\output\intro\renderable($coursename, get_string('sometext', 'tool_adpe'));
$renderer = $PAGE->get_renderer('tool_adpe', 'intro');
echo $renderer->render($renderable);

echo $OUTPUT->heading(get_string('header_simpledb', 'tool_adpe'));
echo get_string('output_simpledb', 'tool_adpe');

// Render the course list.
echo $OUTPUT->heading(get_string('header_sqltable', 'tool_adpe'));
$renderable = new \tool_adpe\output\editcourses\renderable('tooladpeeditcourses', $manageurl, $courseid, 20);
$renderer = $PAGE->get_renderer('tool_adpe', 'editcourses');
echo $renderer->render($renderable);

echo $OUTPUT->footer();
