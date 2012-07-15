<?php
/**
 * Template Name: CP No Sidebar
 * Description: A template for the CP that has no sidebars.
 */

get_header(); ?>
<div id="primary" >
				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>
</div>
<?php include (TEMPLATEPATH . '/cp-footer.php'); ?>
