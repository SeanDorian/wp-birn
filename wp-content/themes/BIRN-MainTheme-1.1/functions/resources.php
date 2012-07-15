<?php
 include("../../../../wp-blog-header.php");
	if($_POST['edit'] == true) {
		// Create post object
		$my_post = array(
			'ID' => 352,
			'post_content' => $_POST['content'],
			'post_title' => 'Resources',
			'post_status' => 'publish',
			'post_category' => array(12,12)
		);
		$post = wp_insert_post( $my_post );
	}
	$args = array( 'numberposts' => 1, 'order'=> 'DESC', 'orderby' => 'post_date', 'category'=> 12 );
	$postslist = get_posts( $args );
	foreach ($postslist as $post) :  setup_postdata($post);
?> 
<div>												
	<div class="BIRN-post-title"><?php the_title(); ?></div>
	<div id="resources-content"><?php the_content(); ?></div>
</div>			 
<?php endforeach; ?>