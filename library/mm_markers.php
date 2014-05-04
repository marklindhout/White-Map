<?php
define('WP_USE_THEMES', false);
require('../../../../wp-config.php');

$wp->init();
$wp->parse_request();
$wp->query_posts();
$wp->register_globals();
 
// use get_posts because it is actually outside of the loop.
$posts = get_posts(
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
 
if ($posts) {
	foreach ($posts as $post) {
		$ray = array();
		the_post();
		
		$ray['id'] = $post->ID;
		$ray['date'] = $post->post_date;
		$ray['timestamp'] = strtotime($post->post_date);
		$ray['link'] = $post->guid;
		$ray['title'] = $post->post_title;
		
		$ray['_mm_location_description'] = get_post_meta($post->ID, '_mm_location_description', true);
		$ray['_mm_location_telephone_number'] = get_post_meta($post->ID, '_mm_location_telephone_number', true);
		$ray['_mm_location_website_url'] = get_post_meta($post->ID, '_mm_location_website_url', true);
		$ray['_mm_location_location_latitude'] = floatval(get_post_meta($post->ID, '_mm_location_location_latitude', true));
		$ray['_mm_location_location_longitude'] = floatval(get_post_meta($post->ID, '_mm_location_location_longitude', true));
		
		$terms = wp_get_post_terms($post->ID, 'location-type');
		$term  = $terms[0];
		$ray['_mm_location_location_type'] = $term->slug;
		
		$ray['_mm_location_email'] = antispambot(get_post_meta($post->ID, '_mm_location_email', true), 0);
		
		$img = wp_get_attachment_image_src( get_post_meta($post->ID, '_mm_location_logo_id', true), 'thumbnail');
		$ray['_mm_location_logo'] = $img[0];
		$ray['_mm_location_photos'] = get_post_meta($post->ID, '_mm_location_photos', true);

		// New if <= 5 days ago
		$ray['new'] = date('U') - $ray['timestamp'] <= 60 * 60 * 24 * 5;
		$json['posts'][] = $ray;
	}
}

header('Content-type: application/json;');
echo json_encode($json);
