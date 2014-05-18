<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

  <head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?php wp_title(''); ?></title>
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

		<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo whitemap_get_option('apple_touch_icon'); ?>">
		<link rel="apple-touch-icon-precomposed" href="<?php echo whitemap_get_option('apple_touch_icon'); ?>">
		<link rel="icon" href="<?php echo whitemap_get_option('favicon_png'); ?>">
		<!--[if IE]><link rel="shortcut icon" href="<?php echo whitemap_get_option('favicon'); ?>"><![endif]-->
		
		<meta name="application-name" content="<?php bloginfo('name'); ?>">
		<meta name="msapplication-TileColor" content="<?php echo whitemap_get_option('header_background_color_top'); ?>">
		<meta name="msapplication-TileImage" content="<?php echo whitemap_get_option('windows_tile_icon'); ?>">

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<script type="text/javascript">
			var template_directory_uri = '<?php echo get_template_directory_uri(); ?>';
		</script>

		<?php wp_head(); ?>
	
		<script type="text/javascript">
			// var _gaq = _gaq || [];
			// _gaq.push(['_setAccount', 'UA-39293412-1']);
			// _gaq.push(['_trackPageview']);
			// (function() {
			// 	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			// 	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			// 	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			// })();
		</script>

	</head>

	<body <?php body_class(); ?>>

		<nav id="menu" role="navigation">
			<?php

				$menu_name = 'main-nav';

				if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
					$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
					$menu_items = wp_get_nav_menu_items($menu->term_id);
					$menu_list = '';

					foreach ( (array) $menu_items as $key => $menu_item ) {
						$title = $menu_item->title;
						$url = $menu_item->url;
						$menu_list .= '<a class="menu_link" href="' . $url . '">' . $title . '</a>';
					}

				} else {
					$menu_list = 'Menu "' . $menu_name . '" not defined.';
				}

				echo $menu_list;

			?>
		</nav>

		<div id="container">

			<header id="header">
				<h1 class="logo"><a href="<?php echo home_url(); ?>" rel="nofollow"><?php bloginfo('name'); ?></a></h1>
				<div id="menutoggle" class="cf">
					<a class="button"><i class="fa fa-bars"></i></a>
				</div>
				<?php
					if ( is_user_logged_in() ) {
						echo '<div class="admin_link cf"><a class="button" href="'.admin_url().'">'.__('Admin','whitemap').'</a></div>';
					}
				?>
			</header>
