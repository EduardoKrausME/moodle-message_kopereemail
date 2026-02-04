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
 * Admin settings for message_kopereemail.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use message_kopereemail\install\file_template;
use message_kopereemail\output\settings_renderer;
use message_kopereemail\placeholders;

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $contextmustache = ["placeholders" => placeholders::get_definitions()];
    $description = $OUTPUT->render_from_template("message_kopereemail/placeholders", $contextmustache);

    $setting = new admin_setting_confightmleditor(
        "message_kopereemail/wrapperhtml",
        get_string("settings_wrapper", "message_kopereemail"),
        $description, "", PARAM_RAW, 60, 18
    );
    $settings->add($setting);

    $contextmustache = [
        "testurl" => new moodle_url("/message/output/kopereemail/template-test.php"),
        'templates' => array_map(
            function(string $templatename): array {
                $p = ['template' => $templatename];
                return [
                    'name' => $templatename,
                    'previewurl' => (new moodle_url('/message/output/kopereemail/template-preview.php', $p)),
                ];
            },
            file_template::listall()
        ),
    ];
    $setting = new admin_setting_heading(
        "message_kopereemail/template_test",
        get_string("action_preview", "message_kopereemail"),
        $OUTPUT->render_from_template("message_kopereemail/template_test", $contextmustache)
    );
    $settings->add($setting);

    $contextmustache = [
        "exporturl" => (new moodle_url("/message/output/kopereemail/export.php", ["sesskey" => sesskey()]))->out(false),
        "importurl" => (new moodle_url("/message/output/kopereemail/import.php"))->out(false),
    ];
    $setting = new admin_setting_heading(
        "message_kopereemail/export_import",
        get_string("templates_transfer_title", "message_kopereemail"),
        $OUTPUT->render_from_template("message_kopereemail/admin_providers_export_import", $contextmustache)
    );
    $settings->add($setting);

    $setting = new admin_setting_heading(
        "message_kopereemail/customtemplates",
        get_string("settings_customtemplates", "message_kopereemail"),
        settings_renderer::render_providers_section()
    );
    $settings->add($setting);
}
