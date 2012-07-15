<?php include("../../../../wp-blog-header.php");
if ($_POST['user']) {
	$id = $_POST['user'];
	
}
$action = $_POST['action'];
$type = $_POST['type'];
$uid = get_current_user_id();
//add/edit strikes. Fields include strike type, severity, date of offense, comment. This needs to be cleaned up with smoother transitions
//Switch statements in php? Would look nicer.
if ($action == 'delete') {
	wp_delete_post($type);
	$action = 'view';
	$type = 'unresolved';
};
if ($action == 'resolve') {
	  $update_post = array('ID' => $type, 'post_category' => array(15,15));
	  wp_update_post( $update_post );
	$action = 'view';
	$type = 'unresolved';
	
};
if ($action == 'unresolve') {
	  $update_post = array('ID' => $type, 'post_category' => array(13,13));
	  wp_update_post( $update_post );
	$action = 'view';
	$type = 'resolved';
	
};
if ($action == 'add') {
	$my_post = array(
 		'post_title' => $_POST['sType'],
 		'post_content' => $_POST['sComment'],
		'post_date' => $_POST['sDate'],
 		'post_status' => 'publish',
 		'post_category' => array(13,13)
	);
	$post_id = wp_insert_post( $my_post );
	add_post_meta($post_id, 'Severity', $_POST['sSever']);
	add_post_meta($post_id, 'Strikee', $id);
	add_post_meta($post_id, 'Striker', $uid);
	$action = 'view';
	$type = 'unresolved';
};
?><div id="profile-page-content">
	<style>
		#view-user {
			height: 500px;
		}
	</style>
<?php if ($action == 'new') {?>
	<script>
	jQuery('input, textarea').click(function() {
		jQuery(this).select();
	})
	jQuery('#strike-date').datepicker({
		dateFormat: "yy-m-d"
	});
	var strikeTypes = [
		"Missed Meeting", 
		"Missed Show", 
		"Late for Show", 
		"Swear Word", 
		"Unapproved Guest", 
		"Lingering Strike", 
		"No Playlist", 
		"Other"
		]
	for (i=0;i<strikeTypes.length;i++) {
		jQuery("#strike-type").append('<option value="'+strikeTypes[i]+'">'+strikeTypes[i]+"</option>")
	}
	</script>
	Type: <select id="strike-type"></select><br>
	<input type="text" id="strike-severity" value="Severity">
	<input type="text" id="strike-date" value="Date">
	<textarea id="strike-comment" rows="5">Comments</textarea>
	<button type="button" class="big" style="float:right" onclick="strikes('<?php echo $id; ?>', 'add')">Add Strike</button>
<?php
} else if ($action == 'edit') {
	echo 'this is edit';
} else if ($action == 'view' || !$action) {
	if ($type == 'resolved') {
		$type = 15;
	} else if ($type == 'unresolved' || !$type) {
		$type = 13;
	}
	$arg = array(
		'category' => $type,
		'meta_key' => 'Strikee',
		'meta_value' => $id
	);
	$lastposts = get_posts($arg);
	foreach($lastposts as $post) : setup_postdata($post); ?>
		<div class="white-box">
			<span style="font-size: 1.4em"><?php echo $post->post_title ?></span>
			<span style="font-style:italic">Severity: <?php echo get_post_meta($post->ID, 'Severity', true) ?></span>
			<span id="post-date-<?php echo $post->ID; ?>" style="float: right"><?php echo $post->post_date ?></span><br>
		<?php
			$strikerID = get_post_meta($post->ID, 'Striker', true);
			$strikeeID = get_post_meta($post->ID, 'Strikee', true);
			$striker = get_userdata($strikerID);
			echo 'By <a href="/cp/profiles/?user='.$strikerID.'">'.$striker->first_name.' '.$striker->last_name.'</a>'; ?><br>
		<div style="border-top: 2px dashed #999; padding-top:5px">
			<?php echo $post->post_content ?>
		</div>			
		<div style="float:right">
			<?php
			$delete = "editStrikes(".$strikeeID.", 'delete', ".$post->ID.")";	
			if ($type == 15) {
				$onclick = "editStrikes(".$strikeeID.", 'unresolve', ".$post->ID.")";	
				$function = 'Unresolve';
			} else if ($type == 13) {
				$onclick = "editStrikes(".$strikeeID.", 'resolve', ".$post->ID.")";	
				$function = 'Resolve';
			}
			?>
			<button onclick="<?php echo $delete; ?>" type="button">Delete</button>
			<button onclick="<?php echo $onclick; ?>" type="button"><?php echo $function; ?></button>
		</div>
		</div>
		<script>//Super messy, you need to clean this
			jQuery('#post-date-'+<?php echo $post->ID; ?>).text(jQuery('#post-date-'+<?php echo $post->ID; ?>).text().slice(0,-8))
		</script>
	<?php endforeach;
} else {
}
?>
</div>