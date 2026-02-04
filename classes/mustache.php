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
 * Mustache rendering helpers (render templates stored as strings).
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail;

use core\output\mustache_engine;
use stdClass;

/**
 * Mustache rendering helpers (render templates stored as strings).
 */
class mustache {

    /**
     * Render a mustache template string with a context.
     *
     * @param string $template
     * @param array|stdClass $contextmustache
     * @return string
     */
    public static function render_string($template, $contextmustache) {
        $engine = new mustache_engine();
        return $engine->render($template, $contextmustache);
    }
}
