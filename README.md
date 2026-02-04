# message_kopereemail — Kopere Email (Message output)

**message_kopereemail** is a Moodle *message output* based on `message_email`, created to allow **full customization of the email** sent by Moodle, while also applying a **base template** to standardize visual identity.

The idea is simple:

* Moodle keeps sending notifications as usual.
* `message_kopereemail` intercepts email delivery and:

  * **If there is a custom template for that provider** → replaces the original content with the custom HTML.
  * **If there is final HTML** → applies an **HTML wrapper** (base template) on top.
  * **If there is no HTML** → generates simple HTML from the plain text (and keeps the plain text too).

## What the plugin solves (in practice)

* Standardize email layout with consistent header/footer and styles (wrapper).
* Create “nice” tailored messages for specific events (e.g., *Course completed*).
* Adjust email subject and body without touching core / without theme hacks.
* Centralize management of customized messages in an admin page.

## Automatic configuration migration (what happens on install)

When the plugin is installed, it performs a planned migration to take the “place” of the default email:

* Copies existing configs from `message/email` to the new output (e.g., `email_provider_*` → `kopereemail_provider_*`)
* Adjusts `message_provider_*_enabled` flags by replacing `email` with `kopereemail`
* Sets lock (`email_provider_*_locked = 1`) for all providers
* Disables the old `email` processor in `mdl_message_processors`

And on uninstall:

* reverts `kopereemail` → `email` in the relevant configs

> This exists to make it easier so students don’t receive two emails: one with the horrible default Moodle layout and another with the template.

## How the plugin works

### 1) Provider-based content replacement (message provider)

Each Moodle notification comes from a **message provider**, defined in `mdl_message_providers` (usually something like `component` + `name`).

The plugin adds a customization layer:

* You choose a provider (e.g., `core` + `coursecompleted`).
* Create a **custom HTML template** for it.
* From then on, whenever that provider is triggered, the email will use the custom HTML.

> Result: the “default Moodle” text is not sent when a custom template exists for that provider.

### 2) HTML wrapper (base email template)

In the plugin settings you define an **HTML wrapper**, which becomes the template all emails will receive, for example:

* `<header>…</header>`
* a central block where the content is inserted via `{{{fullmessagehtml}}}`
* `<footer>…</footer>`

The wrapper is only applied when final HTML exists.

**Rule:**

* if `fullmessagehtml` exists → apply wrapper
* if it doesn’t → the plugin creates simple HTML from `fullmessage` and then applies the wrapper (if configured)

This keeps visual consistency without rewriting every template from scratch.

### 3) HTML vs plain text (fullmessagehtml / fullmessage)

Moodle works with:

* `fullmessage` (plain text)
* `fullmessagehtml` (HTML)

`message_kopereemail` ensures that:

* There is always “sendable” HTML
* And plain text continues to exist (for clients that prefer text, logs, etc.)

When you create a custom HTML template:

* the plugin uses that HTML
* and tries to generate `fullmessage` from the HTML (best-effort)

### 4) Group conversation messages (digest)

For **group** conversations, the plugin avoids sending an instant individual email for each message.

Instead it:

* places records into a queue (plugin table)
* an **scheduled task** runs and sends a **digest** (summary) of the messages

This reduces spam and improves the experience in heavy chat environments.

## How to use (admin)

### 1) Configure the HTML wrapper

Typical path:

* Site administration → General → Messaging → **Kopere Email**

> It’s under the `General` tab, not the `Plugins` tab.

You’ll find an editor where you define the **base email template** (wrapper).

**Tip:** use `{{{fullmessagehtml}}}` (triple braces) to insert the final HTML unescaped. If you use only two `{{`, the plugin will convert it to `{{{`

### 2) Create/edit custom messages per provider

On the same settings page, the plugin lists all providers from `mdl_message_providers` in a table with buttons:

* **Create custom message**
* **Edit custom message**
* **Delete custom message**

Flow:

1. Click **Create** (or **Edit**)
2. Edit the message HTML
3. (Optional) Set a custom subject
4. Save

From that moment on, that provider will use your message content.

## Available placeholders (templates)

The template editor supports Mustache-style placeholders.

Common examples:

* `{{subject}}`
* `{{fullmessage}}`
* `{{{fullmessagehtml}}}`
* `{{site.fullname}}`, `{{site.shortname}}`, `{{site.url}}`
* `{{userto.id}}`, `{{userto.firstname}}`, `{{userto.lastname}}`, `{{userto.email}}`
* `{{userfrom.id}}`, `{{userfrom.firstname}}`, `{{userfrom.lastname}}`, `{{userfrom.email}}`
* `{{dates.now}}`
* `{{course.id}}`, `{{course.fullname}}`, `{{course.shortname}}`, `{{course.url}}` (when a course id is present)

> Important: use `{{{variable}}}` when you want to insert HTML without it being printed as text on screen.

## Import / Export (backup, migration, replication between environments)

**message_kopereemail** lets you transport (between staging/production, or between different sites) both the **global HTML wrapper** and the **per-provider custom messages**.

### What gets exported

1. **HTML Wrapper (global)**
2. **Custom messages per provider**

  * Table: `{message_kopereemail_template}`
  * Provider key: `component + name`
  * Main fields:

    * `subject` (optional)
    * `bodyhtml` (email HTML template with placeholders)

> Note: the output **enabled/locked** settings (keys `message_provider_*` and `*_locked`) belong to Moodle core and are **not** part of the plugin export. This avoids overwriting operational preferences of the target environment.

## Best practices

* **Start with the wrapper**: have the template ready and standardized.
* **Customize only the providers that matter**: e.g., course completed, critical reminders, access warnings, etc.
* **Keep templates short and objective**: overly long emails are harder to read.
* **Avoid broken HTML**: validate tags, close `div`, etc.
* **Use placeholders carefully**: especially `{{{...}}}` (no escaping).
* Always test after saving.

## Support

For questions, bugs, improvements, or suggestions:

* GitHub Issues:
  [https://github.com/EduardoKrausME/moodle-message_kopereemail/issues](https://github.com/EduardoKrausME/moodle-message_kopereemail/issues)

* Direct contact:
  [https://eduardokraus.com/contato](https://eduardokraus.com/contato)

When opening a ticket, it really helps to include:

* Moodle version
* steps to reproduce
* affected provider (component + name)
* a template example (without sensitive data)
* cron / task logs (if it’s about digest)

## Roadmap (common ideas)

Some improvements that often make sense for this kind of plugin:

* expanded placeholder library. But what do you suggest?
