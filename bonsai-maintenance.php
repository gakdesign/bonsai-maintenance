<?php
/*
Plugin Name: Bonsai Digital Maintenance Mode
Description: Displays a customisable maintenance mode page for non-logged-in users, and can replace the standard WordPress maintenance screen.
Version: 1.12
Author: Ben Ervine / The Bonsai Digital Collective
Text Domain: bonsai-maintenance
Update URI: https://thebonsai.digital/bonsai-maintenance
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
 exit;
}

// 1) Load the library (path must match your folder structure)
require_once __DIR__ . '/includes/plugin-update-checker/plugin-update-checker.php';

// 2) Import the v5 factory (major version alias to the latest installed minor, e.g. v5p6)
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

// 3) Build the update checker
$updateChecker = PucFactory::buildUpdateChecker(
 'https://github.com/gakdesign/bonsai-maintenance', // your repo URL
 __FILE__,                                                              // main plugin file
 'bonsai-maintenance'                                                   // plugin slug = folder name
);

// Default branch (change if not "main")
$updateChecker->setBranch('main');

// If you attach built ZIPs to GitHub Releases, uncomment this:
// $updateChecker->getVcsApi()->enableReleaseAssets();

// If the repo is private, set a token with repo read access:
// $updateChecker->setAuthentication('ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx');


/*
|--------------------------------------------------------------------------
| Bootstrap (i18n)
|--------------------------------------------------------------------------
*/
add_action('plugins_loaded', function () {
 load_plugin_textdomain('bonsai-maintenance', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

/*
|--------------------------------------------------------------------------
| Front-end gate (maintenance mode)
|--------------------------------------------------------------------------
*/
add_action('template_redirect', 'cmm_enable_maintenance_mode', 1);

function cmm_enable_maintenance_mode() {
 if (!get_option('cmm_enabled')) return;
 if (is_admin()) return;
 if (defined('WP_CLI') && WP_CLI) return;
 if (defined('REST_REQUEST') && REST_REQUEST) return;
 if (defined('DOING_AJAX') && DOING_AJAX) return;
 if (defined('DOING_CRON') && DOING_CRON) return;
 if (is_user_logged_in() && current_user_can('manage_options')) return;
 if (is_preview()) return;
 if (is_feed()) return;

 status_header(503);
 header('Content-Type: text/html; charset=utf-8');
 header('Retry-After: 3600');
 echo cmm_render_maintenance_page();
 exit;
}

/*
|--------------------------------------------------------------------------
| Maintenance page template (runtime + static)
|--------------------------------------------------------------------------
*/
function cmm_render_maintenance_page() {
 $site_name     = get_bloginfo('name');
 $logo          = esc_url(get_option('cmm_logo'));
 $bg_image      = esc_url(get_option('cmm_background_image'));
 $badge_text    = get_option('cmm_badge_text', esc_html__('Scheduled maintenance', 'bonsai-maintenance'));
 $header        = get_option('cmm_header_text', __('We’ll be back soon!', 'bonsai-maintenance'));
 $main_raw      = get_option('cmm_main_content');
 $embed_raw     = get_option('cmm_embed_content');
 $facebook      = esc_url(get_option('cmm_facebook'));
 $instagram     = esc_url(get_option('cmm_instagram'));
 $linkedin      = esc_url(get_option('cmm_linkedin'));
 $footer_text   = get_option('cmm_footer_text');
 $show_content  = (int) get_option('cmm_show_main_content', 1); // NEW toggle (default on)
 $seo_title_raw = get_option('cmm_seo_title', '');
 $seo_desc_raw  = get_option('cmm_seo_description', '');

 // KSES for allowed HTML blocks
 $main_safe   = wp_kses_post($main_raw);
 $embed_safe  = wp_kses_post($embed_raw);

 // SEO fallbacks
 $page_title = trim($seo_title_raw) !== '' ? $seo_title_raw : ($site_name . ' – ' . __('Scheduled Maintenance', 'bonsai-maintenance'));
 $meta_desc  = trim($seo_desc_raw) !== '' ? $seo_desc_raw : '';

 ob_start(); ?>
 <!DOCTYPE html>
 <html lang="<?php echo esc_attr(get_locale() ? substr(get_locale(), 0, 2) : 'en'); ?>">
 <head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo esc_html($page_title); ?></title>
  <?php if ($meta_desc !== '') : ?>
   <meta name="description" content="<?php echo esc_attr($meta_desc); ?>">
  <?php endif; ?>
  <style>
   :root { --brand:#ee4367; --ink:#111111; }
   * { box-sizing:border-box; }
   body { text-align:center; padding:50px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", Arial, "Karla", sans-serif; font-size:16px; color:var(--ink); background:#fff; min-height:100vh; margin:0; }
   .cmm-bg { position:fixed; inset:0; background-size:cover; background-position:center; background-repeat:no-repeat; z-index:-1; opacity:1; }
   @media (prefers-color-scheme: dark) {
    body { background:#111; color:#eaeaea; }
    .cmm-bg { opacity:1; }
   }
   h1 { font-size:26px; line-height:1.2; padding-bottom:10px; margin:0 0 10px; }
   article { display:block; text-align:left; max-width:720px; margin:0 auto; background:rgba(255,255,255,.85); border-radius:16px; padding:28px; box-shadow:0 6px 24px rgba(0,0,0,.06); }
   @media (prefers-color-scheme: dark) {
    article { background:rgba(20,20,20,.75); }
   }
   article p { margin:0 0 16px; }
   .logo img { max-width:220px; margin:0 0 20px; height:auto; }
   .socials { margin-top:16px; }
   .socials a { text-decoration:none; color:inherit; }
   .socials a:hover { text-decoration:underline; }
   footer { margin-top:40px; font-size:13px; opacity:.8; }
   .badge { display:inline-block; padding:4px 8px; background:var(--brand); color:#fff; border-radius:999px; font-size:12px; margin-bottom:16px; }
   .cmm-empty { display:flex; align-items:center; justify-content:center; min-height:100vh; }
  </style>
 </head>
 <body>
  <?php if (!empty($bg_image)) : ?>
   <div class="cmm-bg" style="background-image:url('<?php echo $bg_image; ?>');"></div>
  <?php endif; ?>

  <?php if ($show_content) : ?>
   <article>
    <div class="logo">
     <?php if (!empty($logo)) : ?>
      <img src="<?php echo $logo; ?>" alt="<?php echo esc_attr__('Logo', 'bonsai-maintenance'); ?>">
     <?php endif; ?>
    </div>

    <?php if (!empty($badge_text)) : ?>
     <span class="badge"><?php echo esc_html($badge_text); ?></span>
    <?php endif; ?>

    <h1><?php echo esc_html($header); ?></h1>

    <?php if (!empty($main_safe)) : ?>
     <div class="cmm-main"><?php echo $main_safe; ?></div>
    <?php endif; ?>

    <?php if (!empty($embed_safe)) : ?>
     <div class="cmm-embed-content"><?php echo $embed_safe; ?></div>
    <?php endif; ?>

    <div class="socials">
     <?php
     $parts = [];
     if (!empty($linkedin))  $parts[] = '<a href="' . $linkedin . '" target="_blank" rel="noopener">' . esc_html__('LinkedIn', 'bonsai-maintenance') . '</a>';
     if (!empty($facebook))  $parts[] = '<a href="' . $facebook . '" target="_blank" rel="noopener">' . esc_html__('Facebook', 'bonsai-maintenance') . '</a>';
     if (!empty($instagram)) $parts[] = '<a href="' . $instagram . '" target="_blank" rel="noopener">' . esc_html__('Instagram', 'bonsai-maintenance') . '</a>';
     echo implode(' &nbsp;•&nbsp; ', $parts);
     ?>
    </div>

    <footer>
     © <?php echo esc_html(date_i18n('Y')); ?>
     <?php if (!empty($footer_text)) echo ' ' . esc_html($footer_text); ?>
    </footer>
   </article>
  <?php else : ?>
   <div class="cmm-empty" aria-hidden="true"></div>
  <?php endif; ?>
 </body>
 </html>
 <?php
 return ob_get_clean();
}

/*
|--------------------------------------------------------------------------
| Settings screen (Options → Maintenance Mode)
|--------------------------------------------------------------------------
*/
add_action('admin_menu', function () {
 add_options_page(
  __('Maintenance Mode', 'bonsai-maintenance'),
  __('Maintenance Mode', 'bonsai-maintenance'),
  'manage_options',
  'cmm-settings',
  'cmm_settings_page'
 );
});

function cmm_settings_page() { ?>
 <div class="wrap">
  <h1><?php echo esc_html__('Maintenance Mode Settings', 'bonsai-maintenance'); ?></h1>
  <form method="post" action="options.php">
   <?php
   settings_fields('cmm_settings');
   do_settings_sections('cmm-settings');
   submit_button();
   ?>
  </form>
 </div>
<?php }

add_action('admin_init', function () {
 // Register + sanitise
 register_setting('cmm_settings', 'cmm_enabled', ['type' => 'boolean', 'sanitize_callback' => 'cmm_sanitize_checkbox', 'default' => 0]);
 register_setting('cmm_settings', 'cmm_override_wp_maintenance', ['type' => 'boolean', 'sanitize_callback' => 'cmm_sanitize_checkbox', 'default' => 0]);

 register_setting('cmm_settings', 'cmm_logo', ['type' => 'string', 'sanitize_callback' => 'esc_url_raw', 'default' => '']);
 register_setting('cmm_settings', 'cmm_background_image', ['type' => 'string', 'sanitize_callback' => 'esc_url_raw', 'default' => '']);
 register_setting('cmm_settings', 'cmm_show_main_content', ['type' => 'boolean', 'sanitize_callback' => 'cmm_sanitize_checkbox', 'default' => 1]); // NEW
 register_setting('cmm_settings', 'cmm_badge_text', ['type' => 'string', 'sanitize_callback' => 'cmm_sanitize_line', 'default' => '']);
 register_setting('cmm_settings', 'cmm_header_text', ['type' => 'string', 'sanitize_callback' => 'cmm_sanitize_line', 'default' => '']);
 register_setting('cmm_settings', 'cmm_main_content', ['type' => 'string', 'sanitize_callback' => 'wp_kses_post', 'default' => '']);
 register_setting('cmm_settings', 'cmm_embed_content', ['type' => 'string', 'sanitize_callback' => 'wp_kses_post', 'default' => '']);
 register_setting('cmm_settings', 'cmm_facebook', ['type' => 'string', 'sanitize_callback' => 'esc_url_raw', 'default' => '']);
 register_setting('cmm_settings', 'cmm_instagram', ['type' => 'string', 'sanitize_callback' => 'esc_url_raw', 'default' => '']);
 register_setting('cmm_settings', 'cmm_linkedin', ['type' => 'string', 'sanitize_callback' => 'esc_url_raw', 'default' => '']);
 register_setting('cmm_settings', 'cmm_footer_text', ['type' => 'string', 'sanitize_callback' => 'cmm_sanitize_line', 'default' => '']);

 // SEO settings (NEW)
 register_setting('cmm_settings', 'cmm_seo_title', ['type' => 'string', 'sanitize_callback' => 'cmm_sanitize_line', 'default' => '']);
 register_setting('cmm_settings', 'cmm_seo_description', ['type' => 'string', 'sanitize_callback' => 'cmm_sanitize_meta_desc', 'default' => '']);

 // Section
 add_settings_section('cmm_main', __('Main Settings', 'bonsai-maintenance'), '__return_false', 'cmm-settings');

 // Fields
 add_settings_field('cmm_enabled', __('Enable Maintenance Mode', 'bonsai-maintenance'), function () {
  printf(
   '<label><input type="checkbox" name="cmm_enabled" value="1" %s> %s</label>',
   checked(1, (int) get_option('cmm_enabled'), false),
   esc_html__('Enabled', 'bonsai-maintenance')
  );
 }, 'cmm-settings', 'cmm_main');

 add_settings_field('cmm_override_wp_maintenance', __('Override WordPress Maintenance Page', 'bonsai-maintenance'), function () {
  printf(
   '<label><input type="checkbox" name="cmm_override_wp_maintenance" value="1" %s> %s</label>',
   checked(1, (int) get_option('cmm_override_wp_maintenance'), false),
   esc_html__('Override update screen', 'bonsai-maintenance')
  );
 }, 'cmm-settings', 'cmm_main');

 add_settings_field('cmm_logo', __('Header Logo (URL)', 'bonsai-maintenance'), function () {
  printf('<input type="url" name="cmm_logo" value="%s" class="regular-text" />', esc_attr(get_option('cmm_logo')));
 }, 'cmm-settings', 'cmm_main');

 add_settings_field('cmm_background_image', __('Background Image (URL)', 'bonsai-maintenance'), function () {
  printf('<input type="url" name="cmm_background_image" value="%s" class="regular-text" placeholder="https://example.com/background.jpg" />', esc_attr(get_option('cmm_background_image')));
 }, 'cmm-settings', 'cmm_main');

 add_settings_field('cmm_show_main_content', __('Show Main Content', 'bonsai-maintenance'), function () {
  printf(
   '<label><input type="checkbox" name="cmm_show_main_content" value="1" %s> %s</label>',
   checked(1, (int) get_option('cmm_show_main_content', 1), false),
   esc_html__('Display logo, badge, header, content, embeds, socials and footer', 'bonsai-maintenance')
  );
 }, 'cmm-settings', 'cmm_main');

 add_settings_field('cmm_badge_text', __('Badge Text', 'bonsai-maintenance'), function () {
  $val = get_option('cmm_badge_text');
  if ($val === '') $val = esc_html__('Scheduled maintenance', 'bonsai-maintenance');
  printf('<input type="text" name="cmm_badge_text" value="%s" class="regular-text" />', esc_attr($val));
 }, 'cmm-settings', 'cmm_main');

 add_settings_field('cmm_header_text', __('Header Text', 'bonsai-maintenance'), function () {
  printf('<input type="text" name="cmm_header_text" value="%s" class="regular-text" />', esc_attr(get_option('cmm_header_text')));
 }, 'cmm-settings', 'cmm_main');

 // Main content as WYSIWYG
 add_settings_field('cmm_main_content', __('Main Content (WYSIWYG)', 'bonsai-maintenance'), function () {
  wp_editor(get_option('cmm_main_content'), 'cmm_main_content', [
   'textarea_name' => 'cmm_main_content',
   'textarea_rows' => 10,
   'media_buttons' => false,
   'tinymce' => [
    'toolbar1' => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink,blockquote',
    'toolbar2' => '',
   ],
  ]);
 }, 'cmm-settings', 'cmm_main');

 add_settings_field('cmm_embed_content', __('Embed Code / HTML', 'bonsai-maintenance'), function () {
  wp_editor(get_option('cmm_embed_content'), 'cmm_embed_content', [
   'textarea_name' => 'cmm_embed_content',
   'textarea_rows' => 5,
   'media_buttons' => false,
  ]);
 }, 'cmm-settings', 'cmm_main');

 add_settings_field('cmm_facebook', __('Facebook URL', 'bonsai-maintenance'), function () {
  printf('<input type="url" name="cmm_facebook" value="%s" class="regular-text" />', esc_attr(get_option('cmm_facebook')));
 }, 'cmm-settings', 'cmm_main');

 add_settings_field('cmm_instagram', __('Instagram URL', 'bonsai-maintenance'), function () {
  printf('<input type="url" name="cmm_instagram" value="%s" class="regular-text" />', esc_attr(get_option('cmm_instagram')));
 }, 'cmm-settings', 'cmm_main');

 add_settings_field('cmm_linkedin', __('LinkedIn URL', 'bonsai-maintenance'), function () {
  printf('<input type="url" name="cmm_linkedin" value="%s" class="regular-text" />', esc_attr(get_option('cmm_linkedin')));
 }, 'cmm-settings', 'cmm_main');

 add_settings_field('cmm_footer_text', __('Footer Text', 'bonsai-maintenance'), function () {
  printf('<input type="text" name="cmm_footer_text" value="%s" class="regular-text" />', esc_attr(get_option('cmm_footer_text')));
 }, 'cmm-settings', 'cmm_main');

 // SEO fields (NEW)
 add_settings_field('cmm_seo_title', __('Page Title (SEO)', 'bonsai-maintenance'), function () {
  printf('<input type="text" name="cmm_seo_title" value="%s" class="regular-text" placeholder="%s" />',
   esc_attr(get_option('cmm_seo_title')),
   esc_attr(get_bloginfo('name') . ' – ' . __('Scheduled Maintenance', 'bonsai-maintenance'))
  );
 }, 'cmm-settings', 'cmm_main');

 add_settings_field('cmm_seo_description', __('Meta Description (SEO)', 'bonsai-maintenance'), function () {
  printf('<textarea name="cmm_seo_description" class="large-text" rows="3" maxlength="320" placeholder="%s">%s</textarea>',
   esc_attr(__('Optional short summary of the page', 'bonsai-maintenance')),
   esc_textarea(get_option('cmm_seo_description'))
  );
 }, 'cmm-settings', 'cmm_main');
});

/*
|--------------------------------------------------------------------------
| WP core maintenance override (wp-content/maintenance.php)
|--------------------------------------------------------------------------
*/
register_activation_hook(__FILE__, 'cmm_on_activation');
register_deactivation_hook(__FILE__, 'cmm_on_deactivation');

function cmm_on_activation() {
 cmm_generate_static_maintenance_page();
 if (get_option('cmm_override_wp_maintenance')) {
  cmm_copy_maintenance_file();
 }
}

function cmm_on_deactivation() {
 cmm_cleanup_wp_maintenance();
}

/**
 * Create/refresh static HTML template in wp-content.
 */
function cmm_generate_static_maintenance_page() {
 $file_path = trailingslashit(WP_CONTENT_DIR) . 'maintenance-template.html';
 $html      = cmm_render_maintenance_page();
 if (wp_mkdir_p(WP_CONTENT_DIR) && is_writable(WP_CONTENT_DIR)) {
  @file_put_contents($file_path, $html);
 }
}

/**
 * De-bounced regeneration after settings save and on shutdown.
 */
add_action('shutdown', 'cmm_generate_static_maintenance_page_once');
function cmm_generate_static_maintenance_page_once() {
 static $done = false;
 if ($done) return;
 $done = true;
 cmm_generate_static_maintenance_page();
}

/**
 * Copy custom-maintenance.php → wp-content/maintenance.php if allowed.
 */
function cmm_copy_maintenance_file() {
 $src  = plugin_dir_path(__FILE__) . 'custom-maintenance.php';
 $dest = trailingslashit(WP_CONTENT_DIR) . 'maintenance.php';
 if (!file_exists($src)) return;
 if (!is_writable(WP_CONTENT_DIR)) return;
 @copy($src, $dest);
}

/**
 * Remove wp-content/maintenance.php if present and allowed.
 */
function cmm_cleanup_wp_maintenance() {
 $dest = trailingslashit(WP_CONTENT_DIR) . 'maintenance.php';
 if (file_exists($dest) && is_writable(WP_CONTENT_DIR)) {
  @unlink($dest);
 }
}

/**
 * Auto-handle maintenance override file when the option toggles.
 */
add_action('update_option_cmm_override_wp_maintenance', function ($old, $new) {
 if ((int) $new === 1) {
  cmm_copy_maintenance_file();
 } else {
  cmm_cleanup_wp_maintenance();
 }
}, 10, 2);

/*
|--------------------------------------------------------------------------
| Sanitisation helpers
|--------------------------------------------------------------------------
*/
function cmm_sanitize_checkbox($value) {
 return (int) (bool) $value;
}

function cmm_sanitize_line($value) {
 $value = is_string($value) ? $value : '';
 $value = wp_strip_all_tags($value, true);
 $value = preg_replace('/\s+/', ' ', $value);
 return trim($value);
}

function cmm_sanitize_meta_desc($value) {
 $value = is_string($value) ? $value : '';
 $value = wp_strip_all_tags($value, true);
 $value = preg_replace('/\s+/', ' ', $value);
 $value = trim($value);
 // Keep within typical SERP range without being too strict
 if (strlen($value) > 320) {
  $value = substr($value, 0, 320);
 }
 return $value;
}
