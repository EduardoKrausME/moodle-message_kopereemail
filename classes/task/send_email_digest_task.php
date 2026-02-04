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
 * Scheduled task to send digest emails for group conversations.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail\task;

use coding_exception;
use context_system;
use core\task\scheduled_task;
use core_user;
use dml_exception;
use message_kopereemail\output\email_digest;
use message_kopereemail\template_processor;

/**
 * Scheduled task to send digest emails for group conversations.
 */
class send_email_digest_task extends scheduled_task {

    /**
     * Task name shown in admin UI.
     *
     * @return string
     * @throws coding_exception
     */
    public function get_name() {
        return get_string("tasksendemaildigest", "message_kopereemail");
    }

    /**
     * Execute the task.
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function execute() {
        global $DB, $PAGE, $SITE;

        $userids = $DB->get_fieldset_sql("SELECT DISTINCT useridto FROM {message_kopereemail_messages}");
        if (empty($userids)) {
            return;
        }

        // Prepare a minimal page context so renderers and user pictures can build URLs.
        $PAGE->set_context(context_system::instance());

        foreach ($userids as $userid) {
            $userto = $DB->get_record("user", ["id" => $userid, "deleted" => 0]);
            if (!$userto || !empty($userto->suspended) || $userto->auth === "nologin") {
                $DB->delete_records("message_kopereemail_messages", ["useridto" => $userid]);
                continue;
            }

            $queued = $DB->get_records("message_kopereemail_messages", ["useridto" => $userid]);
            if (empty($queued)) {
                continue;
            }

            $conversationids = [];
            $messageids = [];
            foreach ($queued as $q) {
                $conversationids[$q->conversationid] = $q->conversationid;
                $messageids[$q->messageid] = $q->messageid;
            }

            // Fetch conversations (best-effort for group conversations).
            [$insqlc, $paramsc] = $DB->get_in_or_equal(array_values($conversationids), SQL_PARAMS_NAMED);
            $sql = "
                SELECT mc.id, mc.itemid AS groupid, g.picture, g.courseid, g.name, c.fullname AS coursename
                  FROM {message_conversations} mc
                  JOIN {groups} g ON g.id = mc.itemid
                  JOIN {course} c ON c.id = g.courseid
                 WHERE mc.id {$insqlc}";
            $conversations = $DB->get_records_sql($sql, $paramsc);

            // Fetch messages + user fields needed by email_digest export/template.
            [$insqlm, $paramsm] = $DB->get_in_or_equal(array_values($messageids), SQL_PARAMS_NAMED);
            $sql = "
                SELECT m.*, u.email, u.picture, u.imagealt, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic,
                       u.middlename, u.alternatename
                  FROM {messages} m
                  JOIN {user} u ON u.id = m.useridfrom
                 WHERE m.id {$insqlm}
                 ORDER BY m.timecreated ASC";
            $messages = $DB->get_records_sql($sql, $paramsm);

            $digest = new email_digest($userto);

            foreach ($conversations as $c) {
                $digest->add_conversation($c);
            }
            foreach ($messages as $m) {
                $digest->add_message($m);
            }

            $htmlrenderer = $PAGE->get_renderer("message_kopereemail", "email");
            $textrenderer = $PAGE->get_renderer("message_kopereemail", "email_textemail");

            $html = $htmlrenderer->render_email_digest($digest);
            $text = $textrenderer->render_email_digest($digest);

            // Apply wrapper to digest HTML (optional).
            $fake = (object) [
                "subject" => "",
                "fullmessage" => $text,
                "fullmessagehtml" => $html,
                "userto" => $userto,
                "userfrom" => core_user::get_noreply_user(),
            ];
            $fake = template_processor::apply_wrapper($fake);
            $html = $fake->fullmessagehtml;

            $subject = get_string("messagedigestemailsubject", "message_kopereemail", format_string($SITE->shortname));
            email_to_user($userto, core_user::get_noreply_user(), $subject, $text, $html);

            // Clear queue for that user after sending.
            $DB->delete_records("message_kopereemail_messages", ["useridto" => $userid]);
        }
    }
}
