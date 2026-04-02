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
 * Helpers to render dynamic content inside admin settings.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail\output;

use Exception;
use message_kopereemail\provider_helper;
use moodle_url;

/**
 * phpcs:disable moodle.PHP.ForbiddenGlobalUse.BadGlobal
 *
 * Helpers to render dynamic content inside admin settings.
 */
class settings_renderer {

    /**
     * Render the providers section for settings.php.
     *
     * @return string
     * @throws Exception
     */
    public static function render_providers_section(): string {
        global $DB, $OUTPUT;

        $sql = "SELECT CONCAT(mp.id,'-',mkt.id,mp.component) AS id, mp.component, mp.name, mkt.id AS templateid
                  FROM {message_providers}             mp
             LEFT JOIN {message_kopereemail_template} mkt ON mkt.component = mp.component
                 WHERE mkt.name = mp.name
              ORDER BY mp.component ASC, mp.name ASC";

        $records = $DB->get_records_sql($sql);

        $providers = [];
        foreach ($records as $record) {
            $params = [
                "component" => $record->component,
                "name" => $record->name,
            ];

            $hascustomtemplate = !empty($record->templateid);

            $providers[] = [
                "providername" => provider_helper::get_display_name($record->component, $record->name),
                "component" => $record->component,
                "name" => $record->name,
                "hascustomtemplate" => $hascustomtemplate,
                "editurl" => new moodle_url("/message/output/kopereemail/edit.php", $params),
                "deleteurl" => new moodle_url("/message/output/kopereemail/delete.php", $params),
                "previewurl" => new moodle_url("/message/output/kopereemail/template-test.php", $params),
            ];
        }

        $context = [
            "providers" => $providers,
            "hasproviders" => !empty($providers),
        ];

        return $OUTPUT->render_from_template("message_kopereemail/admin_providers", $context);
    }
}
