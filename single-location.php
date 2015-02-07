<?php get_header(); ?>
<div id="content">
	<div id="inner-content" class="wrap cf">
		<div id="main" class="m-all t-2of3 d-5of7 cf" role="main">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">
				<header class="article-header">
					<h1 class="single-title custom-post-type-title"><?php the_title(); ?></h1>
					<address class="address">
						<span class="street">
							<?php echo get_post_meta(get_the_id(), 'whitemap_street_address', true); ?>
						</span>
						<span class="divider"></span>
						<span class="postal">
							<?php echo get_post_meta(get_the_id(), 'whitemap_postal-code', true); ?>
						</span>
						<span class="divider"></span>
						<span class="city">
							<?php echo get_post_meta(get_the_id(), 'whitemap_city', true); ?>
						</span>
					</address>

					<?php if( whitemap_has_ratings(get_the_id()) ) { ?>
						<?php
							$average_rating = whitemap_get_rating_average(get_the_id());
							$ratings_amount = whitemap_get_rating_count(get_the_id());
						?>
						<div class="rating rating-display" data-rating="<?php echo $average_rating; ?>">
							<div class="stars">
								<?php for ($k = 0; $k < $average_rating; $k += 1) { ?>
									<div class="star active"><?php echo $k+1; ?></div>
								<?php }; ?>
							</div>
						</div>
						<?php echo $ratings_amount; ?> <?php ( $ratings_amount == 1 ? _e('rating', 'whitemap') :  _e('ratings', 'whitemap') ); ?>
					<?php } ?>

				</header>

				<div id="wmap">
				</div>

				<section class="entry-content cf">
					<?php the_content(); ?>
					<?php comments_template(); ?>
				</section>

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
	</div>
</div>
<?php get_footer(); ?>