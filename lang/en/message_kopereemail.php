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

$string['action_create'] = 'Criar mensagem customizada';
$string['action_delete'] = 'Excluir mensagem customizada';
$string['action_edit'] = 'Editar mensagem customizada';
$string['messagedigestemailsubject'] = '{$a}: Messages digest';
$string['messages'] = 'mensagens';
$string['placeholders_course_data_desc'] = 'Dados do curso (quando detectável).';
$string['placeholders_course_url_desc'] = 'URL do curso (quando detectável).';
$string['placeholders_dates_now_desc'] = 'Data/hora atual (userdate).';
$string['placeholders_desc'] = 'Você pode usar Mustache placeholders na mensagem.
<br><em>PS: No título não há suporte</em>';
$string['placeholders_fullmessage_desc'] = 'Mensagem em texto puro.';
$string['placeholders_fullmessagehtml_desc'] = 'Mensagem em HTML (use triple braces).';
$string['placeholders_site_fullname_desc'] = 'Nome completo do site (<code>{$SITE->fullname}</code>).';
$string['placeholders_site_shortname_desc'] = 'Nome curto do site (<code>{$SITE->shortname}</code>).';
$string['placeholders_site_url_desc'] = 'URL do site (<code>{$CFG->wwwroot}</code>).';
$string['placeholders_subject_desc'] = 'Assunto atual da notificação.';
$string['placeholders_title'] = 'Placeholders disponíveis';
$string['placeholders_userfrom_data_desc'] = 'Dados do remetente.';
$string['placeholders_userto_data_desc'] = 'Dados do destinatário.';
$string['pluginname'] = 'Kopere Email';
$string['privacy:metadata:attachment'] = 'Arquivo anexado no filesystem.';
$string['privacy:metadata:attachname'] = 'Nome do anexo.';
$string['privacy:metadata:fullmessage'] = 'Mensagem em texto puro.';
$string['privacy:metadata:fullmessagehtml'] = 'Mensagem em HTML.';
$string['privacy:metadata:message_kopereemail_messages'] = 'Tabela de fila para digest de mensagens de grupo.';
$string['privacy:metadata:message_kopereemail_messages:conversationid'] = 'ID da conversa.';
$string['privacy:metadata:message_kopereemail_messages:messageid'] = 'ID da mensagem.';
$string['privacy:metadata:message_kopereemail_messages:useridfrom'] = 'ID do usuário que enviou a mensagem.';
$string['privacy:metadata:message_kopereemail_messages:useridto'] = 'ID do usuário que receberá a mensagem.';
$string['privacy:metadata:recipient'] = 'Destinatário do e-mail.';
$string['privacy:metadata:replyto'] = 'E-mail de reply-to.';
$string['privacy:metadata:replytoname'] = 'Nome do reply-to.';
$string['privacy:metadata:subject'] = 'Assunto do e-mail.';
$string['privacy:metadata:userfrom'] = 'Remetente do e-mail.';
$string['settings_customtemplates'] = 'Mensagens customizadas por provedor';
$string['settings_customtemplates_desc'] = 'Crie/edite uma mensagem customizada para cada provedor de notificação.';
$string['settings_wrapper'] = 'Template base (wrapper) do e-mail';
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
$string['settings_wrapper_desc'] = 'Este HTML é formatao em Mustache será aplicado como template da mensagem a ser enviado ao aluno. No local {{{fullmessagehtml}}} no local que deve ser inserido o conteúdo HTML.';
$string['table_actions'] = 'Ações';
$string['table_component'] = 'Componente';
$string['table_name'] = 'Nome';
$string['table_provider'] = 'Provedor';
$string['tasksendemaildigest'] = 'Envio de digest (grupos) - Kopere Email';
$string['template_delete_confirm'] = 'Tem certeza que deseja excluir a mensagem customizada deste provedor?';
$string['template_delete_title'] = 'Excluir mensagem customizada';
$string['template_deleted'] = 'Mensagem customizada excluída.';
$string['template_edit_bodyhtml'] = 'HTML da mensagem';
$string['template_edit_provider'] = 'Provedor';
$string['template_edit_save'] = 'Salvar';
$string['template_edit_subject'] = 'Assunto (opcional)';
$string['template_edit_title'] = 'Mensagem customizada';
$string['template_saved'] = 'Mensagem customizada salva.';
