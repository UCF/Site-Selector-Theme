<?php disallow_direct_load('single.php');?>
<?php get_header(); the_post();?>

	<div class="row page-content" id="<?=$post->post_name?>">
		<div class="span9">
			<article>
				<?php if(!is_front_page())	{ ?>
						<h1><?php the_title();?></h1>
				<?php } ?>
				<?php the_content();?>
			</article>
		</div>

		<div id="sidebar" class="span3">
			<?=get_sidebar();?>
		</div>
	</div>

	<?php get_template_part('includes/below-the-fold'); ?>

<?php get_footer();?>
