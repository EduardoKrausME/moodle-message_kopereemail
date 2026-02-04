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
 * Install callbacks for message_kopereemail.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use message_kopereemail\install\config_migrator;

/**
 * Install the Kopere Email message processor and migrate configs from email.
 *
 * Requirements from the request:
 * - Copy message/email provider configs: email_provider_* -> kopereemail_provider_*
 * - Replace "email" with "kopereemail" in message_provider_{component}_{name}_enabled
 * - Force email_provider_{component}_{name}_locked = 1 for all providers
 * - Disable legacy email processor (message_processors.enabled = 0 where name="email")
 *
 * @throws dml_exception
 * @throws coding_exception
 */
function xmldb_message_kopereemail_install() {
    global $DB, $SITE;

    $provider = new stdClass();
    $provider->name = "kopereemail";
    $DB->insert_record("message_processors", $provider);

    $a = [
        "messagepreferences" => get_string("messagepreferences", "message"),
        "notificationpreferencesurl" => (new moodle_url("/message/notificationpreferences.php"))->out(false),
        "primarycolor" => get_config("theme_boost", "brandcolor"),
    ];
    $value = get_string("settings_wrapper_default", "message_kopereemail", $a);
    set_config("wrapperhtml", $value, "message_kopereemail");

    // Disable legacy email processor if the field exists (it does in modern Moodle).
    $DB->execute("UPDATE {message_processors} SET enabled = 0 WHERE name = ?", ["email"]);

    // Copy all email_provider_* message configs into kopereemail_provider_*.
    $like = $DB->sql_like("name", "?", false);
    $records = $DB->get_records_select("config_plugins", "plugin = ? AND {$like}", ["message", "email_provider_%"]);
    foreach ($records as $rec) {
        $newname = preg_replace("/^email_provider_/", "kopereemail_provider_", $rec->name);
        if (!empty($newname)) {
            set_config($newname, $rec->value, "message");
        }
    }

    // Loop all providers and update enabled outputs + lock email_provider_*.
    $providers = $DB->get_records("message_providers");
    foreach ($providers as $p) {
        $enabledname = "message_provider_{$p->component}_{$p->name}_enabled";
        $enabledvalue = get_config("message", $enabledname);

        if ($enabledvalue !== false && $enabledvalue !== null && $enabledvalue !== "") {
            $enabledvalue = config_migrator::replace_output_name($enabledvalue, "email", "kopereemail");
            set_config($enabledname, $enabledvalue, "message");
        }

        $lockname = "email_provider_{$p->component}_{$p->name}_locked";
        set_config($lockname, 1, "message");
    }

    return true;
}
