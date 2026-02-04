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
 * Language strings for message_kopereemail.
 *
 * @package   message_kopereemail
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['action_create'] = 'Create custom message';
$string['action_delete'] = 'Delete custom message';
$string['action_edit'] = 'Edit custom message';
$string['action_export'] = 'Export JSON';
$string['action_import'] = 'Import JSON';
$string['action_preview'] = 'Template preview';
$string['action_preview_click'] = 'Click here to receive an email with this Template test';
$string['action_preview_success'] = 'Email sent successfully. Please check your inbox or spam folder.';
$string['export_filename_prefix'] = 'kopereemail-templates';
$string['export_title'] = 'Export templates';
$string['import_file'] = 'JSON file';
$string['import_file_help'] = 'Select the JSON exported from the other environment.';
$string['import_invalid_json'] = 'Invalid file: malformed JSON.';
$string['import_invalid_payload'] = 'Invalid file: export structure does not match.';
$string['import_overwrite'] = 'Overwrite existing templates';
$string['import_overwrite_help'] = 'If checked, existing templates will be updated. If unchecked, existing templates will be ignored.';
$string['import_success'] = 'Import completed.<br>&nbsp;&gt; <strong>Imported:</strong> {$a->imported}<br>&nbsp;&gt; <strong>Skipped:</strong> {$a->skipped}<br>&nbsp;&gt; <strong>Wrapper updated:</strong> {$a->wrapper}.';
$string['import_title'] = 'Import templates';
$string['import_wrapper'] = 'Import HTML wrapper (base template)';
$string['import_wrapper_help'] = 'If checked, the JSON HTML wrapper will replace the wrapper configured in this environment.';
$string['messagedigestemailsubject'] = '{$a}: Messages digest';
$string['messages'] = 'messages';
$string['placeholders_course_data_desc'] = 'Course data (when detectable).';
$string['placeholders_course_url_desc'] = 'Course URL (when detectable).';
$string['placeholders_dates_now_desc'] = 'Current date/time (userdate).';
$string['placeholders_desc'] = 'You can use Mustache placeholders in the message.';
$string['placeholders_fullmessage_desc'] = 'Plain-text message.';
$string['placeholders_fullmessagehtml_desc'] = 'HTML message (use triple braces).';
$string['placeholders_site_fullname_desc'] = 'Full site name (<code>{$SITE->fullname}</code>).';
$string['placeholders_site_shortname_desc'] = 'Short site name (<code>{$SITE->shortname}</code>).';
$string['placeholders_site_url_desc'] = 'Site URL (<code>{$CFG->wwwroot}</code>).';
$string['placeholders_subject_desc'] = 'Current notification subject.';
$string['placeholders_title'] = 'Available placeholders';
$string['placeholders_userfrom_data_desc'] = 'Sender data.';
$string['placeholders_userto_data_desc'] = 'Recipient data.';
$string['pluginname'] = 'Kopere Email';
$string['privacy:metadata:attachment'] = 'File attached in the filesystem.';
$string['privacy:metadata:attachname'] = 'Attachment name.';
$string['privacy:metadata:fullmessage'] = 'Plain-text message.';
$string['privacy:metadata:fullmessagehtml'] = 'HTML message.';
$string['privacy:metadata:message_kopereemail_messages'] = 'Queue table for group message digest.';
$string['privacy:metadata:message_kopereemail_messages:conversationid'] = 'Conversation ID.';
$string['privacy:metadata:message_kopereemail_messages:messageid'] = 'Message ID.';
$string['privacy:metadata:message_kopereemail_messages:useridfrom'] = 'ID of the user who sent the message.';
$string['privacy:metadata:message_kopereemail_messages:useridto'] = 'ID of the user who will receive the message.';
$string['privacy:metadata:recipient'] = 'Email recipient.';
$string['privacy:metadata:replyto'] = 'Reply-to email.';
$string['privacy:metadata:replytoname'] = 'Reply-to name.';
$string['privacy:metadata:subject'] = 'Email subject.';
$string['privacy:metadata:userfrom'] = 'Email sender.';
$string['settings_customtemplates'] = 'Custom messages per provider';
$string['settings_customtemplates_desc'] = 'Create/edit a custom message for each notification provider.';
$string['settings_wrapper'] = 'Email base template (wrapper)';
$string['settings_wrapper_default'] = '
<table style="background:{$a->primarycolor}26;" role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
        <td style="padding:24px 12px;" align="center">
            <table style="width:600px;max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(18, 38, 63, 0.08);"
                   role="presentation" border="0" width="600" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td style="height:6px;background:{$a->primarycolor};line-height:6px;font-size:0;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="padding:24px 28px 12px 28px;background:#ffffff;">
                        <table role="presentation" border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td style="vertical-align:middle;" align="left">
                                    <div style="font-family:Arial, Helvetica, sans-serif;font-size:18px;font-weight:bold;color:#121826;">
                                        {{subject}}
                                    </div>
                                    <div style="margin-top:6px;font-family:Arial, Helvetica, sans-serif;font-size:13px;color:#6b7280;">
                                        {{site.shortname}}
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 28px;">
                        <div style="height:1px;background:#eef2f7;line-height:1px;font-size:0;">&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px 28px 10px 28px;">
                        <div style="font-family:Arial, Helvetica, sans-serif;font-size:15px;line-height:1.6;color:#111827;">
                            {{{fullmessagehtml}}}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 28px;background:#fbfcff;">
                        <table role="presentation" border="0" width="100%"
                               cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td style="vertical-align:top;" align="left">
                                    <div style="font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#6b7280;line-height:1.6;">
                                        {{site.fullname}}
                                    </div>
                                </td>
                                <td style="vertical-align:top;" align="right">
                                    <div style="font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#6b7280;line-height:1.6;">
                                        <a style="color:{$a->primarycolor};text-decoration:none;"
                                           href="{$a->notificationpreferencesurl}">{$a->messagepreferences}</a>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>';
$string['settings_wrapper_desc'] = 'This HTML is formatted in Mustache and will be applied as the message template to be sent to the student. The HTML content should be inserted where {{{fullmessagehtml}}} is.';
$string['table_actions'] = 'Actions';
$string['table_component'] = 'Component';
$string['table_name'] = 'Name';
$string['table_provider'] = 'Provider';
$string['tasksendemaildigest'] = 'Send digest (groups) - Kopere Email';
$string['template_delete_confirm'] = 'Are you sure you want to delete the custom message for this provider?';
$string['template_delete_title'] = 'Delete custom message';
$string['template_deleted'] = 'Custom message deleted.';
$string['template_edit_bodyhtml'] = 'Message HTML';
$string['template_edit_provider'] = 'Provider';
$string['template_edit_save'] = 'Save';
$string['template_edit_subject'] = 'Subject (optional)';
$string['template_edit_title'] = 'Custom message';
$string['template_saved'] = 'Custom message saved.';
$string['templates_transfer_desc'] = 'Use this service to migrate settings between environments (e.g., staging -> production).';
$string['templates_transfer_title'] = 'Export / Import settings';
