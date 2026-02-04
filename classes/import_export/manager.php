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
 * manager.php
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail\import_export;

use message_kopereemail\template_repository;

/**
 * Export/import manager for templates across environments.
 *
 * @package    message_kopereemail
 */
class manager {

    /**
     * Build the export payload.
     *
     * @return array
     */
    public static function build_payload() {
        global $DB;

        $wrapper = (string)get_config("message_kopereemail", "wrapperhtml");

        $templates = $DB->get_records("message_kopereemail_template", null, "component ASC, name ASC");
        $items = [];

        foreach ($templates as $t) {
            $items[] = [
                "component" => (string)$t->component,
                "name" => (string)$t->name,
                "subject" => (string)$t->subject,
                "bodyhtml" => (string)$t->bodyhtml,
                "bodyhtmlformat" => (int)$t->bodyhtmlformat,
            ];
        }

        return [
            "schema" => "message_kopereemail.templates.v1",
            "generator" => "message_kopereemail",
            "generatedat" => time(),
            "wrapperhtml" => $wrapper,
            "templates" => $items,
        ];
    }

    /**
     * Validate payload structure.
     *
     * @param mixed $payload
     * @return bool
     */
    public static function validate_payload($payload) {
        if (!is_array($payload)) {
            return false;
        }
        if (empty($payload["schema"]) || $payload["schema"] !== "message_kopereemail.templates.v1") {
            return false;
        }
        if (!array_key_exists("wrapperhtml", $payload)) {
            return false;
        }
        if (!isset($payload["templates"]) || !is_array($payload["templates"])) {
            return false;
        }

        foreach ($payload["templates"] as $t) {
            if (!is_array($t)) {
                return false;
            }
            if (empty($t["component"]) || empty($t["name"])) {
                return false;
            }
            if (!array_key_exists("bodyhtml", $t)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Import templates from payload.
     *
     * @param array $payload
     * @param int $userid
     * @param array $options Supported keys:
     *  - overwrite (bool)
     *  - importwrapper (bool)
     * @return array Results: imported, skipped, wrapperupdated
     */
    public static function import_payload(array $payload, array $options = []) {
        global $USER;
        $overwrite = !empty($options["overwrite"]);
        $importwrapper = !empty($options["importwrapper"]);

        $imported = 0;
        $skipped = 0;
        $wrapperupdated = 0;

        if ($importwrapper) {
            set_config("wrapperhtml", (string)$payload["wrapperhtml"], "message_kopereemail");
            $wrapperupdated = 1;
        }

        foreach ($payload["templates"] as $t) {
            $component = (string)$t["component"];
            $name = (string)$t["name"];

            $existing = template_repository::get_by_provider($component, $name);

            if ($existing && !$overwrite) {
                $skipped++;
                continue;
            }

            $data = new \stdClass();
            $data->component = $component;
            $data->name = $name;
            $data->subject = isset($t["subject"]) ? (string)$t["subject"] : "";
            $data->bodyhtml = [
                "text" => (string)$t["bodyhtml"],
                "format" => isset($t["bodyhtmlformat"]) ? (int)$t["bodyhtmlformat"] : 1,
            ];

            template_repository::upsert_from_form($data, $USER->id);
            $imported++;
        }

        return [
            "imported" => $imported,
            "skipped" => $skipped,
            "wrapperupdated" => $wrapperupdated,
        ];
    }

    /**
     * Encode payload to JSON with stable formatting.
     *
     * @param array $payload
     * @return string
     */
    public static function encode_json(array $payload) {
        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
