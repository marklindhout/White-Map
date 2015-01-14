<div class="bottom_widget_area" role="complementary">
<?php
	if ( is_active_sidebar( 'slide_in_menu_bottom' ) ) {
		dynamic_sidebar('slide_in_menu_bottom');
	}
	else {
?>
	
	<div class="widget widget_text">
		<h3 class="widgettitle"><?php _e('Welcome to White-Map', 'whitemap') ?></h3>
		<div class="textwidget">
			<p><?php _e('White-Map is a responsive, mobile-first Wordpress theme focused on locations.', 'whitemap') ?></p>
			<p><?php _e('To add widgets here, go to <strong>Design > Widgets</strong>', 'whitemap') ?></p>
		</div>	
	</div>

	<div class="widget widget_search">
		<h3 class="widgettitle"><?php _e('Search', 'whitemap') ?></h3>
		<?php get_search_form(); ?>
	</div>

<?php } ?>
</div>