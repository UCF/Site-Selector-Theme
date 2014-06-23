<?php
require_once('functions/base.php');   			# Base theme functions
require_once('custom-taxonomies.php');  		# Where per theme taxonomies are defined
require_once('custom-post-types.php');  		# Where per theme post types are defined
require_once('functions/admin.php');  			# Admin/login functions
require_once('functions/config.php');			# Where per theme settings are registered
require_once('shortcodes.php');         		# Per theme shortcodes

//Add theme-specific functions here.

/**
 * Returns a theme option value or NULL if it doesn't exist
 **/
function get_theme_option($key) {
	global $theme_options;
	return isset($theme_options[$key]) ? $theme_options[$key] : NULL;
}


/**
 * Disable the standard wysiwyg editor for this theme to prevent markup from being blown away by
 * WYSIWYG users.
 **/
function disable_wysiwyg($c) {
    global $post_type;

    if ('page' == $post_type && get_theme_option('enable_page_wysiwyg') == 1) {
        return false;
    }
    return $c;
}
add_filter('user_can_richedit', 'disable_wysiwyg');


/**
 * Returns the url of the parallax feature's/page's featured image by the
 * size specified.
 *
 * @param int $feature_id    - post ID of the parallax feature or page with featured image
 * @param string $size       - image size registered with Wordpress to fetch the image by
 * @param string $cpt_field  - name (including prefix) of the meta field for the potential overridden image
 * @return string
 **/
function get_parallax_feature_img($post_id, $size, $cpt_field) {
	$featured_img_id = get_post_thumbnail_id($post_id);
	$thumb = null;
	$generated_thumb = wp_get_attachment_image_src($featured_img_id, $size);
	$custom_thumb = wp_get_attachment_url(get_post_meta($post_id, $cpt_field, true));

	$thumb = $custom_thumb ? $custom_thumb : $generated_thumb[0];
	return $thumb;
}


/**
 * Output CSS necessary for responsive parallax features.
 *
 * @param int $post_id        - post ID of the parallax feature or page
 * @param string $d_cpt_field - name (including prefix) of the meta field for the potential overridden image for desktop browsers
 * @param string $t_cpt_field - name (including prefix) of the meta field for the potential overridden image for tablet browsers
 * @param string $m_cpt_field - name (including prefix) of the meta field for the potential overridden image for mobile browsers
 **/
function get_parallax_feature_css($post_id, $d_cpt_field, $t_cpt_field, $m_cpt_field) {
	$featured_img_id = get_post_thumbnail_id($post_id);

	$featured_img_f = wp_get_attachment_image_src($featured_img_id, 'parallax_feature-full');
	$featured_img_d = get_parallax_feature_img($post_id, 'parallax_feature-desktop', $d_cpt_field);
	$featured_img_t = get_parallax_feature_img($post_id, 'parallax_feature-tablet', $t_cpt_field);
	$featured_img_m = get_parallax_feature_img($post_id, 'parallax_feature-mobile', $m_cpt_field);
	if ($featured_img_f) { $featured_img_f = $featured_img_f[0]; }

	ob_start();
?>
	<style type="text/css">
		<?php if ($featured_img_f) { ?>
		@media all and (min-width: 1200px) { #photo_<?=$post_id?> { background-image: url('<?=$featured_img_f?>'); } }
		<?php } ?>
		<?php if ($featured_img_d) { ?>
		@media all and (max-width: 1199px) and (min-width: 768px) { #photo_<?=$post_id?> { background-image: url('<?=$featured_img_d?>'); } }
		<?php } ?>
		<?php if ($featured_img_t) { ?>
		@media all and (max-width: 767px) and (min-width: 481px) { #photo_<?=$post_id?> { background-image: url('<?=$featured_img_t?>'); } }
		<?php } ?>
		<?php if ($featured_img_m) { ?>
		@media all and (max-width: 480px) { #photo_<?=$post_id?> { background-image: url('<?=$featured_img_m?>'); } }
		<?php } ?>
	</style>
	<!--[if lt IE 9]>
	<style type="text/css">
		#photo_<?=$post_id?> { background-image: url('<?=$featured_img_d?>'); }
	</style>
	<![endif]-->
<?php
	return ob_get_clean();
}


/**
 * Display a subpage parallax header image.
 **/
function get_parallax_page_header($page_id) {
	$page = get_post($page_id);
	ob_start();
	print get_parallax_feature_css($page_id, 'page_image_d', 'page_image_t', 'page_image_m');
	?>
	<section class="parallax-content parallax-header">
		<div class="parallax-photo" id="photo_<?=$page_id?>" data-stellar-background-ratio="0.5">
			<div class="container parallax-header-inner">
				<div class="row parallax-header-inner">
					<div class="span12 parallax-header-inner">
						<h1><?=$page->post_title?></h1>
						<div class="cta">
							<?php print get_cta_link(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}


/**
 * Displays a call to action link, using the page link provided in Theme Options.
 **/
function get_cta_link() {
	$link = get_permalink(get_post(get_theme_option('cta'))->ID);
	ob_start();
?>
	<a href="<?=$link?>">Partner with us.</a>
<?php
	return ob_get_clean();
}


/**
 * Hide unused admin tools (Links, Comments, etc)
 **/
function hide_admin_links() {
	remove_menu_page('link-manager.php');
	remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'hide_admin_links');

?>