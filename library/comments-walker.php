<?php

/** COMMENTS WALKER */
class whitemap_walker_comment extends Walker_Comment {

	// init classwide variables
	var $tree_type = 'comment';
	var $db_fields = array(
		'parent' => 'comment_parent',
		'id'     => 'comment_ID',
	);

	// Start comments section
	function __construct() {
		?>
			<h3 class="comments-title"><?php _e('Reviews', 'whitemap'); ?></h3>
			<section class="comment-list">
		<?php
	}

	// Starts the children list
	function start_lvl( &$output, $depth=0, $args=array() ) {      
		$GLOBALS['comment_depth'] = $depth + 1;
		echo '<div class="children">';
	}

	// Ends the children list
	function end_lvl( &$output, $depth=0, $args=array() ) {
		$GLOBALS['comment_depth'] = $depth + 1;
		echo '</div>';
	}

	// Start element
	function start_el( &$output, $comment, $depth=0, $args=array(), $id=0 ) {
		$depth++;
		$GLOBALS['comment_depth'] = $depth;
		$GLOBALS['comment'] = $comment;
		$parent_class = ( empty( $args['has_children'] ) ? '' : 'parent' );
	?>
		<article id="comment-<?php comment_ID(); ?>" <?php comment_class('cf'); ?> itemscope itemtype="http://schema.org/Comment">
			<header class="comment-author vcard" role="complementary">
				<figure class="gravatar">
					<?php echo get_avatar( $comment, 40, '[default gravatar URL]', 'Author’s gravatar' ); ?>
				</figure>

				<cite class="fn"><?php echo get_comment_author_link(); ?></cite>

				<time itemprop="datePublished" datetime="<?php echo comment_time('c'); ?>">
					<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
						 <?php echo human_time_diff( get_comment_time('U'), current_time('timestamp') ) . ' ' . __('ago', 'whitemap'); ?> 
					</a>
				</time>

				<?php if( $commentrating = get_comment_meta( get_comment_ID(), 'rating', true ) ) { ?>
					<div class="rating rating-display" data-rating="<?php echo $commentrating; ?>">
						<div class="stars">
							<?php for ($k = 0; $k < $commentrating; $k += 1) { ?>
								<div class="star active"><?php echo $k+1; ?></div>
							<?php }; ?>
						</div>
					</div>
				<?php } ?>
			</header>
		<?php if ($comment->comment_approved == '0') : ?>
			<div class="message message-warning">
				<p><?php _e( 'Your comment is awaiting moderation.', 'whitemap' ) ?></p>
			</div>
		<?php endif; ?>
			<section class="comment_content cf" itemprop="text">
				<?php comment_text() ?>
			</section>
			<?php echo edit_comment_link( __('Edit this rating', 'whitemap'), '<p>', '</p>'); ?>
	
	<?php
	}

	// End element
	function end_el(&$output, $comment, $depth = 0, $args = array() ) {
		?>
			</article>
		<?php
	}

	// End comments section
	function __destruct() {
		?>
			</section>
		<?php
	}

}