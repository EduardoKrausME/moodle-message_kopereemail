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
 * Provider label utilities.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail;

use coding_exception;

/**
 * Provider label utilities.
 */
class provider_helper {

    /**
     * Resolve provider display name from component lang string "messageprovider:{name}".
     *
     * @param string $component Provider component.
     * @param string $name Provider name.
     * @return string
     * @throws coding_exception
     */
    public static function get_display_name($component, $name) {
        $key = "messageprovider:{$name}";
        $sm = get_string_manager();

        if ($sm->string_exists($key, $component)) {
            return get_string($key, $component);
        }

        return "{$component} / {$name}";
    }
}
