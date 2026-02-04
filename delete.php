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
 * Admin page to delete a custom template for a message provider.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use message_kopereemail\provider_helper;
use message_kopereemail\template_repository;

require_once(__DIR__ . "/../../../config.php");

$component = required_param("component", PARAM_COMPONENT);
$name = required_param("name", PARAM_ALPHANUMEXT);
$confirm = optional_param("confirm", 0, PARAM_BOOL);

require_login();
$context = context_system::instance();
require_capability("moodle/site:config", $context);

$url = new moodle_url("/message/output/kopereemail/delete.php", ["component" => $component, "name" => $name]);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout("admin");

$PAGE->set_title(get_string("template_delete_title", "message_kopereemail"));
$PAGE->set_heading(get_string("template_delete_title", "message_kopereemail"));

if ($confirm && confirm_sesskey()) {
    template_repository::delete_by_provider($component, $name);

    $url = new moodle_url("/admin/settings.php", ["section" => "messagesettingkopereemail"]);
    $message = get_string("template_deleted", "message_kopereemail");
    redirect($url, $message, null, notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

echo $OUTPUT->render_from_template("message_kopereemail/template_delete", [
    "providerlabel" => provider_helper::get_display_name($component, $name),
    "yesurl" => (new moodle_url("/message/output/kopereemail/delete.php", [
        "component" => $component,
        "name" => $name,
        "confirm" => 1,
        "sesskey" => sesskey(),
    ]))->out(false),
    "nourl" => (new moodle_url("/admin/settings.php", ["section" => "messagesettingkopereemail"]))->out(false),
]);

echo $OUTPUT->footer();
