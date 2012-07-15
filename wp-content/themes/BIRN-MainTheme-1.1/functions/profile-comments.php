<?php
/*What needs fixing: Users should only be able to edit their own comments, only permission 3 can delete and edit all. Make it look nicer*/
include("../../../../wp-blog-header.php");
$id = $_GET['user'];
$uid = get_current_user_id();
$action = $_POST['action'];
$postID = $_POST['postID'];
if ($action == 'add') {
	$my_post = array(
 		'post_title' => 'Comment: '.$postID,
 		'post_content' => $_POST['comment'],
		//'post_date' => $_POST['sDate'],
 		'post_status' => 'publish',
 		'post_category' => array(14,14)
	);
	$post_id = wp_insert_post( $my_post );
	add_post_meta($post_id, 'Author', $uid);
	add_post_meta($post_id, 'Comment_User', $postID);
};
if ($action == 'delete') {
	wp_delete_post($postID);
};
?>
<style>
	#view-user {
		height: 500px;
	}
</style>
<div id='page-content'>
	<div id="page-header">
		<button type="button" class="big" style="float:right" onclick="editComments(<?php echo $id; ?>, 'new')">Add Comment</button>
	</div>
<?php if ($action == 'new') {?>
	<textarea id="user-comment" rows="5">Comments</textarea>
	<button type="button" class="big" onclick="editComments(<?php echo $postID; ?>,'add')">Add Comment</button>
	<?php
} else {?>
	<div id="profile-comments">
		<?php $arg = array(
			'category' => 14,
			'meta_key' => 'Comment_User',
			'meta_value' => $id
		);
		$lastposts = get_posts($arg);
		foreach($lastposts as $post) : setup_postdata($post); ?>
			<div class="white-box">
				<span id="post-date-<?php echo $post->ID; ?>" style="float: right"><?php echo $post->post_date; ?></span>
			<?php
				$authorID = get_post_meta($post->ID, 'Author', true);
				$userID = get_post_meta($post->ID, 'Comment_User', true);
				$author = get_userdata($authorID);
				echo 'By <a href="/cp/profiles/?user='.$authorID.'">'.$author->first_name.' '.$author->last_name.'</a>'; ?><br>
			<div style="border-top: 2px dashed #999; padding-top:5px">
				<?php echo $post->post_content ?>
			</div>			
			<div style="float:right">
				<button onclick="editComments(<?php echo $post->ID; ?>, 'delete')" type="button">Delete</button>
			</div>
			</div>
			<script>//Super messy, you need to clean this
				jQuery('#post-date-'+<?php echo $post->ID; ?>).text(jQuery('#post-date-'+<?php echo $post->ID; ?>).text().slice(0,-8))
			</script>
		<?php endforeach;?>
	</div><?php
}?>
</div>