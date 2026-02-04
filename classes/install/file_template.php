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
 * template
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail\install;

use coding_exception;
use dml_exception;
use moodle_url;

/**
 * Class file_template
 */
class file_template {
    /**
     * Function wrapperhtml
     *
     * @param string $themename
     * @param bool $test
     * @return string
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function wrapperhtml($themename, $test = false) {
        global $CFG, $OUTPUT;

        $html = file_get_contents("{$CFG->dirroot}/message/output/kopereemail/assets/templates/{$themename}.html");
        if (!$html) {
            return "{{{fullmessagehtml}}}";
        }
        $a = [
            "messagepreferences" => get_string("messagepreferences", "message"),
            "notificationpreferencesurl" => new moodle_url("/message/notificationpreferences.php"),
            "primarycolor" => get_config("theme_boost", "brandcolor"),
        ];

        foreach ($a as $key => $value) {
            $html = str_replace("[{$key}]", $value, $html);
        }
        if ($test) {
            $a = [
                "site.fullname" => $SITE->fullname ?? "",
                "site.shortname" => $SITE->shortname ?? "",
                "site.url" => new moodle_url("/"),
                "site.logourl" => $OUTPUT->get_logo_url(),
                "site.compact_logourl" => $OUTPUT->get_compact_logo_url(),
                "dates.now" => userdate(time()),
            ];
            foreach ($a as $key => $value) {
                $html = str_replace("{{{{$key}}}}", $value, $html);
                $html = str_replace("{{{$key}}}", $value, $html);
            }
        }

        return $html;
    }

    /**
     * Function listall
     *
     * @return array
     */
    public static function listall() {
        global $CFG;
        $files = glob("{$CFG->dirroot}/message/output/kopereemail/assets/templates/*.html");

        $templates = [];
        foreach ($files as $file) {
            $templates[] = pathinfo($file, PATHINFO_FILENAME);
        }

        return $templates;
    }
}
