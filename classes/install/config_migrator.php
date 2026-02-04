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
 * Helpers to migrate/replace output names inside config_plugins values.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail\install;

/**
 * Helpers to migrate/replace output names inside config_plugins values.
 */
class config_migrator {

    /**
     * Replace output name in a config value.
     *
     * @param string $value
     * @param string $from
     * @param string $to
     * @return string
     */
    public static function replace_output_name($value, $from, $to) {
        $trim = ltrim($value);
        if ($trim !== "" && ($trim[0] === "{" || $trim[0] === "[")) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $decoded = self::replace_recursive($decoded, $from, $to);
                return json_encode($decoded);
            }
        }

        // Word-boundary replace for plain strings.
        return preg_replace("/\\b" . preg_quote($from, "/") . "\\b/", $to, $value);
    }

    /**
     * Recursively replace string entries in arrays.
     *
     * @param mixed $data
     * @param string $from
     * @param string $to
     * @return mixed
     */
    private static function replace_recursive($data, $from, $to) {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::replace_recursive($v, $from, $to);
            }
            return $data;
        }

        if (is_string($data)) {
            return preg_replace("/\\b" . preg_quote($from, "/") . "\\b/", $to, $data);
        }

        return $data;
    }
}
