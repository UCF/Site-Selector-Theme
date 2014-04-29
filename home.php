<?php get_header(); the_post();?>
<?php
$home = get_page_by_title('Home');
if (!$home) {
	$home = $post; // get something...
}
?>

<main class="home">
	<h1><?=get_bloginfo('name')?></h1>
	<?=apply_filters('the_content', $home->post_content);?>
</main>

<?php get_footer(); ?>