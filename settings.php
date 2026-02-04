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

use message_kopereemail\output\settings_renderer;
use message_kopereemail\placeholders;

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $description = get_string("settings_wrapper_desc", "message_kopereemail") .
        $OUTPUT->render_from_template("message_kopereemail/placeholders", [
            "placeholders" => placeholders::get_definitions(),
        ]);
    $setting = new admin_setting_confightmleditor(
        "message_kopereemail/wrapperhtml",
        get_string("settings_wrapper", "message_kopereemail"),
        $description, "", PARAM_RAW, 60, 18
    );
    $settings->add($setting);

    $setting = new admin_setting_heading(
        "message_kopereemail/customtemplates",
        get_string("settings_customtemplates", "message_kopereemail"),
        settings_renderer::render_providers_section()
    );
    $settings->add($setting);
}
