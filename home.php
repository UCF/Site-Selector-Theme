<?php get_header(); ?>
<?php
$home = get_page_by_title('Home');
if (!$home) {
	global $post;
	$home = $post; // get something...
}
?>

<main class="home">
	<h1><?=get_bloginfo('name')?></h1>
	<?=apply_filters('the_content', $home->post_content);?>
</main>

<?php get_footer(); ?>
