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
 * event_observers.php
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail;

use core\event\message_viewed;
use dml_exception;

/**
 * Event observers implementation.
 */
class event_observers {

    /**
     * When a message is viewed, remove it from the digest queue.
     *
     * @param message_viewed $event
     * @throws dml_exception
     */
    public static function message_viewed(message_viewed $event) {
        global $DB;

        $userid = $event->userid;
        $messageid = $event->other["messageid"];

        $DB->delete_records("message_kopereemail_messages", [
            "useridto" => $userid,
            "messageid" => $messageid,
        ]);
    }
}
