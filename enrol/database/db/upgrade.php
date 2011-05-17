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
 * Keeps track of upgrades to the global search block
 *
 * @package    
 * @subpackage 
 * @copyright  
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_database_upgrade($oldversion) {
    global $CFG, $DB;

    require('upgradelib.php');

    $result = true;
    $dbman = $DB->get_manager();

    if ($oldversion < 2011051700) {

        // Define table ucla_rolemapping to be created
        $table = new xmldb_table('ucla_rolemapping');

        // Adding fields to table ucla_rolemapping
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('pseudo_role', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('moodle_roleid', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('subject_area', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, '*SYSTEM*');

        // Adding keys to table ucla_rolemapping
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for ucla_rolemapping
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // database savepoint reached
        upgrade_plugin_savepoint(true, 2011051700, 'enrol', 'database');
    }


    return $result;
}
