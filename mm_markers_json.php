<?php
define('WP_USE_THEMES', false);
require('../../../wp-config.php');

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

		$ago = $ray['date'];
		$ray['ago'] = ($ago == "") ? false : $ago;
		$ray['contents'] = strip_tags($post->post_content);
		$ray['link'] = $post->guid;
		$ray['title'] = $post->post_title;
		$ray['link'] = '<a href="'.$post->guid.'">'.$post->post_title.'</a>';

		 // New if <= 5 days ago
		$ray['isNew'] = date('U') - $ray['timestamp'] <= 60 * 60 * 24 * 5;
		$json['posts'][] = $ray;
	}
}

header('Content-type: application/json;');
echo json_encode($json);