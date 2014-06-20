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
								'depth' => 1,
								'walker' => new Bootstrap_Walker_Nav_Menu()
								));
							?>
						</nav>
						<p class="footer-cta">We&rsquo;re looking for our next great partner. <?php print get_cta_link(); ?></p>
						<p class="footer-logo">
							<a target="_blank" href="http://www.ucf.edu/">Go to ucf.edu</a>
						</p>
					</div>
				</div>
			</div>
		</footer>
	</body>
	<?="\n".footer_()."\n"?>
</html>