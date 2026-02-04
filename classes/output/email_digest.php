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
 * Renderable data structure for digest emails.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail\output;

use context_course;
use core\exception\coding_exception;
use renderer_base;
use stdClass;
use user_picture;

/**
 * Renderable data structure for digest emails.
 */
class email_digest implements \renderable, \templatable {

    /**
     * @var array Conversations keyed by conversation id.
     */
    protected $conversations = [];

    /**
     * @var array Messages keyed by conversation id.
     */
    protected $messages = [];

    /**
     * @var stdClass Recipient user.
     */
    protected $userto;

    /**
     * Constructor.
     *
     * @param stdClass $userto
     */
    public function __construct(stdClass $userto) {
        $this->userto = $userto;
    }

    /**
     * Add a conversation.
     *
     * @param stdClass $conversation
     */
    public function add_conversation(stdClass $conversation) {
        $this->conversations[$conversation->id] = $conversation;
    }

    /**
     * Add a message to the digest.
     *
     * @param stdClass $message
     */
    public function add_message(stdClass $message) {
        $this->messages[$message->conversationid][] = $message;
    }

    /**
     * Export for mustache template rendering.
     *
     * @param renderer_base $renderer
     * @return stdClass
     * @throws coding_exception
     */
    public function export_for_template(renderer_base $renderer) {
        global $PAGE;

        $data = (object) [
            "conversations" => [],
        ];

        foreach ($this->conversations as $conversation) {
            $messages = $this->messages[$conversation->id] ?? [];
            if (empty($messages)) {
                continue;
            }

            $group = (object) [
                "id" => $conversation->groupid,
                "picture" => $conversation->picture,
                "courseid" => $conversation->courseid,
            ];

            $grouppictureurl = $renderer->image_url("g/g1")->out(false);
            if (function_exists("get_group_picture_url")) {
                if ($url = get_group_picture_url($group, $group->courseid, false, true)) {
                    $grouppictureurl = $url->out(false);
                }
            }

            $coursecontext = context_course::instance($conversation->courseid);

            $conversationformatted = (object) [
                "groupname" => format_string($conversation->name, true, ["context" => $coursecontext]),
                "grouppictureurl" => $grouppictureurl,
                "coursename" => format_string($conversation->coursename, true, ["context" => $coursecontext]),
                "numberofunreadmessages" => count($messages),
                "messages" => [],
            ];

            foreach ($messages as $message) {
                $user = new stdClass();
                username_load_fields_from_object($user, $message);
                $user->picture = $message->picture;
                $user->imagealt = $message->imagealt;
                $user->email = $message->email;
                $user->id = $message->useridfrom;

                $userpicture = new user_picture($user);
                $userpicture->includetoken = true;
                $userpictureurl = $userpicture->get_url($PAGE)->out(false);

                $messageformatted = (object) [
                    "userpictureurl" => $userpictureurl,
                    "userfullname" => fullname($user),
                    "message" => function_exists("message_format_message_text") ? message_format_message_text($message) :
                        $message->smallmessage,
                    "timesent" => userdate($message->timecreated),
                ];

                $conversationformatted->messages[] = $messageformatted;
            }

            $data->conversations[] = $conversationformatted;
        }

        return $data;
    }
}
