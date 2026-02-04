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
 * Persistence layer for provider templates.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail;

use dml_exception;
use stdClass;

/**
 * Persistence layer for provider templates.
 */
class template_repository {

    /**
     * Get template record by provider.
     *
     * @param string $component
     * @param string $name
     * @return stdClass|null
     * @throws dml_exception
     */
    public static function get_by_provider($component, $name) {
        global $DB;

        return $DB->get_record("message_kopereemail_template", [
            "component" => $component,
            "name" => $name,
        ]);
    }

    /**
     * Insert or update a record from moodleform data.
     *
     * @param stdClass $data
     * @return int Record id.
     * @throws dml_exception
     */
    public static function upsert_from_form(stdClass $data) {
        global $DB, $USER;

        $existing = self::get_by_provider($data->component, $data->name);
        $now = time();

        $record = new stdClass();
        $record->component = $data->component;
        $record->name = $data->name;
        $record->subject = $data->subject ?? "";

        $record->bodyhtml = self::remove_tiny_http($data->bodyhtml["text"]);
        $record->bodyhtmlformat = $data->bodyhtml["format"];
        $record->useridmodified = $USER->id;

        if ($existing) {
            $record->id = $existing->id;
            $record->timecreated = $existing->timecreated;
            $record->timemodified = $now;
            $DB->update_record("message_kopereemail_template", $record);
            return $record->id;
        }

        $record->timecreated = $now;
        $record->timemodified = $now;

        return $DB->insert_record("message_kopereemail_template", $record);
    }

    /**
     * Function remove tiny http
     *
     * @param string $html
     * @return string
     */
    private static function remove_tiny_http(string $html): string {
        $pattern = '~(\bhref\s*=\s*)(["\'])\s*http://\s*(\{\{.*?\}\})\s*\2~i';
        $replacement = '$1$2$3$2';

        $out = preg_replace($pattern, $replacement, $html);

        // preg_replace pode retornar null em caso de erro de regex
        return $out ?? $html;
    }

    /**
     * Delete a template by provider.
     *
     * @param string $component
     * @param string $name
     * @throws dml_exception
     */
    public static function delete_by_provider($component, $name) {
        global $DB;

        $DB->delete_records("message_kopereemail_template", [
            "component" => $component,
            "name" => $name,
        ]);
    }
}
