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
 * Extends table_sql to provide a table of this plugins database.
 *
 * @package   tool_adpe
 * @copyright 2018, Adrian Perez <p.adrian@gmx.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_adpe;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use table_sql;

class table_plugin extends table_sql {

    public function __construct($uniqueid) {
        parent::__construct($uniqueid);

        // Define the list of columns to show.
        $columns = array('courseid', 'name', 'completed', 'priority', 'timecreated', 'timemodified');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array('Course ID', 'Name', 'Completed', 'Priority', 'Time created', 'Time modified');
        $this->define_headers($headers);
    }

    public function col_name($value) {
        return format_string($value->name);
    }

    public function col_completed($value) {
        if ($value->completed == 1) {
            return get_string('output_sqltable_yes', 'tool_adpe');
        } else if ($value->completed == 0 ) {
            return get_string('output_sqltable_no', 'tool_adpe');
        }

        return $value->completed;
    }

    public function col_timecreated($value) {
        return $value->timecreated = userdate($value->timecreated);
    }

    public function col_timemodified($value) {
        return $value->timemodified = userdate($value->timemodified);
    }

}