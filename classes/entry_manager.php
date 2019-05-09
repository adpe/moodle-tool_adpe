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
 * Entry manager class.
 *
 * @package    tool_adpe
 * @copyright  2014 onwards Simey Lameze <lameze@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_adpe;

use core\notification;

defined('MOODLE_INTERNAL') || die();

class entry_manager {

    /**
     * Clean data submitted by mform.
     *
     * @param \stdClass $mformdata data to insert as new entry entry.
     *
     * @return \stdClass Cleaned entry data.
     */
    public static function clean_entrydata_form($mformdata) {
        $entry = new \stdClass();
        if (isset($mformdata->entryid)) {
            $entry->id = $mformdata->entryid;
        }
        $entry->courseid = $mformdata->courseid;
        $entry->name = $mformdata->name;
        $entry->completed = $mformdata->completed;

        return $entry;
    }

    /**
     * Get an instance of entry class.
     *
     * @param \stdClass|int $entryorid A entry object from database or entry id.
     *
     * @return entry object with entry id.
     * @throws \dml_exception
     */
    public static function get_entry($entryorid) {
        global $DB;
        if (!is_object($entryorid)) {
            $entry = $DB->get_record('tool_adpe', array('id' => $entryorid), '*', MUST_EXIST);
        } else {
            $entry = $entryorid;
        }

        return new entry($entry);
    }

    /**
     * Create a new entry.
     *
     * @param \stdClass $entrydata data to insert as new entry entry.
     *
     * @return entry An instance of entry class.
     * @throws \dml_exception
     */
    public static function add_entry($entrydata) {
        global $DB;

        $now = time();
        $entrydata->timecreated = $now;
        $entrydata->timemodified = $now;

        $entrydata->id = $DB->insert_record('tool_adpe', $entrydata);

        return new entry($entrydata);
    }

    /**
     * Delete a entry by entry id.
     *
     * @param int $entryid id of entry to be deleted.
     *
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function delete_entry($entryid) {
        global $DB;

        return $DB->delete_records('tool_adpe', array('id' => $entryid));
    }

    /**
     * Update entry  data.
     *
     * @param object $entrydata entry data to be updated.
     *
     * @return bool
     * @throws \coding_exception if $record->entryid is invalid.
     * @throws \dml_exception
     */
    public static function update_entry($entrydata) {
        global $DB;
        if (!self::get_entry($entrydata->id)) {
            throw new \coding_exception('Invalid entry ID.');
        }
        unset($entrydata->courseid);
        $entrydata->timemodified = time();

        return $DB->update_record('tool_adpe', $entrydata);
    }

    /**
     * Get all entries.
     *
     * @param int $limitfrom Limit from which to fetch entries.
     * @param int $limitto Limit to which entries need to be fetched.
     * @param bool $includesite Determines whether we return site wide entries or not.
     *
     * @return array List of entries for the given course id, if specified will also include site entries.
     * @throws \dml_exception
     */
    public static function get_all_entries($limitfrom = 0, $limitto = 0) {
        global $DB;

        $orderby = 'courseid DESC, name ASC';

        return self::get_instances($DB->get_records('tool_adpe', null, $orderby, '*', $limitfrom, $limitto));
    }

    /**
     * Get entries by course id.
     *
     * @param int $courseid course id of the entry.
     * @param int $limitfrom Limit from which to fetch entries.
     * @param int $limitto Limit to which entries need to be fetched.
     * @param bool $includesite Determines whether we return site wide entries or not.
     *
     * @return array List of entries for the given course id, if specified will also include site entries.
     * @throws \dml_exception
     */
    public static function get_entries_by_courseid($courseid, $limitfrom = 0, $limitto = 0, $includesite = true) {
        global $DB;

        $select = 'courseid = ?';
        $params = array();
        $params[] = $courseid;
        if ($includesite) {
            $select .= ' OR courseid = ?';
            $params[] = 0;
        }
        $orderby = 'courseid DESC, name ASC';

        return self::get_instances($DB->get_records_select('tool_adpe', $select, $params, $orderby,
                '*', $limitfrom, $limitto));
    }

    /**
     * Get entry count by course id.
     *
     * @param int $courseid course id of the entry.
     *
     * @return int count of entries present in system visible in the given course id.
     * @throws \dml_exception
     */
    public static function count_entries_by_courseid($courseid) {
        global $DB;
        $select = "courseid = ? OR courseid = ?";
        return $DB->count_records_select('tool_adpe', $select, array(0, $courseid));
    }

    /**
     * Helper method to convert db records to instances.
     *
     * @param array $arr of entries.
     *
     * @return array of entries as instances.
     */
    protected static function get_instances($arr) {
        $result = array();
        foreach ($arr as $key => $sub) {
            $result[$key] = new entry($sub);
        }
        return $result;
    }
}
