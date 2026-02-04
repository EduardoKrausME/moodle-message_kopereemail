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
 * test-template
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\message\message;
use core\output\notification;
use core_course\external\course_summary_exporter;

require_once(__DIR__ . "/../../../config.php");

require_login();

if ($DB->get_dbfamily() == "mysql") {
    $course = $DB->get_record_sql("SELECT *  FROM {course} WHERE visible = 1 ORDER BY RAND() LIMIT 1");
} else {
    $course = $DB->get_record_sql("SELECT *  FROM {course} WHERE visible = 1 LIMIT 1");
}

$context = context_system::instance();
$PAGE->set_context($context);
require_capability("moodle/site:config", $context);

$messagesubject = "Message subject";
$messagebody = "<div>Message Body...</div>";
$messageplaintext = "Message Body...";

$eventdata = new message();
$eventdata->component = optional_param("component", "moodle", PARAM_TEXT);
$eventdata->name = optional_param("name", "coursecompleted", PARAM_TEXT);
$eventdata->courseid = $course->id;
$eventdata->userfrom = core_user::get_noreply_user();
$eventdata->userto = $USER->id;
$eventdata->notification = 1;
$eventdata->subject = $messagesubject;
$eventdata->fullmessage = $messageplaintext;
$eventdata->fullmessageformat = FORMAT_HTML;
$eventdata->fullmessagehtml = $messagebody;
$eventdata->smallmessage = $messageplaintext;

if ($courseimage = course_summary_exporter::get_course_image($course)) {
    $eventdata->customdata = [
        "notificationpictureurl" => $courseimage,
    ];
}
message_send($eventdata);

redirect(
    new moodle_url("/admin/settings.php", ["section" => "messagesettingkopereemail"]),
    get_string("action_preview_success", "message_kopereemail"),
    null,
    notification::NOTIFY_SUCCESS
);
