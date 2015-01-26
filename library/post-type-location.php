<?php

// Flush your rewrite rules
function whitemap_flush_rewrite_rules() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'whitemap_flush_rewrite_rules' );



// let's create the function for the custom type
function post_type_location() { 
	// creating (registering) the custom type 
	register_post_type( 'location',
		array(
			'labels' => array(
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
			),
			'description' => __( 'A place with address, coordinates and extra info', 'whitemap' ), /* Custom Type Description */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 8, /* this is what order you want it to appear in on the left hand side menu */ 
			'menu_icon' => get_stylesheet_directory_uri() . '/library/img/admin_icon.png', /* the icon for the custom post type menu */
			'rewrite'	=> array( 'slug' => 'location', 'with_front' => false ), /* you can specify its url slug */
			'has_archive' => 'location', /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array( 'title', 'editor', 'author', 'revisions', 'comments'),
			'taxonomies' => array('post_tag'),
		)
	);
}
add_action( 'init', 'post_type_location');
