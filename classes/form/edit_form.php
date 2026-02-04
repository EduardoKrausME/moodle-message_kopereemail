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
 * edit_form.php
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail\form;

use coding_exception;
use moodleform;

require_once("{$CFG->libdir}/formslib.php");

/**
 * phpcs:disable moodle.Files.MoodleInternal.MoodleInternalGlobalState
 *
 * Form to create/edit a provider custom template.
 */
class edit_form extends moodleform {

    /**
     * Define form fields.
     *
     * @throws coding_exception
     */
    public function definition() {
        $mform = $this->_form;
        $custom = $this->_customdata;

        $component = $custom["component"];
        $name = $custom["name"];

        $mform->addElement("hidden", "component", $component);
        $mform->setType("component", PARAM_COMPONENT);

        $mform->addElement("hidden", "name", $name);
        $mform->setType("name", PARAM_ALPHANUMEXT);

        $title = get_string("template_edit_subject", "message_kopereemail");
        $mform->addElement("text", "subject", $title, ["size" => 90]);
        $mform->setType("subject", PARAM_TEXT);

        $title = get_string("template_edit_bodyhtml", "message_kopereemail");
        $mform->addElement("editor", "bodyhtml", $title, null, [
            "maxfiles" => 0,
            "maxbytes" => 0,
            "trusttext" => 1,
        ]);
        $mform->setType("bodyhtml", PARAM_RAW);

        $this->add_action_buttons(true, get_string("template_edit_save", "message_kopereemail"));
    }
}
