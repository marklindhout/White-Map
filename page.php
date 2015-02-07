<?php get_header(); ?>

<div id="content">
	<div id="inner-content" class="wrap cf">
		<div id="main" role="main">
		
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article">

					<header class="article-header">
						<h1 class="single-title custom-post-type-title"><?php the_title(); ?></h1>
					</header>

					<section class="entry-content cf">
						<?php the_content(); ?>
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