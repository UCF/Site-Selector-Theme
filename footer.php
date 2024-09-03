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
						<p class="footer-cta">With more than 69,000 students and four campuses anchoring industry clusters, we fuel Orlando’s $194 billion economy and inspire impactful discoveries to better the world.<?php print get_cta_link(); ?></p>
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