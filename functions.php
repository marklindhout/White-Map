<?php

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


// Hide the admin bar
function whitemap_hide_admin_bar() {
	echo '<style type="text/css">.show-admin-bar {display: none;}</style>';
}
add_action( 'admin_print_scripts-profile.php', 'whitemap_hide_admin_bar' );
add_filter( 'show_admin_bar', '__return_false' );


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

// remove injected CSS from gallery
function whitemap_gallery_style($css) {
	return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
}


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
			get_stylesheet_directory_uri() . '/library/css/white-map.min.css',
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
			get_stylesheet_directory_uri() . '/library/js/white-map.min.js',
			array( 'jquery' ),
			'',
			true // in footer ?
		);

		wp_localize_script(
			'whitemap',							// handle
			'whitemap',							// name
			array(								// data
				'locations' => whitemap_json_location_feed(),
			)
		);

	}
}

/*********************
THEME SUPPORT
*********************/

function whitemap_theme_support() {
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size(125, 125, true);
	add_theme_support( 'custom-background',
		array(
			'default-image' => '',    // background image default
			'default-color' => '',    // background color default (dont add the #)
			'wp-head-callback' => '_custom_background_cb',
			'admin-head-callback' => '',
			'admin-preview-callback' => ''
			)
		);
	add_theme_support('automatic-feed-links');
	add_theme_support( 'menus' );
	register_nav_menus(
		array(
			'main-nav' => __( 'The Main Menu', 'whitemap' ),   // main nav in header
			'footer-links' => __( 'Footer Links', 'whitemap' ) // secondary nav in footer
			)
		);
}

/*********************
RELATED POSTS FUNCTION
*********************/

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
	} /* end whitemap related posts function */

/*********************
PAGE NAVI
*********************/

// Numeric Page Navi (built into the theme by default)
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

/*********************
RANDOM CLEANUP ITEMS
*********************/

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function whitemap_filter_ptags_on_images($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function whitemap_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '...  <a class="excerpt-read-more" href="'. get_permalink($post->ID) . '" title="'. __( 'Read ', 'whitemap' ) . get_the_title($post->ID).'">'. __( 'Read more &raquo;', 'whitemap' ) .'</a>';
}

/************* LOCATION SUPPORT ****************/
require_once( 'library/post-type-location.php' );

/************* THEME OPTIONS **********************/
require_once( 'library/theme-options.php' );

/************* THEME FILTERS AND HOOKS **********************/
function whitemap_ahoy() {
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
add_action( 'after_setup_theme', 'whitemap_ahoy' );


/************* OEMBED SIZE OPTIONS *************/

if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
add_image_size( 'whitemap-thumb-600', 600, 150, true );
add_image_size( 'whitemap-thumb-300', 300, 100, true );

/*
to add more sizes, simply copy a line from above
and change the dimensions & name. As long as you
upload a "featured image" as large as the biggest
set width or height, all the other sizes will be
auto-cropped.

To call a different size, simply change the text
inside the thumbnail function.

For example, to call the 300 x 300 sized image,
we would use the function:
<?php the_post_thumbnail( 'whitemap-thumb-300' ); ?>
for the 600 x 100 image:
<?php the_post_thumbnail( 'whitemap-thumb-600' ); ?>

You can change the names and dimensions to whatever
you like. Enjoy!
*/

add_filter( 'image_size_names_choose', 'whitemap_custom_image_sizes' );

function whitemap_custom_image_sizes( $sizes ) {
	return array_merge( $sizes, array(
		'whitemap-thumb-600' => __('600px by 150px'),
		'whitemap-thumb-300' => __('300px by 100px'),
		) );
}

/*
The function above adds the ability to use the dropdown menu to select
the new images sizes you have just created from within the media manager
when you add media to your content blocks. If you add more image sizes,
duplicate one of the lines in the array and name it according to your
new image size.
*/

/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function whitemap_register_sidebars() {
	register_sidebar(array(
		'id' => 'sidebar1',
		'name' => __( 'Sidebar 1', 'whitemap' ),
		'description' => __( 'The first (primary) sidebar.', 'whitemap' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
		));

}

/********************************************************
JSON LOCATION FEED
********************************************************/

function whitemap_json_location_feed() {

	global $wpdb;

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

	$json = array();

	$json['err'] = false;
	 

	foreach ($posts_query->posts as $post) {

		$ray = array();
		//the_post();
		
		$ray['id']                              = $post->ID;
		$ray['date']                            = $post->post_date;
		$ray['timestamp']                       = strtotime($post->post_date);
		$ray['link']                            = $post->guid;
		$ray['title']                           = $post->post_title;
		$ray['description']                     = $post->content;

		$ray['_mm_location_location_latitude']  = floatval(get_post_meta($post->ID, '_mm_location_location_latitude', true));
		$ray['_mm_location_location_longitude'] = floatval(get_post_meta($post->ID, '_mm_location_location_longitude', true));
		
		
		// New if <= 5 days ago
		$ray['new']                             = date('U') - $ray['timestamp'] <= 60 * 60 * 24 * 5;
		$json['posts'][]                        = $ray;
	}
	wp_reset_postdata();

	return json_encode($json);

}