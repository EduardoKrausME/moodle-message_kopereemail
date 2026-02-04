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
 * Admin page to create/edit a custom template for a message provider.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use message_kopereemail\form\edit_form;
use message_kopereemail\placeholders;
use message_kopereemail\provider_helper;
use message_kopereemail\template_repository;

require_once(__DIR__ . "/../../../config.php");

$component = required_param("component", PARAM_COMPONENT);
$name = required_param("name", PARAM_ALPHANUMEXT);

require_login();
$context = context_system::instance();
require_capability("moodle/site:config", $context);

$url = new moodle_url("/message/output/kopereemail/edit.php", ["component" => $component, "name" => $name]);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout("admin");

$PAGE->set_title(get_string("template_edit_title", "message_kopereemail"));
$PAGE->set_heading(get_string("template_edit_title", "message_kopereemail"));

$mform = new edit_form(null, ["component" => $component, "name" => $name]);

$current = template_repository::get_by_provider($component, $name);
if ($current) {
    $mform->set_data([
        "component" => $component,
        "name" => $name,
        "subject" => $current->subject,
        "bodyhtml" => [
            "text" => $current->bodyhtml,
            "format" => $current->bodyhtmlformat,
        ],
    ]);
}

if ($mform->is_cancelled()) {
    redirect(new moodle_url("/admin/settings.php", ["section" => "messagesettingkopereemail"]));
}

if ($data = $mform->get_data()) {
    template_repository::upsert_from_form($data);

    redirect(
        new moodle_url("/admin/settings.php", ["section" => "messagesettingkopereemail"]),
        get_string("template_saved", "message_kopereemail"),
        null,
        notification::NOTIFY_SUCCESS
    );
}

$formhtml = $mform->render();
$placeholders = placeholders::get_definitions();

echo $OUTPUT->header();

echo $OUTPUT->render_from_template("message_kopereemail/template_edit", [
    "providerlabel" => provider_helper::get_display_name($component, $name),
    "component" => s($component),
    "name" => s($name),
    "formhtml" => $formhtml,
    "placeholders" => array_values($placeholders),
]);

echo $OUTPUT->footer();
