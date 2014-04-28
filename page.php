<?php get_header(); the_post();?>
<main class="page-content">
	<div class="container">
		<div class="row" id="<?=$post->post_name?>">
			<div class="span12">
				<article>
					<h1><?php the_title();?></h1>
					<?php the_content();?>
				</article>
			</div>
		</div>
	</div>
</main>
<?php get_footer();?>