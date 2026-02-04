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
use message_kopereemail\install\file_template;

require_once(__DIR__ . "/../../../config.php");

$template = required_param("template", PARAM_TEXT);
$component = optional_param("component", "moodle", PARAM_TEXT);
$name = optional_param("name", "coursecompleted", PARAM_TEXT);

require_login();
$context = context_system::instance();
require_capability("moodle/site:config", $context);

$params = ["template" => $template, "component" => $component, "name" => $name];
$url = new moodle_url("/message/output/kopereemail/template-preview.php", $params);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout("admin");

$PAGE->set_title(get_string("template_preview", "message_kopereemail") . ": {$template}");
$PAGE->set_heading(get_string("template_preview", "message_kopereemail") . ": {$template}");

echo $OUTPUT->header();

$changuetemplate = optional_param("changuetemplate", false, PARAM_TEXT);
if ($changuetemplate) {
    require_sesskey();
    $value = file_template::wrapperhtml($template);
    if ($value) {
        set_config("wrapperhtml", $value, "message_kopereemail");

        $url = new moodle_url("/admin/settings.php", ["section" => "messagesettingkopereemail"]);
        $message = get_string("template_changued", "message_kopereemail");
        redirect($url, $message, null, notification::NOTIFY_SUCCESS);
    }
}

$contextmustache = [
    "templates" => array_map(
        function(string $templatename) {
            global $template;
            $p = ["template" => $templatename];
            return [
                "name" => $templatename,
                "previewurl" => new moodle_url("/message/output/kopereemail/template-preview.php", $p),
                "active" => $template == $templatename,
            ];
        },
        file_template::listall()
    ),
    "wrapperhtml" => file_template::wrapperhtml($template, true),
    "templatename" => $template,
    "changuetemplateurl" => new moodle_url("/message/output/kopereemail/template-preview.php", [
        "changuetemplate" => 1,
        "template" => $template,
        "sesskey" => sesskey(),
    ]),
];
echo $OUTPUT->render_from_template("message_kopereemail/template_test", $contextmustache);

echo $OUTPUT->footer();
