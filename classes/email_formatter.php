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
 * Email formatting helpers.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail;

/**
 * Email formatting helpers.
 */
class email_formatter {

    /**
     * Convert plain text to a simple HTML body.
     *
     * @param string $plain
     * @return string
     */
    public static function plain_to_html($plain) {
        $plain = trim($plain);
        if ($plain === "") {
            return "";
        }
        $safe = s($plain);
        $safe = nl2br($safe);
        return "<div style=\"font-family: Arial, sans-serif; font-size: 14px; line-height: 1.5;\">{$safe}</div>";
    }

    /**
     * Convert HTML to plain text (best effort).
     *
     * @param string $html
     * @return string
     */
    public static function html_to_plain($html) {
        if (function_exists("html_to_text")) {
            return trim(html_to_text($html));
        }
        return trim(strip_tags($html));
    }
}
