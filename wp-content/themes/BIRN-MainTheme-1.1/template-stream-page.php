<?php
/**
 * Template Name: Stream
 * Description: A Page Template that adds a sidebar to pages
 */

include (TEMPLATEPATH . '/stream-header.php'); ?>

<div id="widget-section">
		<div class="home-header" style="display:block">
		<header id="branding">

		<div id="stream-page-links">
			<a href="/listen/birn-1">BIRN 1</a> | 
			<a href="/listen/birn-2">BIRN 2</a> | 
			<a href="/listen/birn-3">BIRN 3</a> | 
			<a href="/listen/birn-4">BIRN 4</a> | 
			<a href="/listen/birn-5">BIRN 5</a> | 
			<a href="/listen/birn-presents">BIRN Presents</a>
		</div>

		<div id="stream-page-cam">
			Webcams: <a onclick="window.open('/webcam-1','','width=450,height=350,left=800,top=130');">Broadcast</a> | 
			<a onclick="window.open('/webcam-2','','width=450,height=350,left=800,top=130');">Live Room</a>
		</div>

		</header><!-- #branding -->
		</div>
			<div class="widget-content" style="position:relative">
				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

					<?php comments_template( '', true ); ?>

				<?php endwhile; // end of the loop. ?>
			</div>
</div>

<?php get_footer(); ?>