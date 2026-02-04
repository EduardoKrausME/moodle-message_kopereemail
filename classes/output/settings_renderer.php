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
 * Helpers to render dynamic content inside admin settings.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail\output;

use coding_exception;
use core\exception\moodle_exception;
use message_kopereemail\table\providers_table;
use moodle_url;

/**
 * phpcs:disable moodle.PHP.ForbiddenGlobalUse.BadGlobal
 *
 * Helpers to render dynamic content inside admin settings.
 */
class settings_renderer {

    /**
     * Render the providers table section for settings.php.
     *
     * @return string HTML.
     * @throws coding_exception
     * @throws moodle_exception
     */
    public static function render_providers_section() {
        global $CFG, $PAGE, $OUTPUT;

        require_once("{$CFG->libdir}/tablelib.php");

        $table = new providers_table("message_kopereemail_providers");
        $table->define_baseurl($PAGE->url);

        ob_start();
        $table->out(200, true);
        $tablehtml = ob_get_clean();

        return $OUTPUT->render_from_template("message_kopereemail/admin_providers", [
            "tablehtml" => $tablehtml,
        ]);
    }
}
