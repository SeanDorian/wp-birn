<?php
/**
 * Template Name: Webcam
 * Description: A Page Template that adds a sidebar to pages
 */

include (TEMPLATEPATH . '/webcam-header.php'); ?>

<div id="main">

<div id="widget-section">
				<div class="home-header" style="display: block">
				<header id="branding" role="banner">

				<div id="stream-page-cam">
					<a href="/webcam-1">Broadcast</a> | 
					<a href="/webcam-2">Live Room</a>
				</div>
					
				</header><!-- #branding -->
				</div>
				<div class="widget-content" style="padding: 0 5px 0px 5px; text-align: center">
				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>
				</div>
</div>
<?php get_footer()?>
