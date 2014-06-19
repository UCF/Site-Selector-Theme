<?php get_header(); the_post();?>

<main class="page" id="<?=$post->post_name?>">
<?php
$featured_img_id = get_post_thumbnail_id($post->ID);
$featured_img_f = wp_get_attachment_image_src($featured_img_id, 'parallax_feature-full');
if ($featured_img_f) { ?>
	<?php print get_parallax_page_header($post->ID); ?>
	<section class="page-content">
		<?php the_content(); ?>
	</section>
<?php } else { ?>
	<section class="page-content">
		<div class="container">
			<div class="row">
				<div class="span12">
					<h1><?php the_title();?></h1>
				</div>
			</div>
		</div>
		<?php the_content(); ?>
	</section>
<?php } ?>
</main>

<?php get_footer();?>