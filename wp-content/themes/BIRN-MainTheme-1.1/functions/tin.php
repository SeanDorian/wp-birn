<?php
 include("../../../../wp-blog-header.php");
if($_POST['add'] == true) {
	  $my_post = array(
	     'post_title' => $_POST['title'],
	     'post_content' => $_POST['post'],
	     'post_status' => 'publish',
	     'post_category' => array(10,10)
	  );
	  wp_insert_post( $my_post );
}
if($_POST['delete'] == true) {
	wp_delete_post($_POST['id']);
}
if($_POST['save'] == true) {
	$my_post = array(
	     'post_title' => $_POST['title'],
	     'post_content' => $_POST['content'],
	     'post_status' => 'publish',
	     'post_category' => array(10,10),
		 'ID' => $_POST['id']
	  );
	  wp_insert_post( $my_post );
}
$args = array( 'numberposts' => 50, 'order'=> 'DESC', 'orderby' => 'post_date', 'category'=> 10 );
$postslist = get_posts( $args );
$id = $post->ID;
foreach ($postslist as $post) :  setup_postdata($post); ?> 
	<div id="tin-news-post" class="post-<?php echo $id?>">
		<div id="post-title-<?php echo $id?>" class="BIRN-post-title"><?php the_title(); ?></div>   
		<div id="post-content-<?php echo $id?>" class="BIRN-post-content"><?php the_content(); ?></div>
		<?php if($permission >= 3 || $_POST['add'] || $_POST['delete']) {?>
			<div id="post-options-<?php echo $id?>" class="post-options">
				<button type="button" class="big" onclick="tin('edit', <?php echo $id?>)">Edit</button>
				<button type="button" class="big" onclick="tin('delete', <?php echo $id?>)">Delete</button>
				<button type="button" class="big hide" onclick="tin('save', <?php echo $id?>)">Save</button>											
				<button type="button" class="big hide" onclick="tin('cancel', <?php echo $id?>)">Cancel</button>
			</div><?php
		}?>
	</div><?php
endforeach; ?>