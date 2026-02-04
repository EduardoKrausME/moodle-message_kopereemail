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
 * import_form.php
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail\form;

use context_user;

// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalGlobalState
require_once("{$CFG->libdir}/formslib.php");

/**
 * Form for importing templates JSON.
 *
 * @package    message_kopereemail
 */
class import_form extends \moodleform {

    /**
     * Define form fields.
     */
    public function definition() {
        global $USER;

        $mform = $this->_form;

        $options = [
            "accepted_types" => [".json"],
            // Ensure the picker uses the expected draft context.
            "context" => context_user::instance($USER->id),
        ];

        $mform->addElement("filepicker", "importfile", get_string("import_file", "message_kopereemail"), null, $options);
        $mform->addHelpButton("importfile", "import_file", "message_kopereemail");

        $mform->addElement("advcheckbox", "overwrite", get_string("import_overwrite", "message_kopereemail"));
        $mform->addHelpButton("overwrite", "import_overwrite", "message_kopereemail");
        $mform->setDefault("overwrite", 1);

        $mform->addElement("advcheckbox", "importwrapper", get_string("import_wrapper", "message_kopereemail"));
        $mform->addHelpButton("importwrapper", "import_wrapper", "message_kopereemail");
        $mform->setDefault("importwrapper", 1);

        $this->add_action_buttons(true, get_string("action_import", "message_kopereemail"));
    }

    /**
     * Basic validation.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (empty($data["importfile"])) {
            $errors["importfile"] = get_string("required");
        }

        return $errors;
    }
}
