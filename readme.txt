=== Bonsai Digital Maintenance Mode ===
Contributors: bonsai-digital-collective
Donate link: https://bonsaidigital.co.uk
Tags: maintenance, coming soon, offline, 503, custom page
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.11
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight WordPress plugin that displays a customisable maintenance page to visitors while allowing authorised users to keep working. Optionally replaces WordPress’ core maintenance page.

== Description ==

Bonsai Digital Maintenance Mode shows a friendly, customisable maintenance page for non-logged-in visitors. Logged-in administrators can continue working normally.  

* Sends proper **503 Service Unavailable** headers with `Retry-After`.
* Responsive, branded template with **background image**.
* **Editable badge text** and **header text**.
* **WYSIWYG editor** for main content (paragraphs, lists, headings).
* **Toggle main content on/off** (background-only mode).
* **Basic SEO**: custom page title + meta description (still `noindex, nofollow`).
* Optional override of WordPress’ core `wp-content/maintenance.php`.
* Writes static snapshot to `wp-content/maintenance-template.html`.
* Translation-ready, sanitised, secure.

== Installation ==

1. Upload the plugin folder `bonsai-maintenance` to the `/wp-content/plugins/` directory, or install via the Plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to **Settings → Maintenance Mode** to configure.

== Quick Start ==

1. Go to **Settings → Maintenance Mode**.
2. Tick **Enable Maintenance Mode**.
3. (Optional) Add a **Background Image (URL)**.
4. Fill **Badge Text**, **Header Text**, and **Main Content (WYSIWYG)**.
5. Use **Show Main Content** to toggle full content vs background-only mode.
6. (Optional) Set **Page Title (SEO)** and **Meta Description (SEO)**.
7. (Optional) Tick **Override WordPress Maintenance Page** to copy `custom-maintenance.php` to `wp-content/maintenance.php`.

> While enabled, non-logged-in visitors see the maintenance page. Logged-in users with `manage_options` continue as normal.

== Settings Reference ==

* **Enable Maintenance Mode** (`cmm_enabled`) — Master switch  
* **Override WP Maintenance Page** (`cmm_override_wp_maintenance`) — Replace `wp-content/maintenance.php`  
* **Header Logo (URL)** (`cmm_logo`) — Optional logo  
* **Background Image (URL)** (`cmm_background_image`) — Full-page background  
* **Show Main Content** (`cmm_show_main_content`) — If off, only background shows  
* **Badge Text** (`cmm_badge_text`) — Optional pill-style text  
* **Header Text** (`cmm_header_text`) — Main heading  
* **Main Content (WYSIWYG)** (`cmm_main_content`) — Rich text  
* **Embed Code / HTML** (`cmm_embed_content`) — For embeds/extra HTML  
* **Social URLs** (`cmm_facebook`, `cmm_instagram`, `cmm_linkedin`)  
* **Footer Text** (`cmm_footer_text`) — Appears after © YEAR  
* **Page Title (SEO)** (`cmm_seo_title`) — Custom `<title>`  
* **Meta Description (SEO)** (`cmm_seo_description`) — `<meta name="description">` (~320 chars)  

== SEO Considerations ==

* Returns **503 Service Unavailable** with `noindex, nofollow` for crawler-friendly short outages.
* Keep maintenance periods brief; extended 5xx responses may reduce crawl frequency.

== Files Written ==

* `wp-content/maintenance-template.html` — Static snapshot of the current maintenance page.  
* `wp-content/maintenance.php` — Created only if "Override WordPress Maintenance Page" is enabled.  

> Ensure the web server can write to `wp-content/`.

== Compatibility ==

* Works with most themes and caching plugins (503 responses should not be cached).
* Compatible with REST API, AJAX, and cron.
* If you see “headers already sent” warnings, check for early output from other plugins/themes.

== Security ==

* Options sanitised on save (`esc_url_raw`, `wp_kses_post`, line sanitisation).
* Front-end output safely escaped.
* No injected JavaScript; minimal inline CSS only.

== Troubleshooting ==

* **Still seeing the normal site?** You may be logged in with admin rights; test in a private/incognito window.  
* **Blank page?** Check PHP error logs and ensure `wp-content` is writable.  
* **Headers already sent?** Another plugin/theme may print too early; this plugin runs on `template_redirect`.  

== Changelog ==

= 1.12 =
* Adding Wordpress plugin update functionality

= 1.11 =
* Add toggle to show/hide main content (background-only mode).
* Add SEO options: page title + meta description.

= 1.10 =
* Editable badge text.
* WYSIWYG editor for main content.

= 1.9 =
* Background image option.

= 1.8 =
* Safer hook order.
* Improved sanitisation.
* Static template generation.
* Optional override of WP core maintenance page.

== Contributing ==

* Fork and create a feature branch.  
* Keep PRs small and focused.  
* Use UK spelling and WordPress PHPDoc style.  
* Test against PHP 7.4 and latest 8.x, plus last two WordPress releases.  

== License ==

This plugin is licensed under the GPL v2 or later.  
https://www.gnu.org/licenses/gpl-2.0.html

== Author ==

**The Bonsai Digital Collective**  
Maintainer: Ben Ervine  
https://bonsaidigital.co.uk  

For issues or enhancements, please open a GitHub issue with:  
* Steps to reproduce  
* Environment details (PHP/WP versions)  
* Any relevant logs  