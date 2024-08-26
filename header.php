<!DOCTYPE html>
<html lang="en-US">
	<head>
		<?="\n".header_()."\n"?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<?php if (GA4_ACCOUNT) : ?>
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo GA4_ACCOUNT; ?>"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', '<?php echo GA4_ACCOUNT; ?>');
		</script>
		
		<?php elseif(GA_ACCOUNT or CB_UID):?>

		<script type="text/javascript">
			var _sf_startpt = (new Date()).getTime();
			<?php if(GA_ACCOUNT):?>

			var GA_ACCOUNT  = '<?=GA_ACCOUNT?>';
			var _gaq        = _gaq || [];
			_gaq.push(['_setAccount', GA_ACCOUNT]);
			_gaq.push(['_setDomainName', 'none']);
			_gaq.push(['_setAllowLinker', true]);
			_gaq.push(['_trackPageview']);
			<?php endif;?>
			<?php if(CB_UID):?>

			var CB_UID      = '<?=CB_UID?>';
			var CB_DOMAIN   = '<?=CB_DOMAIN?>';
			<?php endif?>

		</script>

		<?php endif;?>
		<script type="text/javascript">
			var PostTypeSearchDataManager = {
				'searches' : [],
				'register' : function(search) {
					this.searches.push(search);
				}
			}
			var PostTypeSearchData = function(column_count, column_width, data) {
				this.column_count = column_count;
				this.column_width = column_width;
				this.data         = data;
			}
		</script>

	</head>
	<body class="<?=body_classes()?>">
		<?php if ( has_nav_menu( 'nav-menu' ) ) : ?>
		<nav class="header-nav">
			<a class="mobile-nav-toggle" href="#"><div class="hamburger"></div>Menu</a>
			<?=wp_nav_menu(array(
				'theme_location' => 'nav-menu',
				'container' => false,
				'menu_class' => 'menu '.get_header_styles(),
				'menu_id' => 'header-menu',
				'depth' => 1
				));
			?>
		</nav>
		<?php endif; ?>
