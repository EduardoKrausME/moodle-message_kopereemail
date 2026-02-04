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
 * Uninstall callbacks for message_kopereemail.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use message_kopereemail\install\config_migrator;

/**
 * Revert "kopereemail" back to "email" in message provider config values.
 *
 * @throws dml_exception
 */
function xmldb_message_kopereemail_uninstall() {
    global $DB;

    $providers = $DB->get_records("message_providers");
    foreach ($providers as $p) {
        $enabledname = "message_provider_{$p->component}_{$p->name}_enabled";
        $enabledvalue = get_config("message", $enabledname);

        if ($enabledvalue !== false && $enabledvalue !== null && $enabledvalue !== "") {
            $enabledvalue = config_migrator::replace_output_name($enabledvalue, "kopereemail", "email");
            set_config($enabledname, $enabledvalue, "message");
        }
    }

    // Re-enable legacy email processor if possible.
    $DB->execute("UPDATE {message_processors} SET enabled = 1 WHERE name = ?", ["email"]);

    return true;
}
