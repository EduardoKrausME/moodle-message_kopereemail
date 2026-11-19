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
 * Tests for formatting message bodies for email.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace message_kopereemail;

/**
 * Email formatter tests.
 *
 * @covers \message_kopereemail\email_formatter
 */
final class email_formatter_test extends \advanced_testcase {

    /**
     * HTML must not be escaped when its declared format is HTML.
     */
    public function test_html_is_preserved_for_string_and_integer_formats(): void {
        $html = '<p>Hello <strong>world</strong> <a href="https://example.org/">link</a></p>';

        foreach ([FORMAT_HTML, (int) FORMAT_HTML] as $format) {
            $this->assertSame($html, email_formatter::to_html($html, $format));
        }
    }

    /**
     * Moodle-format messages must use the core formatting function.
     */
    public function test_moodle_format_uses_core_formatter_for_string_and_integer_formats(): void {
        $message = '<p>Hello <strong>world</strong></p>';
        $expected = format_text($message, FORMAT_MOODLE, ['filter' => false]);

        foreach ([FORMAT_MOODLE, (int) FORMAT_MOODLE] as $format) {
            $actual = email_formatter::to_html($message, $format);
            $this->assertSame($expected, $actual);
            $this->assertStringNotContainsString('&lt;p&gt;', $actual);
        }
    }

    /**
     * Plain text must never introduce executable or other HTML markup.
     */
    public function test_plain_text_is_escaped_for_string_integer_and_default_formats(): void {
        $message = "First <b>bold</b> & <script>alert(1)</script>\nSecond line";
        $expected = email_formatter::plain_to_html($message);

        foreach ([FORMAT_PLAIN, (int) FORMAT_PLAIN] as $format) {
            $this->assertSame($expected, email_formatter::to_html($message, $format));
        }
        $this->assertSame($expected, email_formatter::to_html($message));
        $this->assertStringContainsString('&lt;script&gt;', $expected);
        $this->assertStringContainsString('&lt;b&gt;', $expected);
        $this->assertStringNotContainsString('<script>', $expected);
        $this->assertStringNotContainsString('<b>', $expected);
        $this->assertStringContainsString('<br', $expected);
    }

    /**
     * Markdown must continue to be formatted by Moodle rather than escaped as plain text.
     */
    public function test_markdown_uses_core_formatter_for_string_and_integer_formats(): void {
        $message = '**Bold** and [link](https://example.org/)';
        $expected = format_text($message, FORMAT_MARKDOWN, ['filter' => false]);

        foreach ([FORMAT_MARKDOWN, (int) FORMAT_MARKDOWN] as $format) {
            $this->assertSame($expected, email_formatter::to_html($message, $format));
        }
    }

    /**
     * Unknown formats must retain the conservative plain-text fallback.
     */
    public function test_unknown_format_falls_back_to_escaped_plain_text(): void {
        $message = '<img src=x onerror=alert(1)>';
        $expected = email_formatter::plain_to_html($message);

        $this->assertSame($expected, email_formatter::to_html($message, '99'));
        $this->assertSame($expected, email_formatter::to_html($message, 99));
        $this->assertStringNotContainsString('<img', $expected);
    }

    /**
     * An empty body must remain empty in every format.
     */
    public function test_empty_message_remains_empty(): void {
        foreach ([FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN] as $format) {
            $this->assertSame('', email_formatter::to_html(" \n\t ", $format));
        }
    }
}
