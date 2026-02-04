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
 * SQL table listing all message providers with actions to manage custom templates.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail\table;

use coding_exception;
use core\exception\moodle_exception;
use core_table\sql_table;
use html_writer;
use message_kopereemail\provider_helper;
use moodle_url;
use stdClass;

/**
 * SQL table listing all message providers with actions to manage custom templates.
 */
class providers_table extends sql_table {

    /**
     * Configure the SQL table (columns, SQL and headers).
     *
     * @param string $uniqueid Table unique id.
     * @throws coding_exception
     */
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);

        $this->define_columns(["providername", "component", "name", "actions"]);
        $this->define_headers([
            get_string("table_provider", "message_kopereemail"),
            get_string("table_component", "message_kopereemail"),
            get_string("table_name", "message_kopereemail"),
            get_string("table_actions", "message_kopereemail"),
        ]);

        $fields = "mp.id, mp.component, mp.name, mkt.id AS templateid";
        $from = "{message_providers} mp
                 LEFT JOIN {message_kopereemail_template} mkt
                        ON mkt.component = mp.component AND mkt.name = mp.name";
        $where = "1 = 1";

        $this->set_sql($fields, $from, $where);
        $this->sortable(true, "component");
        $this->collapsible(false);
        $this->pageable(true);
    }

    /**
     * Render provider display name.
     *
     * @param stdClass $row Row data.
     * @return string
     * @throws coding_exception
     */
    public function col_providername($row) {
        return provider_helper::get_display_name($row->component, $row->name);
    }

    /**
     * Render actions column.
     *
     * @param stdClass $row Row data.
     * @return string HTML.
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function col_actions($row) {
        $options = ["component" => $row->component, "name" => $row->name];
        $editurl = new moodle_url("/message/output/kopereemail/edit.php", $options);
        $deleteurl = new moodle_url("/message/output/kopereemail/delete.php", $options);
        $testurl = new moodle_url("/message/output/kopereemail/template-test.php", $options);

        if (!empty($row->templateid)) {
            $title = get_string("action_edit", "message_kopereemail");
            $edit = html_writer::link($editurl, $title, ["class" => "btn btn-sm btn-info me-1"]);

            $title = get_string("action_delete", "message_kopereemail");
            $del = html_writer::link($deleteurl, $title, ["class" => "btn btn-sm btn-danger"]);

            $title = get_string("action_preview_click", "message_kopereemail");
            $test = html_writer::link($testurl, $title, ["class" => "btn btn-sm btn-success"]);

            return $edit . $del . $test;
        } else {
            $title = get_string("action_create", "message_kopereemail");
            $edit = html_writer::link($editurl, $title, ["class" => "btn btn-sm btn-success"]);

            return $edit;
        }
    }
}
