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

require_once(dirname(__FILE__) . '/registrar_query.class.php');

class registrar_ccle_courseinstructorsget extends registrar_query {
    function validate($new, $old) {
        $new = array_change_key_case($new, CASE_LOWER);

        if (!isset($new['srs']) && $new['srs'] != $old['srs']) {
            return false;
        }

        if (!isset($new['ucla_id'])) {
            return false;
        }

        return (object) $new;
    }

    function remote_call_generate($args) {
        if (preg_match('/[0-9]{2}[FWS1]/', $args[0])) {
            $term = $args[0];
        } else {
            return false;
        }

        if (preg_match('/[0-9]{9}/', $args[1])) {
            $srs = $args[1];
        } else {
            return false;
        }

        return "EXECUTE ccle_CourseInstructorsGet '$term', '$srs'";
    }
}