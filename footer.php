			<div id="footer">
				
				<?=wp_nav_menu(array(
					'theme_location' => 'footer-menu', 
					'container' => 'false', 
					'menu_class' => 'menu horizontal', 
					'menu_id' => 'footer-menu', 
					'fallback_cb' => false,
					'depth' => 1,
					'walker' => new Bootstrap_Walker_Nav_Menu()
					));
				?>
			</div>
		</div>
	</body>
	<?="\n".footer_()."\n"?>
</html>