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
 * Class represents a single entry.
 *
 * @package    tool_adpe
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_adpe;

defined('MOODLE_INTERNAL') || die();

/**
 * Class represents a single entry.
 *
 * @since      Moodle 2.8
 * @package    tool_adpe
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entry {

    /**
     * @var \stdClass The entry object form database.
     */
    protected $entry;

    /**
     * Constructor.
     *
     * @param \stdClass $entry A entry object from database.
     */
    public function __construct($entry) {
        $this->entry = $entry;
    }

    /**
     * Can the user manage this entry? Defaults to $USER.
     *
     * @param int $userid Check against this userid.
     *
     * @return bool true if the current user can manage this entry, else false.
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function can_manage_entry($userid = null) {
        $courseid = $this->courseid;
        $context = empty($courseid) ? \context_system::instance() : \context_course::instance($this->courseid);
        return has_capability('tool/monitor:edit', $context, $userid);
    }

    /**
     * Magic get method.
     *
     * @param string $prop property to get.
     *
     * @return mixed
     * @throws \coding_exception
     */
    public function __get($prop) {
        if (property_exists($this->entry, $prop)) {
            return $this->entry->$prop;
        }
        throw new \coding_exception('Property "' . $prop . '" doesn\'t exist');
    }

    /**
     * Return the entry data to be used while setting mform.
     *
     * @throws \coding_exception
     */
    public function get_mform_set_data() {
        if (!empty($this->entry)) {
            $entry = fullclone($this->entry);
            return $entry;
        }
        throw new \coding_exception('Invalid call to get_mform_set_data.');
    }

    /**
     * Get properly formatted name of the rule associated.
     *
     * @param \context $context context where this name would be displayed.
     *
     * @return string Formatted name of the rule.
     */
    public function get_name(\context $context) {
        return format_text($this->name, FORMAT_HTML, array('context' => $context));
    }

    /**
     * Get timecreated formatted as human readable.
     *
     * @param \context $context
     *
     * @return string
     * @throws \Exception
     */
    public function get_timecreated() {
        return userdate($this->timecreated);
    }

    /**
     * Get timemodified formatted as human readable.
     *
     * @param \context $context
     *
     * @return string
     * @throws \Exception
     */
    public function get_timemodified() {
        return userdate($this->timemodified);
    }
}
