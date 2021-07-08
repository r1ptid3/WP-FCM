<?php
/**
 * The main template file
 *
 * @package Sample
 *
 * @since   1.0.0
 */

// Disable direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

get_header();

if ( have_posts() ) :

?>

	<section class="posts-wrapper">

		<?php

		while ( have_posts() ) :
			the_post();

			?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'r1-post' ); ?>>

					<!-- post title -->
					<h3 class="post-title">
						<a href="<?php the_permalink(); ?>">
							<?php the_title(); ?>
						</a>
					</h3>

					<!-- post content -->
					<div class="post-content">
						<?php the_content(); ?>
					</div>

				</article>

			<?php

		endwhile;

		?>

	</section>

<?php

else :

	get_template_part( 'template-parts/content', 'none' );

endif;

get_footer();
