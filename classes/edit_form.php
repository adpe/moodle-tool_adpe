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
 * Edit form to add and update a course entry.
 *
 * @package   tool_adpe
 * @copyright 2019, Adrian Perez <p.adrian@gmx.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_adpe;

use core\notification;

require_once($CFG->libdir . "/formslib.php");

class edit_form extends \moodleform {

    /**
     * Form definition
     *
     * @throws \coding_exception
     */
    public function definition() {
        $mform = $this->_form;
        $entry = $this->_customdata['entry'];
        $courseid = $this->_customdata['courseid'];

        // General section header.
        $mform->addElement('header', 'general', get_string('general'));

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        // We are editing a existing entry.
        if (!empty($entry->id)) {
            // Hidden entry id.
            $mform->addElement('hidden', 'entryid');
            $mform->setType('entryid', PARAM_INT);
            $mform->setConstant('entryid', $entry->id);

            // Force course id.
            $courseid = $entry->courseid;
        }

        // Name field.
        $mform->addElement('text', 'name', get_string('shortname'));
        $mform->addRule('name', get_string('required'), 'required');
        $mform->setType('name', PARAM_TEXT);

        // Completed checkbox.
        $mform->addElement('advcheckbox', 'completed', get_string('complete'), '', array(0, 1));

        // Make course id a constant.
        $mform->setConstant('courseid', $courseid);

        $this->add_action_buttons();
    }

    /**
     * Form validation
     *
     * @param array $data data from the form.
     * @param array $files files uploaded.
     *
     * @return array of errors.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function validation($data, $files) {
        global $DB;

        if (empty($data['entryid'])) {
            $errors = array();
            if ($DB->record_exists('tool_adpe', array('courseid' => $data['courseid'], 'name' => $data['name']))) {
                array_push($errors, notification::warning(get_string('output_entryexists', 'tool_adpe')));
            }

            return $errors;
        }
    }
}
