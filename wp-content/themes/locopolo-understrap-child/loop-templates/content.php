<?php
/**
 * Post rendering content according to caller of get_template_part
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class( 'mb-5' ); ?> id="post-<?php the_ID(); ?>">

	<div class="row align-items-center">

		<div class="col-sm-6 col-md-4 col-lg-3 text-center">

			<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>

		</div>
		
		<div class="col-sm-6 col-md-8 col-lg-9">

			<header class="entry-header">

				<?php
				the_title(
					sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
					'</a></h2>'
				);
				?>

				<?php if ( 'post' === get_post_type() ) : ?>

					<div class="entry-meta">
						<?php understrap_posted_on(); ?>
					</div><!-- .entry-meta -->

				<?php endif; ?>

			</header><!-- .entry-header -->

			<div class="entry-content">

				<?php
				the_excerpt();
				understrap_link_pages();
				?>

			</div><!-- .entry-content -->

			<footer class="entry-footer">

				<?php understrap_entry_footer(); ?>

			</footer><!-- .entry-footer -->

		</div><!-- .col-md-6 -->

	</div><!-- .row -->

</article><!-- #post-<?php the_ID(); ?> -->
