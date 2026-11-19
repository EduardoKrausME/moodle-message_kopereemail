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
 * Tests for the conversion of message event data to HTML.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail;

/**
 * Template processor tests.
 *
 * @covers \message_kopereemail\template_processor
 */
final class template_processor_test extends \advanced_testcase {

    /**
     * Stored Moodle messages arrive without fullmessagehtml and may have integer formats.
     */
    public function test_ensure_html_formats_moodle_messages(): void {
        $message = '<p>Direct <strong>message</strong></p>';
        $expected = format_text($message, FORMAT_MOODLE, ['filter' => false]);

        foreach ([FORMAT_MOODLE, (int) FORMAT_MOODLE] as $format) {
            $eventdata = (object) [
                'fullmessage' => $message,
                'fullmessageformat' => $format,
            ];
            $result = template_processor::ensure_html($eventdata);
            $this->assertSame($expected, $result->fullmessagehtml);
            $this->assertStringNotContainsString('&lt;p&gt;', $result->fullmessagehtml);
        }
    }

    /**
     * HTML-format messages should retain markup after the event-data conversion.
     */
    public function test_ensure_html_preserves_html_messages(): void {
        $message = '<p>HTML <a href="https://example.org/">link</a></p>';

        foreach ([FORMAT_HTML, (int) FORMAT_HTML] as $format) {
            $eventdata = (object) [
                'fullmessage' => $message,
                'fullmessageformat' => $format,
            ];
            $result = template_processor::ensure_html($eventdata);
            $this->assertSame($message, $result->fullmessagehtml);
        }
    }

    /**
     * HTML generated from plain messages must be safe, including when no format is set.
     */
    public function test_ensure_html_escapes_plain_messages(): void {
        $message = '<script>alert(1)</script> and <b>not HTML</b>';
        $expected = email_formatter::plain_to_html($message);

        foreach ([FORMAT_PLAIN, (int) FORMAT_PLAIN, null] as $format) {
            $eventdata = (object) ['fullmessage' => $message];
            if ($format !== null) {
                $eventdata->fullmessageformat = $format;
            }
            $result = template_processor::ensure_html($eventdata);
            $this->assertSame($expected, $result->fullmessagehtml);
        }
    }

    /**
     * Existing HTML (for example, from a notification template) must not be replaced.
     */
    public function test_ensure_html_leaves_existing_html_untouched(): void {
        $eventdata = (object) [
            'fullmessage' => '<p>Original</p>',
            'fullmessageformat' => FORMAT_PLAIN,
            'fullmessagehtml' => '<div>Custom template</div>',
        ];

        $this->assertSame($eventdata, template_processor::ensure_html($eventdata));
        $this->assertSame('<div>Custom template</div>', $eventdata->fullmessagehtml);
    }

    /**
     * Missing message bodies must not be synthesized.
     */
    public function test_ensure_html_does_not_generate_empty_messages(): void {
        $eventdata = (object) ['fullmessage' => ''];
        $result = template_processor::ensure_html($eventdata);
        $this->assertFalse(isset($result->fullmessagehtml));
    }
}
