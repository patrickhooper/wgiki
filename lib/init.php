<?php
/**
 * WGIki functions and definitions
 *
 * @package WGIki
 */

if ( ! function_exists( 'wgiki_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function wgiki_setup() {
  /*
   * Make theme available for translation.
   * Translations can be filed in the /languages/ directory.
   * If you're building a theme based on WGIki, use a find and replace
   * to change 'wgiki' to the name of your theme in all the template files
   */
  load_theme_textdomain( 'wgiki', get_template_directory() . '/languages' );

  // Add default posts and comments RSS feed links to head.
  add_theme_support( 'automatic-feed-links' );

  /*
   * Let WordPress manage the document title.
   * By adding theme support, we declare that this theme does not use a
   * hard-coded <title> tag in the document head, and expect WordPress to
   * provide it for us.
   */
  add_theme_support( 'title-tag' );

  /*
   * Enable support for Post Thumbnails on posts and pages.
   *
   * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
   */
  add_theme_support( 'post-thumbnails' );

  // This theme uses wp_nav_menu() in one location.
  register_nav_menus( array(
    'primary' => esc_html__( 'Primary Menu', 'wgiki' ),
  ) );

  /*
   * Switch default core markup for search form, comment form, and comments
   * to output valid HTML5.
   */
  add_theme_support( 'html5', array(
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
  ) );

  /*
   * Enable support for Post Formats.
   * See http://codex.wordpress.org/Post_Formats
   */
  add_theme_support( 'post-formats', array(
    'aside',
    'image',
    'video',
    'quote',
    'link',
  ) );

  // Set up the WordPress core custom background feature.
  add_theme_support( 'custom-background', apply_filters( 'wgiki_custom_background_args', array(
    'default-color' => 'ffffff',
    'default-image' => '',
  ) ) );
}
endif; // wgiki_setup
add_action( 'after_setup_theme', 'wgiki_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function wgiki_content_width() {
  $GLOBALS['content_width'] = apply_filters( 'wgiki_content_width', 640 );
}
add_action( 'after_setup_theme', 'wgiki_content_width', 0 );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function wgiki_widgets_init() {
  register_sidebar( array(
    'name'          => esc_html__( 'Sidebar', 'wgiki' ),
    'id'            => 'sidebar-1',
    'description'   => '',
    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    'after_widget'  => '</aside>',
    'before_title'  => '<h1 class="widget-title">',
    'after_title'   => '</h1>',
  ) );
}
add_action( 'widgets_init', 'wgiki_widgets_init' );

/* Allow uploading of .svg and .eps files */
add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes( $existing_mimes=array() ) {
  $existing_mimes['svg'] = 'mime/type';
  $existing_mimes['eps'] = 'mime/type';
  return $existing_mimes;
}

/* Hide admin bar on front end */
add_filter('show_admin_bar', '__return_false');

/**
 * Hide backend from all but administrators, editors, and content managers
 */
add_action('init', 'hide_dashboard');
function hide_dashboard() {
  if (is_admin() && !current_user_can('administrator') && !current_user_can('editor') && !current_user_can('manager_accounting_admin') && !current_user_can('manager_civil') && !current_user_can('manager_creative') && !current_user_can('manager_environmental') && !current_user_can('manager_hr') && !current_user_can('manager_it') && !current_user_can('manager_landscape_arch') && !current_user_can('manager_planning') && !current_user_can('manager_roadway') && !current_user_can('manager_structures') && !current_user_can('manager_sue') && !current_user_can('manager_survey') && !current_user_can('manager_trans_planning') && !current_user_can('manager_utilities') && !(defined( 'DOING_AJAX' ) && DOING_AJAX)) {
    wp_redirect(site_url());
    exit;
  }
}


/*
 * Custom rewrite rules for protecting WGI files
 *
 * 1. Add comment to block off this section of rules so it's clearly theme-related
 * 2. Turn on RewriteEngine if module exists
 * 3. For all uploads with provided extensions, redirect to resources page
 * 4. Pass query parameter with file name
 */
add_filter('mod_rewrite_rules', 'wgiki_htaccess_rules');
function wgiki_htaccess_rules($rules) {
  $wgiki_rules = <<<EOD
\n# BEGIN WGIki
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^wp-content/uploads/(.+)$ http://wgiki.com/protected/?file=$1 [QSA,L]
</IfModule>
# END WGIki\n\n
EOD;
  return $wgiki_rules . $rules;
}
