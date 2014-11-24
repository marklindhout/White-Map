<?php

function pw_map_field( $field, $meta ) {
	wp_enqueue_script( 'pw_google_maps_api', '//maps.googleapis.com/maps/api/js?sensor=false&libraries=places', array(), null );
	wp_enqueue_script( 'pw_google_maps_init', get_stylesheet_directory_uri() . '/library/php/cmb/field_map/js/script.js', array( 'pw_google_maps_api' ), null );
	wp_enqueue_style( 'pw_google_maps_css', get_stylesheet_directory_uri() . '/library/php/cmb/field_map/css/style.css', array(), null );

	echo '<input type="text" class="map-search" id="' . $field['id'] . '" />';
	echo '<div class="map"></div>';
	echo '<input type="hidden" class="latitude" name="' . $field['id'] . '[latitude]" value="' . ( isset( $meta['latitude'] ) ? $meta['latitude'] : '' ) . '" />';
	echo '<input type="hidden" class="longitude" name="' . $field['id'] . '[longitude]" value="' . ( isset( $meta['longitude'] ) ? $meta['longitude'] : '' ) . '" />';

	if ( ! empty( $field['desc'] ) ) echo '<p class="cmb_metabox_description">' . $field['desc'] . '</p>';
}
add_filter( 'cmb_render_pw_map', 'pw_map_field', 10, 2 );

/**
 * Split latitude/longitude values into two meta fields
 */
function pw_map_sanitise( $meta_value, $field ) {
	$latitude = $meta_value['latitude'];
	$longitude = $meta_value['longitude'];

	if ( ! empty( $latitude ) ) {
		update_post_meta( get_the_ID(), $field['id'] . '_latitude', $latitude );
	}

	if ( ! empty( $longitude ) ) {
		update_post_meta( get_the_ID(), $field['id'] . '_longitude', $longitude );
	}

	return $meta_value;
}