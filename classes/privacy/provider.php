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
 * Privacy provider for message_kopereemail.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail\privacy;

use core_privacy\local\metadata\collection;

/**
 * Privacy provider for message_kopereemail.
 */
class provider implements \core_privacy\local\metadata\provider {

    /**
     * Describe stored data.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection) {
        $collection->link_external_location("smtp", [
            "recipient" => "privacy:metadata:recipient",
            "userfrom" => "privacy:metadata:userfrom",
            "subject" => "privacy:metadata:subject",
            "fullmessage" => "privacy:metadata:fullmessage",
            "fullmessagehtml" => "privacy:metadata:fullmessagehtml",
            "attachment" => "privacy:metadata:attachment",
            "attachname" => "privacy:metadata:attachname",
            "replyto" => "privacy:metadata:replyto",
            "replytoname" => "privacy:metadata:replytoname",
        ], "privacy:metadata:externalpurpose");

        return $collection;
    }
}
