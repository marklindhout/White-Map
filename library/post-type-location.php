<?php
add_action( 'after_switch_theme', 'whitemap_flush_rewrite_rules' );

// Flush your rewrite rules
function whitemap_flush_rewrite_rules() {
	flush_rewrite_rules();
}

// let's create the function for the custom type
function post_type_location() { 
	// creating (registering) the custom type 
	register_post_type( 'location',
		array( 'labels' => array(
			'name' => __( 'Locations', 'whitemap' ), /* This is the Title of the Group */
			'singular_name' => __( 'Location', 'whitemap' ), /* This is the individual type */
			'all_items' => __( 'All Locations', 'whitemap' ), /* the all items menu item */
			'add_new' => __( 'Add New', 'whitemap' ), /* The add new menu item */
			'add_new_item' => __( 'Add New Location', 'whitemap' ), /* Add New Display Title */
			'edit' => __( 'Edit', 'whitemap' ), /* Edit Dialog */
			'edit_item' => __( 'Edit Location', 'whitemap' ), /* Edit Display Title */
			'new_item' => __( 'New Location', 'whitemap' ), /* New Display Title */
			'view_item' => __( 'View Location', 'whitemap' ), /* View Display Title */
			'search_items' => __( 'Search Location', 'whitemap' ), /* Search Custom Type Title */ 
			'not_found' =>  __( 'Nothing found in the Database.', 'whitemap' ), /* This displays if there are no entries yet */ 
			'not_found_in_trash' => __( 'Nothing found in Trash', 'whitemap' ), /* This displays if there is nothing in the trash */
			'parent_item_colon' => ''
			), /* end of arrays */
			'description' => __( 'A place with address, coordinates and extra info', 'whitemap' ), /* Custom Type Description */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 8, /* this is what order you want it to appear in on the left hand side menu */ 
			'menu_icon' => get_stylesheet_directory_uri() . '/library/img/post-type-icon-location.png', /* the icon for the custom post type menu */
			'rewrite'	=> array( 'slug' => 'location', 'with_front' => false ), /* you can specify its url slug */
			'has_archive' => 'location', /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'author', 'thumbnail', 'comments', 'revisions', 'sticky')
		)
	);
}
add_action( 'init', 'post_type_location');

/***************************************************************************************
CUSTOM TYPE TAXONOMY for the LOCATION content type
***************************************************************************************/

register_taxonomy( 'location-type', 
	array('location'),
	array('hierarchical' => false,
		'labels' => array(
			'name' => __( 'Location Types', 'whitemap' ),
			'singular_name' => __( 'Location Type', 'whitemap' ),
			'search_items' =>  __( 'Search Location Type', 'whitemap' ),
			'all_items' => __( 'All Location Types', 'whitemap' ),
			'parent_item' => __( 'Parent Location Type', 'whitemap' ),
			'parent_item_colon' => __( 'Parent Location Type:', 'whitemap' ),
			'edit_item' => __( 'Edit Location Type', 'whitemap' ),
			'update_item' => __( 'Update Location Type', 'whitemap' ),
			'add_new_item' => __( 'Add New Location Type', 'whitemap' ),
			'new_item_name' => __( 'New Location Type Name', 'whitemap' )
		),
		'show_admin_column' => true, 
		'public' => true, // If the taxonomy should be publicly queryable. 
		'show_ui' => true, // Whether to generate a default UI for managing this taxonomy. 
		'show_in_nav_menus' => false, // Makes this taxonomy available for selection in navigation menus. 
		'query_var' => true,
		'rewrite' => array( 'slug' => 'location-type' ),
	)
);

// remove the WordPress standard meta box from the 'location' edit screen.
// remove the WordPress standard featured image box from the 'location' edit screen.
function location_remove_meta_boxes() {
	remove_meta_box( 'tagsdiv-location-type' , 'location' , 'side' );
	remove_meta_box( 'postimagediv' , 'location' , 'normal' );
}
add_action( 'admin_menu' , 'location_remove_meta_boxes' );


/***************************************************************************************
CUSTOM META BOXES for the LOCATION content type
***************************************************************************************/

function cmb_initialize_cmb_meta_boxes() {
	if ( ! class_exists( 'cmb_Meta_Box' ) ) {
		require_once 'php/cmb/init.php';
		require_once 'php/cmb/field_map/cmb-field-map.php';
	}
}
add_action( 'init', 'cmb_initialize_cmb_meta_boxes', 9999 );


function whitemap_meta_boxes( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_mm_location_';

	$meta_boxes['location_metabox'] = array(
		'id'         => 'location_metabox',
		'title'      => __( 'Location', 'whitemap' ),
		'pages'      => array( 'location' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'cmb_styles' => false, // Enqueue the CMB stylesheet on the frontend

		'fields'     => array(
			array(
				'name'		=> __( 'Description', 'whitemap' ),
				'id'		=> $prefix . 'description',
				'type'		=> 'text',
			),
			array(
				'name' => __( 'Telephone Number', 'cmb' ),
				'id'   => $prefix . 'telephone_number',
				'type' => 'text_small',
			),
			array(
				'name' => __( 'Website URL', 'cmb' ),
				'id'   => $prefix . 'website_url',
				'type' => 'text_url',
				'protocols' => array('http', 'https'),
			),
			array(
				'name' => __( 'Email Address', 'cmb' ),
				'id'   => $prefix . 'email',
				'type' => 'text_email',
			),
			array(
				'name'		=> __( 'Street Address', 'whitemap' ),
				'id'		=> $prefix . 'street_address',
				'type'		=> 'text',
			),
			array(
				'name'		=> __( 'Postal Code', 'whitemap' ),
				'id'		=> $prefix . 'postal-code',
				'type'		=> 'text',
			),
			array(
				'name'		=> __( 'City', 'whitemap' ),
				'id'		=> $prefix . 'city',
				'type'		=> 'text',
			),
			array(
				'name'		=> 'Coordinates',
				'desc'		=> 'Drag the marker to set the exact location',
				'id'		=> $prefix . 'location',
				'type'		=> 'pw_map',
				'sanitization_cb' => 'pw_map_sanitise',
			),
			array(
				'name'		=> __( 'Location Type', 'whitemap' ),
				'id'	=> $prefix . 'location_type',
				'type'		=> 'taxonomy_radio',
				'taxonomy'	=> 'location-type',
				'inline'	=> true,
			),
			array(
				'name'		=> __( 'Logo', 'whitemap' ),
				'id'		=> $prefix . 'logo',
				'type'		=> 'file',
			),
			array(
				'name'		=> __( 'Photos', 'whitemap' ),
				'id'		=> $prefix . 'photos',
				'type'		=> 'file_list',
				'allow' => array( 'attachment' ),
				'preview_size' => array( 128, 128 ),
			),
		),
	);

	return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'whitemap_meta_boxes' );