<?php
/**
 * Template Name: Main No Sidebar
 * Description: A Main Site Template with No Sidebars
 *
 */

include (TEMPLATEPATH . '/main-header.php'); ?>

<div id="widget-section" style="padding: 0 10px 10px 10px">
				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>
</div>
<?php get_footer(); ?>