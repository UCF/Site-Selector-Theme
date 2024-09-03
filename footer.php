		<footer>
			<div class="container">
				<div class="row">
					<div class="span12">
						<nav>
							<?=wp_nav_menu(array(
								'theme_location' => 'nav-menu', 
								'container' => 'false', 
								'menu_class' => 'menu horizontal', 
								'menu_id' => 'footer-menu', 
								'fallback_cb' => false,
								'depth' => 1
								));
							?>
						</nav>
						<?php if ( $theme_content = get_theme_mod( 'footer_content' ) ) : ?>
							<?php echo $theme_content; ?>
						<?php endif; ?>
						<p class="footer-logo">
							<a target="_blank" href="https://www.ucf.edu/">Go to ucf.edu</a>
						</p>
					</div>
				</div>
			</div>
		</footer>
	</body>
	<?="\n".footer_()."\n"?>
</html>