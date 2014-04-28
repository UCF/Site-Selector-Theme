<?php
require_once('functions/base.php');   			# Base theme functions
require_once('custom-taxonomies.php');  		# Where per theme taxonomies are defined
require_once('custom-post-types.php');  		# Where per theme post types are defined
require_once('functions/admin.php');  			# Admin/login functions
require_once('functions/config.php');			# Where per theme settings are registered
require_once('shortcodes.php');         		# Per theme shortcodes

//Add theme-specific functions here.

/**
 * Returns the url of the homepage feature's featured image by the
 * size specified.
 *
 * @param int $feature_id    - post ID of the homepage feature
 * @param string $size       - image size registered with Wordpress to fetch the image by
 * @param string $cpt_field  - name (including prefix) of the meta field for the potential overridden image
 * @return string
 **/
function get_homepage_feature_img($feature_id, $size, $cpt_field) {
	$featured_img_id = get_post_thumbnail_id($feature_id);
	$thumb = null;
	$generated_thumb = wp_get_attachment_image_src($featured_img_id, $size);
	$custom_thumb = wp_get_attachment_url(get_post_meta($feature_id, $cpt_field, true));

	$thumb = $custom_thumb ? $custom_thumb : $generated_thumb[0];
	return $thumb;
}

?>