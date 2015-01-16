<?php

/********************************************************
CLEANUP HEAD
********************************************************/

function whitemap_head_cleanup() {
	// category feeds
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	// post and comment feeds
	remove_action( 'wp_head', 'feed_links', 2 );
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// index link
	remove_action( 'wp_head', 'index_rel_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );
	// remove WP version from css
	add_filter( 'style_loader_src', 'whitemap_remove_wp_ver_css_js', 9999 );
	// remove Wp version from scripts
	add_filter( 'script_loader_src', 'whitemap_remove_wp_ver_css_js', 9999 );

}


/********************************************************
HIDE ADMIN BAR
********************************************************/

function whitemap_hide_admin_bar() {
	echo '<style type="text/css">.show-admin-bar {display: none;}</style>';
}
add_action( 'admin_print_scripts-profile.php', 'whitemap_hide_admin_bar' );
add_filter( 'show_admin_bar', '__return_false' );



/********************************************************
TITLE
********************************************************/

// A better title
// http://www.deluxeblogtips.com/2012/03/better-title-meta-tag.html
function rw_title( $title, $sep, $seplocation ) {
	global $page, $paged;

	// Don't affect in feeds.
	if ( is_feed() ) return $title;

	// Add the blog's name
	if ( 'right' == $seplocation ) {
		$title .= get_bloginfo( 'name' );
	} else {
		$title = get_bloginfo( 'name' ) . $title;
	}

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );

	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title .= " {$sep} {$site_description}";
	}

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 ) {
		$title .= " {$sep} " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );
	}

	return $title;

}



/********************************************************
CLEAN WP VERSION FROM FILES
********************************************************/

// remove WP version from RSS
function whitemap_rss_version() { return ''; }

// remove WP version from scripts
function whitemap_remove_wp_ver_css_js( $src ) {
	if ( strpos( $src, 'ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}

// remove injected CSS for recent comments widget
function whitemap_remove_wp_widget_recent_comments_style() {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

// remove injected CSS from recent comments widget
function whitemap_remove_recent_comments_style() {
	global $wp_widget_factory;
	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
		remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
	}
}


/********************************************************
SCRIPTS AND STYLES
********************************************************/

function whitemap_scripts_and_styles() {

	global $wp_styles;
	// call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way

	if (!is_admin()) {

		wp_enqueue_script(
			'modernizr',
			get_stylesheet_directory_uri() . '/library/js/vendor/modernizr/modernizr-2.6.2.min.js',
			array(),
			'2.6.2',
			false
		);

		wp_enqueue_style(
			'whitemap',
			get_stylesheet_directory_uri() . '/library/css/whitemap.min.css',
			array(),
			'',
			'all'
		);

		wp_deregister_script('jquery');
		wp_enqueue_script(
			'jquery',
			get_stylesheet_directory_uri() . '/library/js/vendor/jquery/jquery-2.1.1.min.js',
			array(),
			'2.1.1',
			false
		);

		wp_enqueue_script(
			'leaflet',
			get_stylesheet_directory_uri() . '/library/js/vendor/leaflet/leaflet.js',
			'',
			false // in footer ?
		);

		wp_enqueue_script(
			'whitemap',
			get_stylesheet_directory_uri() . '/library/js/whitemap.min.js',
			array( 'jquery' ),
			'',
			true // in footer ?
		);

		wp_localize_script(
			'whitemap',							// handle
			'WhiteMap',							// name
			array(								// data
				'map_default_location' => whitemap_get_default_map_location(),
				'locations'            => whitemap_get_locations(),
				'map_layer'            => whitemap_get_map_layer('default'),
				'map_marker_normal'    => whitemap_get_map_marker('normal'),
				'map_marker_active'    => whitemap_get_map_marker('active'),
			)
		);
	}
}

/********************************************************
THEME SUPPORT
********************************************************/

function whitemap_theme_support() {
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size(125, 125, true);

	add_theme_support( 'menus' );
	register_nav_menus(
		array(
			'main-nav' => __( 'Left Slide-in Menu', 'whitemap' ),
		)
	);
}



/********************************************************
RELATED POSTS (based on tags)
********************************************************/

// Related Posts Function (call using whitemap_related_posts(); )
function whitemap_related_posts() {
	echo '<ul id="whitemap-related-posts">';
	global $post;
	$tags = wp_get_post_tags( $post->ID );
	if($tags) {
		foreach( $tags as $tag ) {
			$tag_arr .= $tag->slug . ',';
		}
		$args = array(
			'tag' => $tag_arr,
			'numberposts' => 5, /* you can change this to show more */
			'post__not_in' => array($post->ID)
			);
		$related_posts = get_posts( $args );
		if($related_posts) {
			foreach ( $related_posts as $post ) : setup_postdata( $post ); ?>
			<li class="related_post"><a class="entry-unrelated" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
			<?php endforeach; }
			else { ?>
			<?php echo '<li class="no_related_post">' . __( 'No Related Posts Yet!', 'whitemap' ) . '</li>'; ?>
			<?php }
		}
		wp_reset_postdata();
		echo '</ul>';
}



/********************************************************
PAGE NAVI
********************************************************/
function whitemap_page_navi() {
	global $wp_query;
	$bignum = 999999999;
	if ( $wp_query->max_num_pages <= 1 )
		return;
	echo '<nav class="pagination">';
	echo paginate_links( array(
		'base'         => str_replace( $bignum, '%#%', esc_url( get_pagenum_link($bignum) ) ),
		'format'       => '',
		'current'      => max( 1, get_query_var('paged') ),
		'total'        => $wp_query->max_num_pages,
		'prev_text'    => '&larr;',
		'next_text'    => '&rarr;',
		'type'         => 'list',
		'end_size'     => 3,
		'mid_size'     => 3
		) );
	echo '</nav>';
} /* end page navi */



/********************************************************
CLEAN UP STUFF THINGSIES
********************************************************/

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function whitemap_filter_ptags_on_images($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}


// This removes the annoying […] to a Read More link
function whitemap_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '<a class="button excerpt-read-more" href="'. get_permalink($post->ID) . '" title="'. __( 'Read ', 'whitemap' ) . get_the_title($post->ID).'">'. __( 'Read more &raquo;', 'whitemap' ) .'</a>';
}


/************* THEME FILTERS AND HOOKS **********************/
function whitemap_init() {
	load_theme_textdomain( 'whitemap', get_template_directory() . '/library/translation' );
	add_action( 'init', 'whitemap_head_cleanup' );
	add_filter( 'wp_title', 'rw_title', 10, 3 );
	add_filter( 'the_generator', 'whitemap_rss_version' );
	add_filter( 'wp_head', 'whitemap_remove_wp_widget_recent_comments_style', 1 );
	add_action( 'wp_head', 'whitemap_remove_recent_comments_style', 1 );
	add_filter( 'gallery_style', 'whitemap_gallery_style' );
	add_action( 'wp_enqueue_scripts', 'whitemap_scripts_and_styles', 999 );
	whitemap_theme_support();
	add_action( 'widgets_init', 'whitemap_register_sidebars' );
	add_filter( 'the_content', 'whitemap_filter_ptags_on_images' );
	add_filter( 'excerpt_more', 'whitemap_excerpt_more' );
}
add_action( 'after_setup_theme', 'whitemap_init' );



/********************************************************
OEMBED OPTIONS
********************************************************/

if ( ! isset( $content_width ) ) {
	$content_width = 640;
}



/********************************************************
IMAGE SIZES
********************************************************/

add_image_size( 'whitemap-thumb-600', 600, 150, true );
add_image_size( 'whitemap-thumb-300', 300, 100, true );
add_image_size( 'whitemap-logo', 256, 128, true );



/********************************************************
SIDEBARS
********************************************************/

function whitemap_register_sidebars() {
	register_sidebar(array(
		'id' => 'slide_in_menu_bottom',
		'name' => __( 'Slide-in Menu (bottom)', 'whitemap' ),
		'description' => __( 'Area located at the bottom of the slide-in-menu on the left of the page.', 'whitemap' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));
}



/********************************************************
 COMMENT FORM
********************************************************/
require_once( 'library/comments-walker.php' );


function whitemap_get_overall_rating($id) {

	$args = array(
		'post_id'  => $id,
		'meta_key' => 'rating',
		'status'   => 'approve',
	);

	$comments_query = new WP_Comment_Query;
	$comments = $comments_query->query( $args );

	$overall_rating = array();

	if ( $comments ) {
		foreach ( $comments as $comment ) {
			array_push($overall_rating, get_comment_meta( $comment->comment_ID, 'rating', true) );
		}
	}

	return $overall_rating;
}

function whitemap_get_rating_average($id) {
	$overall_rating = whitemap_get_overall_rating($id);

	if ( count($overall_rating) > 0) {
		return array_sum($overall_rating) / count($overall_rating);
	} else {
		return 0;
	}
}

function whitemap_get_rating_count($id) {
	$overall_rating = whitemap_get_overall_rating($id);
	return count($overall_rating);
}



function whitemap_comment_form() {
	// This is a wrapper for comment_form() with some extensions that are specific for this theme

	global $current_user,$post;

	$commenter = wp_get_current_commenter();
	$req       = get_option( 'require_name_email' );
	$aria_req  = ( $req ? " aria-required='true'" : '' );

	$comments_args = array(
		'id_form'           => 'commentform',
		
		'id_submit'         => 'submit',
		
		'title_reply'       => __('Rate this place', 'whitemap'),
		
		'title_reply_to'    => null,
		
		'cancel_reply_link' => null,
		
		'label_submit'      => __('Post your rating', 'whitemap'),
		
		'comment_field'     => ''
							. '<div class="comment-form-rating"><label for="rating">' . __( 'Rating', 'whitemap' ) . '</label>'
							 . '<div class="rating">'
								 . '<input type="radio" name="rating" value="1" checked /><span></span>'
								 . '<input type="radio" name="rating" value="2" /><span></span>'
								 . '<input type="radio" name="rating" value="3" /><span></span>'
								 . '<input type="radio" name="rating" value="4" /><span></span>'
								 . '<input type="radio" name="rating" value="5" /><span></span>'
							 . '</div>'
							. '</div>'
							. '<div class="comment-form-comment"><label for="comment">'
							. _x( 'Comment', 'noun' )
							. '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true">'
							. '</textarea>'
							. '</div>',
		
		'must_log_in'       => '<div class="must-log-in">'
							. sprintf(__( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )	)
							. '</div>',
		
		'logged_in_as'      => '<div class="logged-in-as">'
							. sprintf(__( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) )
							. '</div>',
		
		'comment_notes_before' => '<div class="comment-notes">'
							. __( 'Your email address will not be published.', 'whitemap' )
							. ( $req ? $required_text : '' )
							. '</div>',

		'comment_notes_after' => '',
		
		'fields'            => apply_filters('comment_form_default_fields', array(
			'author' =>
				'<div class="comment-form-author"><label for="author">' . __( 'Name', 'domainreference' ) . '</label> ' .
				( $req ? '<span class="required">*</span>' : '' ) .
				'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
				'" size="30"' . $aria_req . ' /></div>',

			'email' =>
				'<div class="comment-form-email"><label for="email">' . __( 'Email', 'domainreference' ) . '</label> ' .
				( $req ? '<span class="required">*</span>' : '' ) .
				'<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
				'" size="30"' . $aria_req . ' /></div>',
			)
		),
	);


	$usercomment = get_comments(
		array(
			'author_email' => $current_user->user_email,
			'user_id' => $current_user->ID,
			'post_id' => $post->ID,
		)
	);

	if($usercomment) {
		echo "<!-- One comment per user per post -->";
	}
	else {
		comment_form($comments_args);
	}
	
}

function whitemap_save_comment_meta_data( $comment_id ) {

	if ( ( isset( $_POST['rating'] ) ) && ( $_POST['rating'] != '') ) {

		// Filter and verify the rating data here!
		$rating = wp_filter_nohtml_kses($_POST['rating']);

		// Add the rating to the comment
		add_comment_meta( $comment_id, 'rating', $rating );
	}

}
add_action( 'comment_post', 'whitemap_save_comment_meta_data' );




/********************************************************
WHITE MAP
********************************************************/
require_once( 'library/post-type-location.php' );
require_once( 'library/theme-customizer.php' );

// DEFAULT MAP LOCATION
function whitemap_get_default_map_location() {

	$rawlatlng = explode(',', get_theme_mod('default_coordinates'));

	// if the default location is set in theme settings, load it here.
	$default_map_location = array(
		'latitude' => $rawlatlng[0],
		'longitude' => $rawlatlng[1],
	);

	// for single locations we override the default location
	if ( is_single() && get_post_type() == 'location' ) {
		$single_lat = get_post_custom_values('whitemap_location_latitude');
		$single_lon = get_post_custom_values('whitemap_location_longitude');

		if ( !empty($single_lat[0]) || !isset($single_lat[0])) {
			$default_map_location['latitude'] = $single_lat[0];
		}

		if ( !empty($single_lon[0]) || !isset($single_lon[0])) {
			$default_map_location['longitude'] = $single_lon[0];
		}
	}

	// if none of the above work, default to something sensible, in this case Berlin, Germany.
	if (empty($default_map_location['latitude'])) {
		$default_map_location['latitude'] = '52.51202';
	}

	if (empty($default_map_location['longitude'])) {
		$default_map_location['longitude'] = '13.40891';
	}

	return $default_map_location;
}


// DEFAULT MAP LAYER
function whitemap_get_map_layer($type) {

	// Default to returning the url
	if ( !isset($type) ) {
		$type = 'default';
	}

	// Available map layers. Can be replaced by a function that extracts layers from theme options.
	$map_layers = array(
		'default' => array(
			'url'         => 'https://{s}.tiles.mapbox.com/v3/marklindhout.hpk7ih6p/{z}/{x}/{y}.png',
			// 'url'         => 'http://a.tile.stamen.com/toner/{z}/{x}/{y}.png',
			'attribution' => '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
			'opacity'     => '1.0',
		),
	);

	// Return the requested option if it exists
	if ( !empty($map_layers[$type]) ) {
		return $map_layers[$type];
	} else {
		return false;
	}
	
}


// DEFAULT MAP LOCATION MARKERS
function whitemap_get_map_marker($type) {
	
	// Default to returning the normal marker
	if ( !isset($type) ) {
		$type = 'normal';
	}

	// Load marker sizes into a temp var first, otherwise PHP <5.3 will be a  whiny little bitch.
	$default_marker_size = getimagesize( get_stylesheet_directory() . '/library/img/default_marker.png');
	$default_marker_active_size = getimagesize( get_stylesheet_directory() . '/library/img/default_marker.png');

	// The markers
	$map_markers = array(
		'normal' => array(
			'url'    => get_stylesheet_directory_uri() . '/library/img/default_marker.png',
			'width'  => $default_marker_size[0],
			'height' => $default_marker_size[1],
		),
		'active' => array(
			'url'    => get_stylesheet_directory_uri() . '/library/img/default_marker_active.png',
			'width'  => $default_marker_active_size[0],
			'height' => $default_marker_active_size[1],
		),
	);

	// Return the requested type if it exists
	if ( !empty($map_markers[$type]) ) {
		return $map_markers[$type];
	} else {
		return false;
	}
}



/***********************************************************
 JSON Location feed
**********************************************************/
function whitemap_get_locations() {

	global $wpdb;

	function return_tags_as_array($pid) {

		$tags = array();
		$rawtags = wp_get_object_terms( $pid, 'post_tag' );

		if (!empty($rawtags)){
			if (!is_wp_error($rawtags)) {
				foreach ($rawtags as $rawtag) {
					$tags[] = $rawtag->name;
				}
			}
		}

		return $tags;
	}

	// use get_posts because it is actually outside of the loop.
	$posts_query = new WP_Query(
		array(
			'posts_per_page' => 100,
			'offset' => 0,
			'orderby' => 'title',
			'order' => 'DESC',
			'post_type' => 'location',
			'post_status' => 'publish'
		)
	);

	$locations = array();

	foreach ($posts_query->posts as $post) {

		$pid = $post->ID;
		$desc_length = 128;
		$ray = array();
			
		$ray['id']          = $pid;
		$ray['permalink']   = get_permalink($pid);
		$ray['date']        = $post->post_date;
		$ray['timestamp']   = strtotime($post->post_date);
		$ray['title']       = $post->post_title;
		$ray['description'] = ( strlen($post->post_content) > $desc_length ? substr($post->post_content, 0, $desc_length) : $post->post_content);
		$ray['desc_is_cut'] = strlen($post->post_content) > $desc_length;
		$ray['street']      = get_post_meta($pid, 'whitemap_street_address', true);
		$ray['postal']      = get_post_meta($pid, 'whitemap_postal-code', true);
		$ray['city']        = get_post_meta($pid, 'whitemap_city', true);
		$ray['latitude']    = floatval(get_post_meta($pid, 'whitemap_location_latitude', true));
		$ray['longitude']   = floatval(get_post_meta($pid, 'whitemap_location_longitude', true));
		$ray['tags']        = return_tags_as_array($pid);
		$ray['new']         = date('U') - $ray['timestamp'] <= (60 * 60 * 24 * 5); // New if < 5 days ago

		$locations[]        = $ray;
	}
	wp_reset_postdata();

	return $locations;
}



/***********************************************************
 Generate custom CSS based on theme options input.
 **********************************************************/

function whitemap_theme_customizer_css() {

	// Custom logo
	$site_logo_id  = attachment_url_to_postid(get_theme_mod('site_logo'));
	$site_logo     = wp_get_attachment_image_src( $site_logo_id, 'whitemap-logo' );

	// Custom site color
	$site_color = get_theme_mod('site_color');

	// start the style file
	$css = '';

	if ( !empty($site_logo) ) {
		$css .= "\n";
		$css .= '#container #header .logo {' . "\n";
		$css .= 'background-image: url(' . $site_logo[0] . ');' . "\n";
		$css .= 'width: ' . $site_logo[1] . 'px;' . "\n";
		$css .= 'height: ' . $site_logo[2] . 'px;' . "\n";
		$css .= 'text-indent: ' . $site_logo[1] . 'px;' . "\n";
		$css .= 'line-height: ' . $site_logo[2] . 'px;' . "\n";
		$css .= '}' . "\n";
	}

	if ( !empty($site_color) ) {
		$css .= "\n";
		$css .= 'a {' . "color: " . $site_color . "}";
		$css .= "\n";
		$css .= '.button {' . "background-color: " . $site_color . "}";
		$css .= "\n";
		$css .= '.brand {' . "color: " . $site_color . " !important; }";
		$css .= "\n";
		$css .= '.brandbg {' . "background-color: " . $site_color . "}";
	}

	// end the style block
	$output = "\n" . '<style>' . "\n" . $css . "\n" . '</style>';

	// Echo all to the front end if there is something there
	if ( !empty($css) ) {
		echo $output;
	}


}
add_action( 'wp_head', 'whitemap_theme_customizer_css');


/***********************************************************
 HEDER ICONS
**********************************************************/

function whitemap_theme_customizer_icons() {

	$apple_touch_icon = wp_get_attachment_image_src( attachment_url_to_postid(get_theme_mod('appletouchicon')), array(152,152) );
	if (!empty($apple_touch_icon)) {
		?>
			<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo $apple_touch_icon[0]; ?>">
			<link rel="apple-touch-icon-precomposed" href="<?php echo $apple_touch_icon[0]; ?>">
		<?php
	}

	$faviconpng = wp_get_attachment_image_src( attachment_url_to_postid(get_theme_mod('siteicon')), array(32,32) );
	if (!empty($faviconpng)) {
	?>
	<link rel="icon" href="<?php echo $faviconpng[0]; ?>">
	<?php
	}

	$favicon = wp_get_attachment_image_src( attachment_url_to_postid(get_theme_mod('favicon')), array(16,16) );
	if (!empty($favicon)) {
		?>
			<!--[if IE]><link rel="shortcut icon" href="<?php echo $favicon[0]; ?>"><![endif]-->
		<?php
	}

	$main_color = get_theme_mod('site_color');
	if (!empty($main_color)) {
		?>
			<meta name="msapplication-TileColor" content="<?php echo $main_color; ?>">
		<?php
	}

	$windows_tile_icon = wp_get_attachment_image_src( attachment_url_to_postid(get_theme_mod('windowstileicon')), array(270,270) );
	if (!empty($windows_tile_icon)) {
		?>
			<meta name="msapplication-TileImage" content="<?php echo $windows_tile_icon[0]; ?>">
		<?php
	}
}
add_action( 'wp_head', 'whitemap_theme_customizer_icons');


