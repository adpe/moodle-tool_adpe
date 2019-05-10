<?php
// This file is part of Moodle - https://moodle.org/
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
 * Modify navigation nodes.
 *
 * @package   tool_adpe
 * @copyright 2018, Adrian Perez <p.adrian@gmx.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function tool_adpe_extend_navigation_course(navigation_node $navigation) {
    global $USER, $COURSE;

    if (!has_capability('tool/adpe:view', context_course::instance($COURSE->id), $USER->id)) {
        return;
    }

    $navigation->add(
            get_string('pluginname', 'tool_adpe'),
            new moodle_url('/admin/tool/adpe/index.php', ['courseid' => $COURSE->id]),
            navigation_node::TYPE_SETTING,
            get_string('pluginname', 'tool_adpe'),
            'adpe',
            new pix_icon('icon', '', 'tool_adpe')
    );
}
