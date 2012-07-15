<?php include("../../../../wp-blog-header.php");
$id = $_GET['user'];
$uid = get_current_user_id();
if($_POST['add'] == true) {
	$time = date("Y/m/d");
	$timeF = date("F d, Y");
	$sid = $_POST['showID'];
	$pt = $_POST['title'];
	$pp = $_POST['post'];
	$addPost = $wpdb->insert(
	 	'show_posts',
		array(
		'User_ID' => $uid,
		'Show_ID' => $sid,
		'Title' => $pt,
		'Content' => $pp,
		'Date' => $time,
		'Time_Formatted' => $timeF
		)
	);
	echo $wpdb->insert_id;
};
if($_POST['delete'] == true) {
	$pid = $_POST['postID'];
	$wpdb->query( 
		"
         DELETE FROM show_posts
		 WHERE ID = $pid
		"
	);
};
if($_POST['save'] == true) {
	$title = $_POST['title'];
	$content = $_POST['content'];
	$postID = $_POST['id'];
	$wpdb->update(
	'show_posts',
	array(
		'Title' => $title,
		'Content' => $content,
	),
	array(
		'ID' => $postID
	)
	);
}
?>