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
						<p id="footer-cta">And we&rsquo;re looking for our next great partner. <strong>Partner with us.</strong></p>
						<p id="footer-logo">
							<a target="_blank" href="http://www.ucf.edu/"><img src="<?=THEME_IMG_URL?>/logo.png" alt="Go to UCF.edu" title="Go to UCF.edu" /></a>
						</p>
					</div>
				</div>
			</div>
		</footer>
	</body>
	<?="\n".footer_()."\n"?>
</html>