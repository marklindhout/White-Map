<?php get_header(); ?>
<div id="content">
	<div id="inner-content" class="wrap cf">
		<div id="main" class="m-all t-2of3 d-5of7 cf" role="main">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">
				<header class="article-header">
					<h1 class="single-title custom-post-type-title"><?php the_title(); ?></h1>
				</header>
				<div class="location-map"></div>
				<section class="entry-content cf">
					<?php the_content(); ?>
					<div class="location-info">
						<pre style="padding: 0 1em;"><code><?php
						$custom_fields = get_post_custom();

						foreach ( $custom_fields as $field_key => $field_values ) {
							foreach ( $field_values as $key => $value ) {
								echo '<div><span style="opacity: 0.7">' .  $field_key . '</span>: <span>' . $value . '</span></div>';
							}
						}
						?></code></pre>
					</div>
				</section>
				<footer class="article-footer">
				</footer>
				<?php comments_template(); ?>

			</article>
			<?php endwhile; ?>
		<?php else : ?>
			<article id="post-not-found" class="hentry cf">
				<header class="article-header">
					<h1><?php _e( 'Oops, Post Not Found!', 'whitemap' ); ?></h1>
				</header>
				<section class="entry-content">
					<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'whitemap' ); ?></p>
				</section>
				<footer class="article-footer">
					<p><?php _e( 'This is the error message in the single-location.php template.', 'whitemap' ); ?></p>
				</footer>
			</article>
		<?php endif; ?>
		</div>

		<?php get_sidebar(); ?>
	</div>
</div>
<?php get_footer(); ?>