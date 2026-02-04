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
 * Base renderer for email digest templates.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail\output;

use core\exception\moodle_exception;

/**
 * Base renderer for email digest templates.
 */
class renderer extends \plugin_renderer_base {

    /**
     * Render email digest using the current renderer template.
     *
     * @param email_digest $emaildigest
     * @return string
     * @throws moodle_exception
     */
    public function render_email_digest(email_digest $emaildigest) {
        $data = $emaildigest->export_for_template($this);
        return $this->render_from_template("message_kopereemail/{$this->get_template_name()}", $data);
    }

    /**
     * Return mustache template file name for this renderer.
     *
     * @return string
     */
    public function get_template_name() {
        return "email_digest_html";
    }
}
