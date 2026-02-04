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
 * Applies custom provider templates and the wrapper HTML.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail;

use core\exception\moodle_exception;
use dml_exception;
use stdClass;

/**
 * Applies custom provider templates and the wrapper HTML.
 */
class template_processor {

    /**
     * Apply custom template + ensure HTML + apply wrapper.
     *
     * @param stdClass $eventdata
     * @return stdClass
     * @throws dml_exception
     * @throws moodle_exception
     */
    public static function apply_all(stdClass $eventdata) {
        $eventdata = self::apply_custom_template($eventdata);
        $eventdata = self::ensure_html($eventdata);
        $eventdata = self::apply_wrapper($eventdata);
        return $eventdata;
    }

    /**
     * If a provider template exists, replace subject/body.
     *
     * @param stdClass $eventdata
     * @return stdClass
     * @throws moodle_exception
     * @throws dml_exception
     */
    public static function apply_custom_template(stdClass $eventdata) {
        if (empty($eventdata->component) || empty($eventdata->name)) {
            return $eventdata;
        }

        $tpl = template_repository::get_by_provider($eventdata->component, $eventdata->name);
        if (!$tpl) {
            return $eventdata;
        }

        $contextmustache = placeholders::build_context($eventdata);

        $subject = trim($tpl->subject);
        if ($subject !== "") {
            $eventdata->subject = mustache::render_string($subject, $contextmustache);
        }

        $html = mustache::render_string($tpl->bodyhtml, $contextmustache);
        $eventdata->fullmessagehtml = $html;
        $eventdata->fullmessage = email_formatter::html_to_plain($html);

        return $eventdata;
    }

    /**
     * Ensure fullmessagehtml exists (generate from fullmessage if missing).
     *
     * @param stdClass $eventdata
     * @return stdClass
     */
    public static function ensure_html(stdClass $eventdata) {
        if (!empty($eventdata->fullmessagehtml)) {
            return $eventdata;
        }

        if (!empty($eventdata->fullmessage)) {
            $eventdata->fullmessagehtml = email_formatter::plain_to_html($eventdata->fullmessage);
        }

        return $eventdata;
    }

    /**
     * Apply wrapper HTML if we have HTML content.
     *
     * @param stdClass $eventdata
     * @return stdClass
     * @throws dml_exception
     */
    public static function apply_wrapper(stdClass $eventdata) {
        $wrapper = get_config("message_kopereemail", "wrapperhtml");
        $html = $eventdata->fullmessagehtml ?? "";

        if (trim($wrapper) === "" || trim($html) === "") {
            return $eventdata;
        }

        // Allow wrapper to also use placeholders, while keeping fullmessagehtml unescaped.
        $token = "___KOPEREEMAIL_FULLMESSAGEHTML___";
        $prepared = str_replace(["{{{fullmessagehtml}}}", "{{fullmessagehtml}}"], $token, $wrapper);

        $contextmustache = placeholders::build_context($eventdata);
        $rendered = mustache::render_string($prepared, $contextmustache);

        $eventdata->fullmessagehtml = str_replace($token, $html, $rendered);

        return $eventdata;
    }
}
