<?php
/**
 * Template Name: Main Sidebar
 * Description: Main Site Template with Sidebars
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

include (TEMPLATEPATH . '/main-header.php'); ?>
<div id="main-site-section" class="left-sidebar" style="float:left;width:20%;margin-right:5px">
	<?php dynamic_sidebar( 'home-left' ); ?>
</div>

		<div id="main-site-section" class="center-sidebar" style="float:left;width:79%;">
				<div id="widget-section" style="padding: 0 10px 10px 10px">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'content', 'page' ); ?>
						<?php comments_template( '', true ); ?>
					<?php endwhile; // end of the loop. ?>
				</div>
		</div>

		<div id="main-site-section" class="right-sidebar" style="float:right;width:20%;margin-left:5px">
			<?php dynamic_sidebar( 'home-right' ); ?>
		</div>
<?php get_footer(); ?>