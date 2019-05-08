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
 * Renderable class for show courses table.
 *
 * @package   tool_adpe
 * @copyright 2019, Adrian Perez <p.adrian@gmx.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_adpe\output\editcourses;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/tablelib.php');

class renderable extends \table_sql implements \renderable {

    /**
     * @var int course id.
     */
    public $courseid;

    /**
     * @var \context_course|\context_system context of the page to be rendered.
     */
    protected $context;

    /**
     * @var bool Does the user have capability to manage entrys at site context.
     */
    protected $hassystemcap;

    /**
     * Sets up the table_log parameters.
     *
     * @param string $uniqueid unique id of form.
     * @param \moodle_url $url url where this table is displayed.
     * @param int $courseid course id.
     * @param int $perpage Number of entrys to display per page.
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function __construct($uniqueid, \moodle_url $url, $courseid = 0, $perpage = 100) {
        parent::__construct($uniqueid);

        $this->set_attribute('id', 'tooladpeeditcourses_table');
        $this->set_attribute('class', 'tooladpe editcourses generaltable generalbox');
        $this->define_columns(array('courseid', 'name', 'completed', 'priority', 'timecreated', 'timemodified', 'manage'));
        $this->define_headers(array('Course ID', 'Name', 'Completed', 'Priority', 'Time created', 'Time modified', 'Manage'));
        $this->courseid = $courseid;
        $this->pagesize = $perpage;
        $systemcontext = \context_system::instance();
        $this->context = empty($courseid) ? $systemcontext : \context_course::instance($courseid);
        $this->hassystemcap = has_capability('tool/adpe:edit', $systemcontext);
        $this->collapsible(true);
        $this->sortable(true);
        $this->pageable(true);
        $this->is_downloadable(false);
        $this->define_baseurl($url);
    }

    public function col_name(\tool_adpe\entry $entry) {
        return $entry->get_name($this->context);
    }

    /**
     * @param  \tool_adpe\entry $entry object
     *
     * @return string
     * @throws \coding_exception
     */
    public function col_completed(\tool_adpe\entry $entry) {
        if ($entry->completed == 1) {
            return get_string('output_sqltable_yes', 'tool_adpe');
        } else {
            return get_string('output_sqltable_no', 'tool_adpe');
        }
    }

    /**
     * Generate content for manage column.
     *
     * @param  \tool_adpe\entry $entry object
     *
     * @return string html used to display the manage column field.
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function col_manage(\tool_adpe\entry $entry) {
        global $OUTPUT, $CFG;

        $manage = '';

        // Do not allow the user to edit the entry unless they have the system capability, or we are viewing the entries
        // for a course, and not the site. Note - we don't need to check for the capability at a course level since
        // the user is never shown this page otherwise.
        if ($this->hassystemcap || ($entry->courseid != 0)) {
            $editurl = new \moodle_url($CFG->wwwroot. '/admin/tool/adpe/edit.php', array('entryid' => $entry->id,
                    'courseid' => $entry->courseid, 'sesskey' => sesskey()));
            $icon = $OUTPUT->render(new \pix_icon('t/edit', get_string('edit')));
            $manage .= \html_writer::link($editurl, $icon, array('class' => 'action-icon'));
        }

        return $manage;
    }

    /**
     * Query the reader. Store results in the object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     *
     * @throws \dml_exception
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        $total = \tool_adpe\entry_manager::count_entries_by_courseid($this->courseid);
        $this->pagesize($pagesize, $total);
        $entries = \tool_adpe\entry_manager::get_entries_by_courseid($this->courseid, $this->get_page_start(),
                $this->get_page_size(), false);
        $this->rawdata = $entries;
        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }
}
