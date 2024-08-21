<?php

/**
 * Responsible for running code that needs to be executed as wordpress is
 * initializing.  Good place to register scripts, stylesheets, theme elements,
 * etc.
 *
 * @return void
 * @author Jared Lang
 **/
function __init__(){
	add_theme_support('menus');
	add_theme_support('post-thumbnails');
	add_image_size('parallax_feature-full', 2000, 1200, true);
	add_image_size('parallax_feature-desktop', 1199, 925, true);
	add_image_size('parallax_feature-tablet', 767, 775, true);
	add_image_size('parallax_feature-mobile', 480, 475, true);
	register_nav_menu('nav-menu', __('Navigation Menu'));
	register_sidebar(array(
		'name'          => __('Sidebar'),
		'id'            => 'sidebar',
		'description'   => 'Sidebar found on two column page templates and search pages',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
	));

	global $timer;
	$timer = Timer::start();

	set_defaults_for_options();
}
add_action('after_setup_theme', '__init__');


function sst_is_admin_asset( $asset ) {
	return isset( $asset['admin'] ) && $asset['admin'] === true ? true : false;
}

function sst_add_scripts() {
	foreach( Config::$styles as $style ) {
		if ( ! sst_is_admin_asset( $style ) ) {
			Config::add_css( $style );
		}
	}
	foreach( Config::$scripts as $script ) {
		if ( ! sst_is_admin_asset( $script ) ) {
			Config::add_script( $script );
		}
	}
	wp_deregister_script('l10n');
}

add_action( 'wp_enqueue_scripts', 'sst_add_scripts' );

function sst_add_admin_scripts() {
	foreach( Config::$styles as $style ) {
		if ( sst_is_admin_asset( $style ) ) {
			Config::add_css( $style );
		}
	}
	foreach( Config::$scripts as $script ) {
		if ( sst_is_admin_asset( $script ) ) {
			Config::add_script( $script );
		}
	}
}
add_action( 'admin_enqueue_scripts', 'sst_add_admin_scripts' );


# Set theme constants
#define('DEBUG', True);                  # Always on
#define('DEBUG', False);                 # Always off
define('DEBUG', isset($_GET['debug'])); # Enable via get parameter
define('THEME_URL', get_bloginfo('stylesheet_directory'));
define('THEME_ADMIN_URL', get_admin_url());
define('THEME_DIR', get_stylesheet_directory());
define('THEME_INCLUDES_DIR', THEME_DIR.'/includes');
define('THEME_STATIC_URL', THEME_URL.'/static');
define('THEME_IMG_URL', THEME_STATIC_URL.'/img');
define('THEME_JS_URL', THEME_STATIC_URL.'/js');
define('THEME_CSS_URL', THEME_STATIC_URL.'/css');
define('THEME_OPTIONS_GROUP', 'settings');
define('THEME_OPTIONS_NAME', 'theme');
define('THEME_OPTIONS_PAGE_TITLE', 'Theme Options');

$theme_options = get_option(THEME_OPTIONS_NAME);
define('GA_ACCOUNT', isset( $theme_options['ga_account'] ) ? $theme_options['ga_account'] : null );
define('GA4_ACCOUNT', isset( $theme_options['ga4_account'] ) ? $theme_options['ga4_account'] : null );
define('CB_UID', isset ( $theme_options['cb_uid'] ) ? $theme_options['cb_uid'] : null );
define('CB_DOMAIN', isset( $theme_options['cb_domain'] ) ? $theme_options['cb_domain'] : null );


/**
 * Set config values including meta tags, registered custom post types, styles,
 * scripts, and any other statically defined assets that belong in the Config
 * object.
 **/
Config::$custom_post_types = array(
	'Page',
	'Post',
	'ParallaxFeature'
);

Config::$body_classes = array('default',);

/**
 * Grab array of pages for Config::$theme_settings:
 **/
$pages = get_posts(array('post_type' => 'page'));
$pages_array = array();
foreach ($pages as $page) {
	$pages_array[$page->post_title] = $page->ID;
}

/**
 * Configure theme settings, see abstract class Field's descendants for
 * available fields. -- functions/base.php
 **/
Config::$theme_settings = array(
	'Analytics' => array(
		new TextField(array(
			'name'        => 'Google WebMaster Verification',
			'id'          => THEME_OPTIONS_NAME.'[gw_verify]',
			'description' => 'Example: <em>9Wsa3fspoaoRE8zx8COo48-GCMdi5Kd-1qFpQTTXSIw</em>',
			'default'     => null,
			'value'       => isset( $theme_options['gw_verify'] ) ? $theme_options['gw_verify'] : null,
		)),
		new TextField(array(
			'name'        => 'Google Analytics Account',
			'id'          => THEME_OPTIONS_NAME.'[ga_account]',
			'description' => 'Example: <em>UA-9876543-21</em>. Leave blank for development.',
			'default'     => null,
			'value'       => isset( $theme_options['ga_account'] ) ? $theme_options['ga_account'] : null,
		)),
		new TextField(array(
			'name'        => 'Google Analytics 4 Account',
			'id'          => THEME_OPTIONS_NAME.'[ga4_account]',
			'description' => 'Example: <em>G-9876543</em>. Will override the Universal Analytics account above. Leave blank for development.'
		))
	),
	'Site' => array(
		new TextField(array(
			'name'        => 'Contact Email',
			'id'          => THEME_OPTIONS_NAME.'[site_contact]',
			'description' => 'Contact email address that visitors to your site can use to contact you.',
			'value'       => isset( $theme_options['site_contact'] ) ? $theme_options['site_contact'] : null,
		)),
		new TextField(array(
			'name'        => 'Organization Name',
			'id'          => THEME_OPTIONS_NAME.'[organization_name]',
			'description' => 'Your organization\'s name',
			'value'       => isset( $theme_options['organization_name'] ) ? $theme_options['organization_name'] : null,
		)),
		new SelectField(array(
			'name'        => 'Global Call to Action link',
			'id'          => THEME_OPTIONS_NAME.'[cta]',
			'description' => 'Page where the "Partner with Us" links used on the site direct to.',
			'choices'     => $pages_array,
			'default'     => isset( $pages_array[0] ) ? $pages_array[0] : null,
			'value'       => isset( $theme_options['cta'] ) ? $theme_options['cta'] : null,
		)),
	),
	'Social' => array(
		new RadioField(array(
			'name'        => 'Enable OpenGraph',
			'id'          => THEME_OPTIONS_NAME.'[enable_og]',
			'description' => 'Turn on the opengraph meta information used by Facebook.',
			'default'     => 1,
			'choices'     => array(
				'On'  => 1,
				'Off' => 0,
			),
			'value'       => $theme_options['enable_og'],
	    )),
		new TextField(array(
			'name'        => 'Facebook Admins',
			'id'          => THEME_OPTIONS_NAME.'[fb_admins]',
			'description' => 'Comma seperated facebook usernames or user ids of those responsible for administrating any facebook pages created from pages on this site. Example: <em>592952074, abe.lincoln</em>',
			'default'     => null,
			'value'       => isset( $theme_options['fb_admins'] ) ? $theme_options['fb_admins'] : null,
		)),
		new TextField(array(
			'name'        => 'Facebook URL',
			'id'          => THEME_OPTIONS_NAME.'[facebook_url]',
			'description' => 'URL to the facebook page you would like to direct visitors to.  Example: <em>https://www.facebook.com/CSBrisketBus</em>',
			'default'     => null,
			'value'       => isset( $theme_options['facebook_url'] ) ? $theme_options['facebook_url'] : null,
		)),
		new TextField(array(
			'name'        => 'Twitter URL',
			'id'          => THEME_OPTIONS_NAME.'[twitter_url]',
			'description' => 'URL to the twitter user account you would like to direct visitors to.  Example: <em>http://twitter.com/csbrisketbus</em>',
			'value'       => isset( $theme_options['twitter_url'] ) ? $theme_options['twitter_url'] : null,
		)),
	),
	'Web Fonts' => array(
		new TextField(array(
			'name'        => 'Cloud.Typography CSS Key URL',
			'id'          => THEME_OPTIONS_NAME.'[cloud_font_key]',
			'description' => 'The CSS Key provided by Cloud.Typography for this project. <strong>Only include the value in the "href" portion of the link
							tag provided; e.g. "//cloud.typography.com/000000/000000/css/fonts.css".</strong><br/><br/>NOTE: Make sure the Cloud.Typography
							project has been configured to deliver fonts to this site\'s domain.<br/>
							See the <a target="_blank" href="http://www.typography.com/cloud/user-guide/managing-domains">Cloud.Typography docs on managing domains</a> for more info.',
			'default'     => '//cloud.typography.com/730568/675644/css/fonts.css', /* CSS Key relative to PROD project */
			'value'       => $theme_options['cloud_font_key'],
		)),
	),
	'Settings' => array(
		new RadioField(array(
			'name'        => 'Disable WYSIWYG editor on pages',
			'id'          => THEME_OPTIONS_NAME.'[enable_page_wysiwyg]',
			'description' => 'Disables the WYSIWYG editor for pages, forcing the text editor for all users.  Recommended for this site to avoid custom markup from being blown away by switching editors.',
			'default'     => 1,
			'choices'     => array(
				'On'  => 1,
				'Off' => 0,
			),
			'value'       => $theme_options['enable_page_wysiwyg'],
	    )),
	),
);

Config::$links = array(
	array('rel' => 'shortcut icon', 'href' => THEME_IMG_URL.'/favicon.ico',),
	array('rel' => 'alternate', 'type' => 'application/rss+xml', 'href' => get_bloginfo('rss_url'),),
);


Config::$styles = array(
	array('admin' => True, 'src' => THEME_CSS_URL.'/admin.css',),
	THEME_STATIC_URL.'/bootstrap/bootstrap/css/bootstrap.css',
	THEME_STATIC_URL.'/bootstrap/bootstrap/css/bootstrap-responsive.css',
	//THEME_CSS_URL.'/webcom-base.css',
	get_bloginfo('stylesheet_url'),
	THEME_URL.'/style-responsive.css'
);

if (!empty($theme_options['cloud_font_key'])) {
	array_push(Config::$styles, array('name' => 'font-cloudtypography', 'src' => $theme_options['cloud_font_key']));
	//array_push(Config::$styles, array('name' => 'font-cloudtypography-admin', 'admin' => True, 'src' => $theme_options['cloud_font_key']));
}

Config::$scripts = array(
	array('admin' => True, 'src' => THEME_JS_URL.'/admin.js',),
	array('name' => 'ucfhb-script', 'src' => '//universityheader.ucf.edu/bar/js/university-header.js',),
	THEME_STATIC_URL.'/bootstrap/bootstrap/js/bootstrap.js',
	array('name' => 'stellar-js',  'src' => THEME_JS_URL.'/jquery.stellar.min.js',),
	array('name' => 'fittext-js',  'src' => THEME_JS_URL.'/jquery.fittext.js',),
	array('name' => 'base-script',  'src' => THEME_JS_URL.'/webcom-base.js',),
	array('name' => 'theme-script', 'src' => THEME_JS_URL.'/script.js',),
);

Config::$metas = array(
	array('charset' => 'utf-8',),
);
if ( isset( $theme_options['gw_verify'] ) && $theme_options['gw_verify'] ) {
	Config::$metas[] = array(
		'name'    => 'google-site-verification',
		'content' => htmlentities($theme_options['gw_verify']),
	);
}



function jquery_in_header() {
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');
    wp_enqueue_script( 'jquery' );
}

add_action('wp_enqueue_scripts', 'jquery_in_header');
