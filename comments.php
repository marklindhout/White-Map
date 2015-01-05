<?php

// don't load it if you can't comment
if ( post_password_required() ) {
	return;
}

if ( have_comments() ) {
	wp_list_comments( array(
		'walker' => new whitemap_walker_comment,
	) );
}

if ( is_user_logged_in() ) {
	whitemap_comment_form();
}