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
 * Renderer class for show courses page.
 *
 * @package   tool_adpe
 * @copyright 2019, Adrian Perez <p.adrian@gmx.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_adpe\output\editcourses;

defined('MOODLE_INTERNAL') || die;

class renderer extends \plugin_renderer_base {

    /**
     * Get html to display on the page.
     *
     * @param renderable $renderable renderable widget
     *
     * @return string to display on the editcourses page.
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    protected function render_renderable(renderable $renderable) {
        $o = $this->render_table($renderable);
        $o .= $this->render_add_button($renderable->courseid);

        return $o;
    }

    /**
     * Get html to display on the page.
     *
     * @param renderable $renderable renderable widget
     *
     * @return string to display on the courses page.
     */
    protected function render_table(renderable $renderable) {
        $o = '';
        ob_start();
        $renderable->out($renderable->pagesize, true);
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }

    /**
     * Html to add a button for adding a new course.
     *
     * @param int $courseid course id.
     *
     * @return string html for the button.
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    protected function render_add_button($courseid) {
        global $CFG;

        $button = \html_writer::tag('button', get_string('add'), ['class' => 'btn btn-primary mt-3']);
        $addurl = new \moodle_url($CFG->wwwroot . '/admin/tool/adpe/edit.php', array('courseid' => $courseid));
        return \html_writer::link($addurl, $button);
    }
}
