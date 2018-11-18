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
 * This file contains the specifications for the database.
 *
 * @package   tool_adpe
 * @copyright 2018, Adrian Perez <p.adrian@gmx.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_tool_adpe_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    // Add initial database.
    if ($oldversion < 2018111702) {

        // Define table tool_adpe to be created.
        $table = new xmldb_table('tool_adpe');

        // Adding fields to table tool_adpe.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('completed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('priority', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table tool_adpe.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for tool_adpe.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Adpe savepoint reached.
        upgrade_plugin_savepoint(true, 2018111702, 'tool', 'adpe');
    }

    if ($oldversion < 2018111703) {

        // Define key foreign index (foreign) to be added to tool_adpe.
        $table = new xmldb_table('tool_adpe');
        $key = new xmldb_key('foreign index', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

        // Launch add key foreign index.
        $dbman->add_key($table, $key);

        // Define index unique index (unique) to be added to tool_adpe.
        $index = new xmldb_index('unique index', XMLDB_INDEX_UNIQUE, array('courseid', 'name'));

        // Conditionally launch add index unique index.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Adpe savepoint reached.
        upgrade_plugin_savepoint(true, 2018111703, 'tool', 'adpe');
    }

    return true;
}