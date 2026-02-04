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
 * Import templates JSON for environment migration.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;
use message_kopereemail\form\import_form;
use message_kopereemail\import_export\manager;

require_once(__DIR__ . "/../../../config.php");

require_login();
$context = context_system::instance();
require_capability("moodle/site:config", $context);

$url = new moodle_url("/message/output/kopereemail/import.php");
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout("admin");

$PAGE->set_title(get_string("import_title", "message_kopereemail"));
$PAGE->set_heading(get_string("import_title", "message_kopereemail"));

$mform = new import_form();

if ($mform->is_cancelled()) {
    redirect(new moodle_url("/admin/settings.php", ["section" => "messagesettingkopereemail"]));
}

if ($data = $mform->get_data()) {
    $draftitemid = $data->importfile;

    $fs = get_file_storage();
    $contextuser = context_user::instance($USER->id);
    $files = $fs->get_area_files($contextuser->id, "user", "draft", $draftitemid, "id DESC", false);
    $file = reset($files);

    if (!$file) {
        $message = get_string("import_invalid_payload", "message_kopereemail");
        redirect($PAGE->url, $message, null, notification::NOTIFY_ERROR);
    }

    $content = $file->get_content();
    $payload = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $message = get_string("import_invalid_json", "message_kopereemail");
        redirect($PAGE->url, $message, null, notification::NOTIFY_ERROR);
    }

    if (!manager::validate_payload($payload)) {
        $message = get_string("import_invalid_payload", "message_kopereemail");
        redirect($PAGE->url, $message, null, notification::NOTIFY_ERROR);
    }

    $result = manager::import_payload($payload, [
        "overwrite" => !empty($data->overwrite),
        "importwrapper" => !empty($data->importwrapper),
    ]);

    $a = (object) [
        "imported" => $result["imported"],
        "skipped" => $result["skipped"],
        "wrapper" => !empty($result["wrapperupdated"]) ? "sim" : "não",
    ];

    $url = new moodle_url("/admin/settings.php", ["section" => "messagesettingkopereemail"]);
    $message = get_string("import_success", "message_kopereemail", $a);
    redirect($url, $message, null, notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();

echo $OUTPUT->render_from_template("message_kopereemail/import", [
    "desc" => get_string("templates_transfer_desc", "message_kopereemail"),
    "formhtml" => $mform->render(),
]);

echo $OUTPUT->footer();
