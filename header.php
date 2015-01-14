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

		<?php
			$apple_touch_icon = whitemap_get_option('apple_touch_icon');
			if (!empty($apple_touch_icon)) {
			?>
				<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo whitemap_get_option('apple_touch_icon'); ?>">
				<link rel="apple-touch-icon-precomposed" href="<?php echo whitemap_get_option('apple_touch_icon'); ?>">
			<?php
			}

			$faviconpng = whitemap_get_option('favicon_png');
			if (!empty($favicon_png)) {
			?>
				<link rel="icon" href="<?php echo whitemap_get_option('favicon_png'); ?>">
			<?php
			}

			$favicon = whitemap_get_option('favicon');
			if (!empty($favicon)) {
			?>
				<!--[if IE]><link rel="shortcut icon" href="<?php echo whitemap_get_option('favicon'); ?>"><![endif]-->
			<?php
			}
			
			$main_color = whitemap_get_option('main_color');
			if (!empty($main_color)) {
			?>
				<meta name="msapplication-TileColor" content="<?php echo whitemap_get_option('main_color'); ?>">
			<?php
			}
			
			$windows_tile_icon = whitemap_get_option('windows_tile_icon');
			if (!empty($windows_tile_icon)) {
			?>
				<meta name="msapplication-TileImage" content="<?php echo whitemap_get_option('windows_tile_icon'); ?>">
			<?php
			}
		?>

		<meta name="application-name" content="<?php bloginfo('name'); ?>">

		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<script type="text/javascript">
			var template_directory_uri = '<?php echo get_template_directory_uri(); ?>';
		</script>

		<?php wp_head(); ?>
	
	</head>

	<body <?php body_class(); ?>>

		<div id="menu">
			<nav class="slide_in_menu" role="navigation">
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

			<?php get_sidebar(); ?>
		</div>

		<div id="container">

			<header id="header">
				<?php
					$logo = whitemap_get_option('site_logo');
					$lc = ( isset($logo) && !empty($logo) ? 'logo' : 'nologo' );
				?>
				<h1 class="<?php echo $lc; ?>"><a href="<?php echo home_url(); ?>" rel="nofollow"><?php bloginfo('name'); ?></a></h1>
				<div id="menutoggle" class="cf">
					<a class="button"><i class="fa fa-bars"></i></a>
				</div>
				<?php
					if ( is_user_logged_in() ) {
					?>
						<div class="admin_links">
							<a class="button" href="<?php echo admin_url(); ?>">
								<i class="fa fa-gear"></i>
							</a>
						</div>
					<?php
					}
				?>
			</header>
