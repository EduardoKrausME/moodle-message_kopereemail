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
 * Message output processor: Kopere Email.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_message\api;
use message_kopereemail\template_processor;

// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalGlobalState
require_once("{$CFG->dirroot}/message/output/lib.php");

/**
 * The Kopere Email message processor.
 */
class message_output_kopereemail extends message_output {

    /**
     * Send a message via email using KopereEmail rules:
     *
     * @param object $eventdata The event data submitted by the message sender.
     * @return bool True on success.
     * @throws coding_exception
     * @throws dml_exception
     */
    public function send_message($eventdata) {
        global $CFG, $DB;

        // Skip suspended/deleted/no-login users.
        if (!empty($eventdata->userto) && (
                $eventdata->userto->auth === "nologin" ||
                !empty($eventdata->userto->suspended) ||
                !empty($eventdata->userto->deleted)
            )) {
            return true;
        }

        // Apply custom templates + wrapper + ensure HTML.
        $eventdata = template_processor::apply_all($eventdata);

        // Determine recipient (allow override email preference).
        $recipient = $eventdata->userto;
        $prefemail = get_user_preferences("message_processor_kopereemail_email", null, $recipient);
        if (!empty($prefemail)) {
            $prefemail = clean_param($prefemail, PARAM_EMAIL);
            if (!empty($prefemail)) {
                $recipient = clone($recipient);
                $recipient->email = $prefemail;
            }
        }

        // Attachment support (same behavior as email output).
        $attachment = "";
        $attachname = "";
        if (!empty($CFG->allowattachments) && !empty($eventdata->attachment)) {
            if (empty($eventdata->attachname)) {
                debugging("Attachments should have a file name. No attachments have been sent.", DEBUG_DEVELOPER);
            } else if (!($eventdata->attachment instanceof stored_file)) {
                debugging("Attachments should be of type stored_file. No attachments have been sent.", DEBUG_DEVELOPER);
            } else {
                $attachment = $eventdata->attachment->copy_content_to_temp();
                $attachname = $eventdata->attachname;
            }
        }

        // Reply-to handling.
        $replyto = "";
        $replytoname = "";
        if (!empty($eventdata->replyto)) {
            $replyto = $eventdata->replyto;
        }
        if (!empty($eventdata->replytoname)) {
            $replytoname = $eventdata->replytoname;
        }

        // Group conversations: store for digest to avoid immediate spam.
        $emailuser = true;
        if (!empty($eventdata->conversationtype) &&
            $eventdata->conversationtype == api::MESSAGE_CONVERSATION_TYPE_GROUP) {
            $emailuser = false;
        }

        if ($emailuser) {
            $result = email_to_user(
                $recipient,
                $eventdata->userfrom,
                $eventdata->subject,
                $eventdata->fullmessage,
                $eventdata->fullmessagehtml,
                $attachment,
                $attachname,
                true,
                $replyto,
                $replytoname
            );
        } else {
            $messagetosend = new stdClass();
            $messagetosend->useridfrom = $eventdata->userfrom->id;
            $messagetosend->useridto = $recipient->id;
            $messagetosend->conversationid = $eventdata->convid;
            $messagetosend->messageid = $eventdata->savedmessageid;
            $result = $DB->insert_record("message_kopereemail_messages", $messagetosend, false);
        }

        // Cleanup attachment temp file if created.
        if (!empty($attachment) && file_exists($attachment)) {
            unlink($attachment);
        }

        return $result;
    }

    /**
     * This output is always considered configured as long as Moodle email works.
     *
     * @return bool
     */
    public function is_system_configured() {
        return true;
    }

    /**
     * Returns true as message can be sent to internal support user.
     *
     * @return bool
     */
    public function can_send_to_any_users() {
        return true;
    }

    /**
     * Creates necessary fields in the messaging config form.
     *
     * @param array $preferences An array of user preferences
     */
    public function config_form($preferences) {
        return null;
    }

    /**
     * Parses the submitted form data and saves it into preferences array.
     *
     * @param stdClass $form preferences form class
     * @param array $preferences preferences array
     */
    public function process_form($form, &$preferences) {
        return true;
    }

    /**
     * Loads the config data from database to put on the form during initial form display
     *
     * @param array $preferences preferences array
     * @param int $userid the user id
     */
    public function load_data(&$preferences, $userid) {
        return true;
    }
}
