<?php
/**
 * Template Name: Home
 * Description: Home Page of the Site
 */

include (TEMPLATEPATH . '/main-header.php'); ?>
	<div id="main-site-section" class="left-sidebar" style="float:left;width:20%;margin-right:5px">
		<?php dynamic_sidebar( 'home-left' ); ?>
	</div>

	<div id="main-site-section" class="center-sidebar" style="float:left;width:79%;">
		<div id="featured" >  
		    <ul class="ui-tabs-nav">  
		        <li class="ui-tabs-nav-item ui-tabs-selected" id="nav-fragment-1"><a href="#fragment-1"><span>Now Playing</span></a></li>  
		        <li class="ui-tabs-nav-item" id="nav-fragment-2"><a href="#fragment-2"><span>News</span></a></li>  
		        <li class="ui-tabs-nav-item" id="nav-fragment-3"><a href="#fragment-3"><span>Events</span></a></li>  
		    </ul>  
		    <!-- First Content -->  
		    <div id="fragment-1" class="ui-tabs-panel" style=""> 
		        <img src="images/image1.jpg" alt="" />  
		        <h2><a href="#" >Name of Show That's Playing</a></h2>  
		        <p>Show description, max 140 characters, will also put up show image...<a href="#" >read more</a></p>  
		    </div>  
		    <!-- Second Content -->  
		    <div id="fragment-2" class="ui-tabs-panel ui-tabs-hide" style="">  
				<?php
				$args = array( 'numberposts' => 1, 'order'=> 'DESC', 'orderby' => 'post_date', 'category'=> 8 );
				$postslist = get_posts( $args );
				foreach ($postslist as $post) :  setup_postdata($post); ?> 											
						<div class="featured-post-title"><?php the_title(); ?></div>   
						<?php the_content(); ?>						
				<?php endforeach; ?>
		    </div>  
		    <!-- Third Content -->  
		    <div id="fragment-3" class="ui-tabs-panel ui-tabs-hide" style="">  
		       		<?php
					$args = array( 'numberposts' => 1, 'order'=> 'DESC', 'orderby' => 'post_date', 'category'=> 11 );
					$postslist = get_posts( $args );
					foreach ($postslist as $post) :  setup_postdata($post); ?> 
						<div>												
							<div class="featured-post-title"><?php the_title(); ?></div>   
							<?php the_content(); ?>						
						</div>
					<?php endforeach; ?>  
		    </div>  
		</div>
	</div>

	<div id="main-site-section" class="right-sidebar" style="float:right;width:20%;margin-left:5px">
		<?php dynamic_sidebar( 'home-right' ); ?>
	</div>

<?php get_footer(); ?>