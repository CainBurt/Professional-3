<?php

/**
 * Registers any plugin dependancies the theme has.
 *
 * Requires TGMPA
 */
function register_plugins () {
	$plugins = array(
		/* Register any required plugins:
		array(
			'name'               => 'Example Plugin', // Required. The plugin name.
			'slug'               => 'example-plugin', // Requried. The plugin slug (typically the folder name).
			'source'             => 'http://example-plugin.com', // The plugin source. Often a .zip file. Do not include this if the plugin is from the Wordpress Repository.
			'required'           => true, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '', // E.g. 1.0.0. If set, the active plugin must be this version or higher. If the plugin version is higher than the plugin version installed, the user will be notified to update the plugin.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
			'is_callable'        => '', // If set, this callable will be be checked for availability to determine if a plugin is active.
        ),*/
        array(
            'name' => 'Timber',
            'slug' => 'timber-library',
            'required' => true,
            'force_activation' => true
        ),
		array(
			'name' => 'Advanced Custom Fields Pro',
            'slug' => 'advanced-custom-fields-pro',
            'source' => get_template_directory_uri() . '/includes/plugins/advanced-custom-fields-pro.zip',
			'required' => true,
            'force_activation' => true
        ),
        array(
            'name' => 'Advanced Custom Fields: Font Awesome Field',
            'slug' => 'advanced-custom-fields-font-awesome',
            'required' => true,
            'force_activation' => true
        ),
        array(
            'name' => 'Yoast SEO',
            'slug' => 'wordpress-seo',
            'required' => true,
            'force_activation' => true
        ),
        array(
            'name' => 'Safe SVG',
            'slug' => 'safe-svg',
            'required' => true,
            'force_activation' => true
        ),
        array(
            'name' => 'WPS Hide Login',
            'slug' => 'wps-hide-login',
            'required' => false
        ),
	);
	register_required_plugins ($plugins);
}

// Plugin Dependancies
require_once('includes/required-plugins/class-tgm-plugin-activation.php');
require_once('includes/required-plugins/register-plugin.php');

if ( is_admin() && function_exists('register_required_plugins')) {
    add_action ('tgmpa_register', 'register_plugins');
}

require_once 'includes/cache_bust.php';
function get_cache_ver() {
    include 'includes/cache_bust.php';
    return $cache_ver;
}

require_once('includes/edit-strings/edit-strings.php');

if ( ! class_exists( 'Timber' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
    } );
    return;
}

Timber::$dirname = array('templates', 'components');

/**
 * StarterSite
 * 
 * The start point for Timber. It's a good idea to run any initial hooks and filters here that you need globally across the site.
 */
class StarterSite extends TimberSite {

    function __construct() {
        add_theme_support( 'post-formats' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'menus' );

        // Timber filters
        add_filter( 'timber_context', array( $this, 'add_to_context' ) );
        add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
        add_filter( 'upload_mimes', array($this, 'svg_mime_types' ));

        // Comment out to Enable oEmbed (responsible for embedding twitter etc)
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
        remove_action('wp_head', 'wp_oembed_add_host_js');
        remove_action('rest_api_init', 'wp_oembed_register_route');
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

        // Header Removal
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_generator'); // Hide WP Version for security
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'rest_output_link_wp_head', 10); //Remove wp-json/ link
        add_action( 'wp_enqueue_scripts', 'bs_dequeue_dashicons' );
            function bs_dequeue_dashicons() {
                if ( ! is_user_logged_in() ) {
                    wp_deregister_style( 'dashicons' );
                }
            }



        add_filter( 'emoji_svg_url', '__return_false' );
        
        add_action('after_setup_theme', function () {
            add_theme_support( 'html5', ['script', 'style'] );
        });

        // Timber Actions
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        add_action( 'init', array( $this, 'register_acf_blocks' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );

        // First party actions
        add_action('inline_file', array($this, 'inline_file'));
        add_action('admin_head', array($this, 'fix_svg_thumb_display'));
        add_action( 'init', 'disable_wp_emojicons' );

        // Add Advanced Custom Fields options page
        if( function_exists('acf_add_options_page') ) {
            acf_add_options_sub_page('Theme');
            acf_add_options_sub_page('Analytics/Tracking');

            if (current_user_can('administrator') || get_field('show_debug_menu', 'option')) {
              acf_add_options_sub_page('Debug Options');
            }
        }

        require_once('includes/toggle_acf_edit.php');

        if (!$showacf) {
            require_once('includes/acf-edit-screen-disabler.php');
            if (function_exists('get_field')) {
                if (!get_field('enable_acf_edit', 'option')) {
                    add_filter('acf/settings/show_admin', '__return_false'); //DO NOT COMMENT OUT OR DISABLE USE THEME OPTIONS TICK BOX INSTEAD
                }
            }
        }


        parent::__construct();
    }

    function register_post_types() {
        // require_once custom post types here
        // require_once('includes/post-types/form.php');
        require_once('includes/post-types/suggestion.php');
    }

    function register_taxonomies() {
        // require_once custom taxonomies here
    }

    function register_acf_blocks() {
        if ( ! function_exists( 'acf_register_block' ) ) {
            return;
        }
        // require_once custom acf blocks here
        
        // require_once('includes/blocks/example.php');
        require_once('includes/blocks/resource.php');
        require_once('includes/blocks/landing-page/nav-grid.php');
        require_once('includes/blocks/landing-page/latest-updates.php');
        require_once('includes/blocks/team.php');
        require_once('includes/blocks/hero.php');
        require_once('includes/blocks/start.php');
    }

    function add_to_context( $context ) {
        $context['menu'] = new TimberMenu('Global Header Navigation');
        $context['site'] = $this;
        if (function_exists('get_fields')) {
            $context['options'] = get_fields('option');
        }
        $context['page_stats'] = TimberHelper::start_timer();
        $context['share_links'] = get_social_share_links();
        $context['allow_ga'] = isset($_COOKIE["allow_ga"]) && $_COOKIE['allow_ga'];
        $context['block_ga'] = isset($_COOKIE["block_ga"]) && $_COOKIE['block_ga'];
        return $context;
    }

    function add_to_twig( $twig ) {
        // Add your own twig functions
        $twig->addFunction( new Twig_SimpleFunction('query_cat', array($this, 'query_cat')));
        $twig->addFilter(new Twig_SimpleFilter('json', array($this, 'json')));
        $twig->addFunction( new Twig_SimpleFunction( 'load_ga', array($this, 'load_ga')));
        $twig->addFunction( new Twig_SimpleFunction( 'load_gtm', array($this, 'load_gtm')));
        return $twig;
    }

    function assets( $twig ) {
        // Get rid of default media element
        // wp_deregister_script('wp-mediaelement'); // Uncomment to disable Media Element
        // wp_deregister_style('wp-mediaelement'); // Uncomment to disable Media Element

        // Remove Wp's jQuery
        // wp_deregister_script('jquery'); // Uncomment to disable jQuery

        // Define globals with for cache busting
        require_once 'enqueues.php';
        require('includes/cache_bust.php');

        wp_enqueue_script( 'essential.js', BUNDLE_JS_SRC, array(), $cache_ver, false); // These will appear at the top of the page
        wp_enqueue_script( 'deferred.js', DEFERRED_BUNDLE_JS_SRC, array(), $cache_ver, true); // These will appear in the footer
        // Enqueue a main stylesheet as a sensible default
        // wp_enqueue_style( 'main.css', MAIN_CSS_SRC, array(), $cache_ver, 'all' );
        inline_script(get_template_directory_uri() . '/dist/js/partytown.js');
        inline_script(get_template_directory_uri() . '/dist/js/header.js');
        inline_script(get_template_directory_uri() . '/dist/js/nav-grid.js');
        inline_script(get_template_directory_uri() . '/dist/js/team.js');
        inline_script(get_template_directory_uri() . '/dist/js/new-starter.js');
        inline_style(get_template_directory_uri(  ) . '/dist/styles/main.css');
        inline_style(get_template_directory_uri(  ) . '/dist/styles/deferred.css');
        inline_style(get_template_directory_uri(  ) . '/dist/styles/header.css');
        inline_style(get_template_directory_uri(  ) . '/dist/styles/footer.css');
        inline_style(get_template_directory_uri(  ) . '/dist/styles/nav-grid.css');
        inline_style(get_template_directory_uri(  ) . '/dist/styles/latest-updates.css');
        inline_style(get_template_directory_uri(  ) . '/dist/styles/resource.css');
        inline_style(get_template_directory_uri(  ) . '/dist/styles/team.css');
        inline_style(get_template_directory_uri(  ) . '/dist/styles/hero.css');
        inline_style(get_template_directory_uri(  ) . '/dist/styles/start.css');
        inline_style(get_template_directory_uri(  ) . '/dist/styles/start-form.css');
        inline_style(get_template_directory_uri(  ) . '/dist/styles/suggestions-form.css');
    }

    /**
     * Inline File
     *
     * This action will echo the contents of a file when passed a relative path, ath
     * the point the function was called.
     *
     * The intended use of this function is for inlining files within templates, for
     * example: embedding an SVG.
     */
    function inline_file($path) {
        if ( $path ) {
            echo file_get_contents($_SERVER['DOCUMENT_ROOT'] . parse_url($path)['path']);
        }
    }

    /**
     * Allows SVGs to be uploaded in the wordpress media library
     */
    function svg_mime_types( $mimes ) {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    /**
     * Limits sizes of SVGs in WordPress backend
     */
    function fix_svg_thumb_display() {
        echo '<style> td.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail { width: 100% !important; height: auto !important; } </style>';
    }

    /**
     * Query Cat
     * Queries passed category id's and limits results to passed limit
     *
     * This is registered as a Timber function and can be called in templates
     * with the following syntax:
     *
     *      {{ query_cat([1, 2, 3], 3) }}
     *
     * This would return posts in categories 1, 2, or 3 and limit the response
     * to 3 results.
     */
    function query_cat(
        $cats = [],
        $limit = 3,
        $post_type = 'any',
        $orderby = 'date',
        $offset = 0,
        $exclude = []
    ) {
        return Timber::get_posts(array(
            'post_type' => $post_type,
            'cat' => $cats,
            'posts_per_page' => $limit,
            'orderby' => $orderby,
            'offset' => $offset,
            'post__not_in' => $exclude
        ));
    }

    /**
     * JSON - Twig Filter
     *
     * Returns object as JSON string
     *
     * Features:
     * - Strips newline characters from String
     * - Escapes and quotes properly, preventing double-encoding of JSON data.
     *
     * Usage:
     *
     *     <script>
     *         var jsonData = '{{ twigObject|json }}';
     *     </script>
     */
    function json($o) {
        return str_replace(array('\r', '\n'), '', str_replace("\u0022","\\\\\"", json_encode($o, JSON_NUMERIC_CHECK | JSON_HEX_QUOT)));
    }

    function load_ga() {
        require('includes/cache_bust.php');
        wp_enqueue_script( 'google-analytics.js', get_template_directory_uri() . '/dist/js/google-analytics.js', array(), $cache_ver, false);
    }

    function load_gtm() {
        require('includes/cache_bust.php');
        wp_enqueue_script( 'google-tag-manager.js', get_template_directory_uri() . '/dist/js/google-tag-manager.js', array(), $cache_ver, false);
    }

}

new StarterSite();

/*******************************************************************************
 * Global Functions
 ******************************************************************************/

/**
 * Console Log
 *
 * Takes array of strings and returns a javascript console.log.
 */
function console_log($args, $delimiter = ' ') {
    $s = '<script>console.log("';
    $s .= join($delimiter, $args);
    $s .= '")</script>';

    return $s;
}

/**
 * Stop Timber Timer
 *
 * A timer is started at the beginning of every page load that times how long it
 * takes to generate a page. This function stops the timer and reports the
 * following stats using the console_log function:
 *
 * - How long the page took to generate
 * - How many database queries did it take
 */
function stop_timber_timer() {
    $context = Timber::get_context();

    return console_log([
        'Page generated in ' . TimberHelper::stop_timer($context['page_stats']),
        get_num_queries() . ' database queries'
    ]);
}

function custom_login_style() {
  echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/login/custom-login-styles.css" />';
}

add_action('login_head', 'custom_login_style');


/**
 * Disables Emjois in TinyMCE
 *
 * Is a filter.
 */
function disable_emojicons_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    } else {
        return array();
    }
}

/**
 * Dequeues all scripts and plugins relating to Wordpress emoji defaults
 */
function disable_wp_emojicons() {
    // all actions related to emojis
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

    // filter to remove TinyMCE emojis
    add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}

/**
 * Ajax Forms
 *
 * A function to handle the ajax submission of the various flex forms around the
 * site
 */
add_action( 'wp_ajax_flex_form', 'flex_form' );
add_action( 'wp_ajax_nopriv_flex_form', 'flex_form' );
function flex_form() {
    $currentForm = new Timber\Post($_POST['form_id']);
    $result = array();

    // Parse & Sanitize Fields
    $fields = json_decode(stripslashes($_POST['fields']), true);
    foreach ($fields as $key => $value) {
        $fields[$key] = sanitize_text_field($value);
    }

    // Email Setup
    $to_address = sanitize_email($currentForm->destination_email_address);
    $subject = $currentForm->title . ' submission';
    $message = $subject . "\r\n\r\n";
    foreach ($fields as $key => $value) {
        if ($key == 'FileID') {
            $result['download'] = wp_get_attachment_url(intval($value));
        } else {
            $message.= $key . ": " . $value . "\r\n";
        }
    }

    // Send
    if (wp_mail($to_address, $subject, $message)) {
        $result['message'] = $currentForm->get_field('thank_you_message');
    } else {
        $result['message'] = $currentForm->get_field('error_message');
    }

    echo json_encode($result);

    wp_die(); // Terminate response
}

/*
*   Remove the Back-End code editor
*/
function remove_editor_menu() {
    remove_action('admin_menu', '_add_themes_utility_last', 101);
    if (!function_exists('get_field')) {
        return;
    }
    if (!get_field('enable_comments_menu', 'option')) {
      remove_menu_page( 'edit-comments.php' );
    }
}
add_action('_admin_menu', 'remove_editor_menu', 1);

/*
*   Remove Gutenburg CSS
*/
function remove_block_css(){
    global $wp_styles;
    $block_style = $wp_styles->registered['wp-block-library']->src; // get block styles src url
    $block_library_style = $wp_styles->registered['wp-block-library-theme']->src; // get block styles src url
    // $woo_block_style = $wp_styles->registered['wc-blocks-style']->src; // get block styles src url

    wp_dequeue_style( 'wp-block-library' ); // dequeue default style
    wp_dequeue_style( 'wp-block-library-theme' );
    // wp_dequeue_style( 'wc-blocks-style' ); // Remove WooCommerce block CSS

    inline_style($block_style); // inline it ourselves
    inline_style($block_library_style); // inline it ourselves
    // inline_style($woo_block_style); // inline it ourselves
}
add_action( 'wp_enqueue_scripts', 'remove_block_css', 100 );

/**
 * Disable Yoast's Hidden love letter about using the WordPress SEO plugin.
 */
add_action( 'template_redirect', function () {
 
    if ( ! class_exists( 'WPSEO_Frontend' ) ) {
        return;
    }
 
    $instance = WPSEO_Frontend::get_instance();
 
    // make sure, future version of the plugin does not break our site.
    if ( ! method_exists( $instance, 'debug_mark') ) {
        return ;
    }
 
    // ok, let us remove the love letter.
     remove_action( 'wpseo_head', array( $instance, 'debug_mark' ), 2 );
}, 9999 );


/*
*   Remove the detail from the wordpress errors
*/
function no_wordpress_errors() {
    return 'Something is wrong';
}
add_filter('login_errors', 'no_wordpress_errors');


/*
*   Add the async attribute to loaded script tags.
*/
function add_async_attribute($tag, $handle) {
    $scripts_to_async = array(
        
    );
    foreach($scripts_to_async as $async_script) {
        if($async_script === $handle) {
            return str_replace('src', 'async="async" src', $tag);
        }
    }
    return $tag;
}

add_filter('script_loader_tag', 'add_async_attribute', 10, 2);

function add_lazy_script($tag, $handle) {
    $scripts_to_async = array(
        'essential.js',
        'deferred.js',
        'google-analytics.js',
        'google-tag-manager.js',
    );
    foreach($scripts_to_async as $async_script) {
        if($async_script === $handle) {
            return str_replace('src', 'async="async" data-src', $tag);
        }
    }
    return $tag;
}

add_filter('script_loader_tag', 'add_lazy_script', 10, 2);

function add_partytown_script($tag, $handle) {
    $scripts_to_async = array(
        'google-analytics.js',
        'google-tag-manager.js',
    );
    foreach($scripts_to_async as $async_script) {
        if($async_script === $handle) {
            return str_replace('<script', '<script type="text/partytown"', $tag);
        }
    }
    return $tag;
}

add_filter('script_loader_tag', 'add_partytown_script', 10, 2);

/*
*   Replaces the WP logo in the admin bar.
*/
function ec_dashboard_custom_logo() {
    echo '
    <style type="text/css">
        #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
        background-image: url(' . get_bloginfo('stylesheet_directory') . '/dist/images/admin_logo.svg)
        !important; background-position: 0 0; color:rgba(0, 0, 0, 0);background-size:cover;
    }

    #wpadminbar #wp-admin-bar-wp-logo.hover > .ab-item .ab-icon { background-position: 0 0; }

    </style>
    ';
}
add_action('wp_before_admin_bar_render', 'ec_dashboard_custom_logo');

/*
 *  Noindex Author 
 *  Adds a noindex meta tag on author archives so they are not indexed by Google
 */

function noindex_author() {
    if (is_author()) {
        echo '<meta name="robots" content="noindex" />';
    }
}
add_action('wp_head', 'noindex_author');

function reset_social_link_icons() {
    return array(
        'facebook_site'  => array(
            'icon' => '<i class="fab fa-facebook-f"></i>'
        ),
        'twitter_site'   => array(
            'prepend' => 'https://twitter.com/',
            'icon'    => '<i class="fab fa-twitter"></i>'
        ),
        'instagram_url' => array(
            'icon' => '<i class="fab fa-instagram"></i>'
        ),
        'linkedin_url'  => array(
            'icon' => '<i class="fab fa-linkedin-in"></i>'
        )
    );
}
add_filter('crowd_social_link_options','reset_social_link_icons');

function social_links_shortcode($atts) {
    $atts = shortcode_atts(array(
        'list'      => 1, // true for a <ul>, false for <div>
        'raw'       => 0, // true for text only, no HTML
        'css_class' => '', // class to add to the wrapper
        'delim'     => ' ' // entity between items
    ), $atts);

    $seo_data = get_option('wpseo_social');
    $options = apply_filters('crowd_social_link_options', array());
    $output = array();
    $wrapp_tag = 'div';

    if (sizeof($options) > 0) {
        if (!$atts['raw']) {
            if ($atts['list']) {
                $wrapp_tag = 'ul';
            }

            $output[] = '<' . $wrapp_tag . ' class="' . $atts['css_class'] . '">';
        }

        foreach ($seo_data as $seo_network => $url) {
            $network_settings = $options[$seo_network];

            if(!empty($url) && !empty($network_settings)) {
                if (!empty($network_settings['prepend']))
                    $url = $network_settings['prepend'] . $url;
                if ($url && !empty($network_settings['icon']) && !$atts['raw']) {
                    if ($atts['list']) $output[] = '<li>';
                    $output[] = '<a target="_blank" href="' . esc_url_raw($url) . '">' . $network_settings['icon'] . '</a>';
                    if ($atts['list']) $output[] = '</li>';
                } else {
                    $output[] = esc_url_raw($url);
                }
            }
        }
        if (!$atts['raw'])
            $output[] = '</' . $wrapp_tag . '>';
    }

    if (!empty($output)) return join($atts['delim'], $output);
}
add_shortcode('social_links', 'social_links_shortcode');

function get_social_share_links() {
    global $post;
        
        // Get page details
    $share_url = urlencode(get_permalink());
    $share_thumb = get_the_post_thumbnail_url(get_the_post_thumbnail());
    $share_title = str_replace( ' ', '%20', get_the_title());        

    // Construct sharing URL
    $facebookURL = 'https://www.facebook.com/sharer/sharer.php?u='.$share_url;
    $twitterURL = 'https://twitter.com/intent/tweet?text='.$share_title.'&amp;url='.$share_url.'&amp;via='.get_the_author_meta('twitter');        
    $whatsappURL = 'whatsapp://send?text='.$share_title . ' ' . $share_url;

    return array(
        'facebook' => $facebookURL,
        'twitter' => $twitterURL,
        'whatsapp' => $whatsappURL
    );
}

function inline_script($src,$deps = array(),$footer = true) {
    if ( $src ) {
        wp_register_script( $src, '' );
        wp_enqueue_script( $src, '', $deps, get_cache_ver(), $footer );
        wp_add_inline_script( $src, file_get_contents($_SERVER['DOCUMENT_ROOT'] . parse_url($src)['path']) );
    }
}

function inline_style($src) {
    if ( $src ) {
        wp_register_style( $src, '' );
        wp_enqueue_style( $src, '', array(), get_cache_ver(), 'all' );
        wp_add_inline_style( $src, file_get_contents($_SERVER['DOCUMENT_ROOT'] . parse_url($src)['path']) );
    }
}

if ( !function_exists( 'wp_password_change_notification' ) ) {
    function wp_password_change_notification() {}
}

function replace_empty_image_alt($metadata, $object_id, $meta_key, $single){

    // make sure we're getting the alt meta
    if ($meta_key == '_wp_attachment_image_alt') {

        // if the alt is empty, return the title of the current post
        if ($metadata == '') {
            global $post;
            return $post->post_title;
        }
    }

    // Return original if the check does not pass
    return $metadata;

}
add_filter( 'get_post_metadata', 'replace_empty_image_alt', 100, 4 );

if (function_exists('get_field') && !get_field('enable_rss','option')) {
    function disable_feeds() {
        wp_redirect( home_url(), 301 );
        die;
    }
      
      //Remove WP feeds
    add_action( 'do_feed',      'disable_feeds', -1 );
    add_action( 'do_feed_rdf',  'disable_feeds', -1 );
    add_action( 'do_feed_rss',  'disable_feeds', -1 );
    add_action( 'do_feed_rss2', 'disable_feeds', -1 );
    add_action( 'do_feed_atom', 'disable_feeds', -1 );
    
    // Disable comment feeds.
    add_action( 'do_feed_rss2_comments', 'disable_feeds', -1 );
    add_action( 'do_feed_atom_comments', 'disable_feeds', -1 );
    
    // Prevent feed links from being inserted in the <head> of the page.
    add_action( 'feed_links_show_posts_feed',    '__return_false', -1 );
    add_action( 'feed_links_show_comments_feed', '__return_false', -1 );
    remove_action( 'wp_head', 'feed_links',       2 );
    remove_action( 'wp_head', 'feed_links_extra', 3 );
}

function custom_render_block_core_image (
	string $block_content, 
	array $block
): string 
{
	if (
		$block['blockName'] === 'core/image' && 
		!is_admin() &&
		!wp_is_json_request()
	) {
		$html = '';

        $data = $block['attrs'];
        $data['image'] = $block['attrs']['id'];
        $data['class'] = $block['attrs']['className'] . ' wp-block-image';
        $data['figure'] = true;

		return Timber::compile('image.twig',$data);
	}

	return $block_content;
}

add_filter('render_block', 'custom_render_block_core_image', null, 2);



function login_redirect() {
    global $pagenow;
    // $page_title = $post->post_title;
    if((!is_user_logged_in() && $pagenow != 'wp-login.php') && !is_page('new-starter') && !is_page('new-starter-form') && !wp_is_mobile()){
        auth_redirect();
    }
          
}
add_action( 'wp', 'login_redirect' );

// save suggestions post
function save_posted_data( $posted_data ) {
    
    $args = array(
      'post_type' => 'suggestion',
      'post_status'=>'publish',
      'post_title'=>$posted_data['your-name'],
      'post_content'=>$posted_data['your-message'],
    );
    $post_id = wp_insert_post($args);

    if(!is_wp_error($post_id)){
      if( isset($posted_data['your-name']) ){
        update_post_meta($post_id, 'your-name', $posted_data['your-name']);
      }

      if( isset($posted_data['your-message']) ){
        update_post_meta($post_id, 'your-message', $posted_data['your-message']);
      }
   return $posted_data;
  }
}

add_filter( 'wpcf7_posted_data', 'save_posted_data' );

// ACF Default Image field
function add_defult_image_field($field) {
    acf_render_field_setting( $field, array(
        'label'         => 'Default Image',
        'instructions'      => 'Appears when creating a new post',
        'type'          => 'image',
        'name'          => 'default_value',
    ));
}
add_action('acf/render_field_settings/type=image', 'add_defult_image_field');

function reset_default_image($value, $post_id, $field) {
    if (!$value) {
      $value = $field['default_value'];
    }
    return $value;
  }
add_filter('acf/load_value/type=image', 'reset_default_image', 10, 3);

// disable admin bar for all users not admin
function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
    show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'remove_admin_bar');