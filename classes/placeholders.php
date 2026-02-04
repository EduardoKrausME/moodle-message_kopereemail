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
 * Build placeholder context and definitions for admin UI.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail;

use context_course;
use core\exception\moodle_exception;
use dml_exception;
use moodle_url;
use stdClass;

/**
 * Build placeholder context and definitions for admin UI.
 */
class placeholders {

    /**
     * Return a curated list of placeholder definitions for the editor UI.
     *
     * @return array
     */
    public static function get_definitions() {
        global $SITE, $CFG;

        $a = [
            "site_fullname" => $SITE->fullname,
            "site_shortname" => $SITE->shortname,
            "cfg_wwwroot" => $CFG->wwwroot,
        ];

        return [
            [
                "key" => "{{subject}}",
                "desc" => get_string("placeholders_subject_desc", "message_kopereemail"),
            ],
            [
                "key" => "{{fullmessage}}",
                "desc" => get_string("placeholders_fullmessage_desc", "message_kopereemail"),
            ],
            [
                "key" => "{{{fullmessagehtml}}}",
                "desc" => get_string("placeholders_fullmessagehtml_desc", "message_kopereemail"),
            ],
            [
                "key" => "{{site.fullname}}",
                "desc" => get_string("placeholders_site_fullname_desc", "message_kopereemail", $a),
            ],
            [
                "key" => "{{site.shortname}}",
                "desc" => get_string("placeholders_site_shortname_desc", "message_kopereemail", $a),
            ],
            [
                "key" => "{{site.url}}",
                "desc" => get_string("placeholders_site_url_desc", "message_kopereemail", $a),
            ],
            [
                "key" => "{{course.url}}",
                "desc" => get_string("placeholders_course_url_desc", "message_kopereemail"),
            ],
            [
                "key" => "{{course.fullname}} / {{course.shortname}} / {{course.id}}",
                "desc" => get_string("placeholders_course_data_desc", "message_kopereemail"),
            ],
            [
                "key" => "{{userto.firstname}} / {{userto.lastname}} / {{userto.email}}",
                "desc" => get_string("placeholders_userto_data_desc", "message_kopereemail"),
            ],
            [
                "key" => "{{userfrom.firstname}} / {{userfrom.lastname}} / {{userfrom.email}}",
                "desc" => get_string("placeholders_userfrom_data_desc", "message_kopereemail"),
            ],
            [
                "key" => "{{dates.now}}",
                "desc" => get_string("placeholders_dates_now_desc", "message_kopereemail"),
            ],
        ];
    }

    /**
     * Build the mustache context from eventdata.
     *
     * @param stdClass $eventdata
     * @return stdClass
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function build_context(stdClass $eventdata) {
        global $SITE;

        $contextmustache = (object) [
            "course" => [],
        ];

        $contextmustache->subject = $eventdata->subject ?? "";
        $contextmustache->fullmessage = $eventdata->fullmessage ?? "";
        $contextmustache->fullmessagehtml = $eventdata->fullmessagehtml ?? "";

        $contextmustache->site = (object) [
            "fullname" => $SITE->fullname ?? "",
            "shortname" => $SITE->shortname ?? "",
            "url" => (new moodle_url("/"))->out(false),
        ];

        $contextmustache->userto = $eventdata->userto ?? (object) [];
        $contextmustache->userfrom = $eventdata->userfrom ?? (object) [];

        $contextmustache->dates = (object) [
            "now" => userdate(time()),
        ];

        // Best-effort course detection.
        if (!empty($eventdata->courseid)) {
            $course = get_course($eventdata->courseid);
            if ($course) {
                $context = context_course::instance($course->id);
                $contextmustache->course = (object) [
                    "id" => $course->id,
                    "fullname" => format_string($course->fullname, true, ['context' => $context]),
                    "shortname" => $course->shortname,
                    "summary" => format_string($course->summary, true, ['context' => $context]),
                    "url" => (new moodle_url("/course/view.php", ["id" => $course->id]))->out(false),
                ];
            }
        }

        return $contextmustache;
    }
}
