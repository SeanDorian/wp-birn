<?php include("../../../../wp-blog-header.php");
global $current_user, $permission, $roles, $uid;
get_currentuserinfo();
$uid = get_current_user_id(); $s = true;
$roles = $wpdb->get_results(
	"
	SELECT Role_ID
	FROM roles_users
	WHERE User_ID = $uid
	ORDER BY Role_ID ASC
	"
);
foreach ($roles as $role) {
	if ($role->Role_ID == 10) {
		$permission = -1;
		break;
	} else if ($role->Role_ID >= 8) {
		$permission = 0;
		break;
	} else if ($role->Role_ID >= 6) {
		$permission = 1;
		break;	
	} else if ($role->Role_ID >= 4) {
		$permission = 2;
		break;
	} else if ($role->Role_ID >= 1) {
		$permission = 3;
		break;
	}
}
if ($_POST['inactive']) {
	$active = 0;
	$function = 'Activate';
	?><script>
		jQuery('#show-inactive').css('background-color', '#ddd')
		jQuery('#show-active').css('background-color', 'transparent')
	</script><?php
} else {
	$active = 1;
	$function = 'Inactivate';
	?><script>
		jQuery('#show-active').css('background-color', '#ddd')
		jQuery('#show-inactive').css('background-color', 'transparent')
	</script><?php
}
if ($_POST['remove'] == 'all') {
	$showStatus = $wpdb->get_results(
		"
		SELECT *
		FROM shows
		WHERE active = 1
		"
		);
	foreach($showStatus as $s) {
		$wpdb->update(
		'shows',
		array(
			'active' => 0
		),
		array(
			'id' => $s->id
		)
		);
	}
} else if ($_POST['remove']) {
	$remove = $_POST['remove'];
	$showStatus = $wpdb->get_results(
		"
		SELECT *
		FROM shows
		WHERE id = $remove
		"
		);
	foreach($showStatus as $s) {
		$wpdb->update(
		'shows',
		array(
			'active' => 0
		),
		array(
			'id' => $s->id
		)
		);
	}
}
if ($_POST['activate']) {
	$activate = $_POST['activate'];
	$showStatus = $wpdb->get_results(
		"
		SELECT *
		FROM shows
		WHERE id = $activate
		"
		);
	foreach($showStatus as $s) {
		$wpdb->update(
		'shows',
		array(
			'active' => 1
		),
		array(
			'id' => $s->id
		)
		);
	}
}
if ($_POST['action'] == 'addNewShow') {
	$time = date("Y/m/d");
	$show = $wpdb->insert( 
		'shows', 
		array( 
			'title' => $_POST['showName'],
			'created_at' => $time,
			'active' => 1,
			'avatar_file_name' => 'default.jpg',
			'avatar_content_type' => 'image/jpeg',
			'genre' => ' ',
			'description' => ' '
		)
	);
		$showID = $wpdb->insert_id;
		$userList = $_POST['userID'];
		$userCount = count($userList);
		for ($i=0;$i<$userCount; $i++) {
			$userName = $userList[$i];
			$wpdb->insert( 
				'shows_users', 
				array( 
					'show_id' => $showID,
					'user_id' => $userName
				)
			);
		}
}
$ashows = $wpdb->get_results( 
	"
	SELECT * 
	FROM shows
	WHERE active = $active
	ORDER BY title ASC
	"
);
foreach ($ashows as $ashow){
	$id = $ashow->id;?>
	<div id="id-<?php echo $id;?>" class="gray-box show-box">
		<div class="BIRN-post-title"><a target="_blank" href="/shows/?show='<?php echo $id;?>'"><?php echo $ashow->title;?></a></div>
		<div id="show-box-content">
			<div class="show-genre-djs">	
				<div class="show-genre">Genres: <em><?php echo $ashow->genre;?></em></div>
				<div class="show-djs">DJs: <em></em></div>
			</div>
			<img class="show-image-thumbnail" src="/wp-content/uploads/show_images/<?php echo $ashow->avatar_file_name?>">
		</div>
		<?php if($permission == 3) { ?>
			<div id="admin-controls" style="position:relative">
				<!--<button type="button" style="float:right" onclick="manage(<?php echo $id; ?>)">Manage</button> This is not yet working-->
				<button type="button" onclick="<?php echo $function; ?>('<?php echo $id;?>')" style="float:right"><?php echo $function; ?></button>
			</div> <?php
		}
		?>
	</div>
	<?php if($permission == 3) { ?>
		<div id="manage-show-<?php echo $id ?>">
			<div id="manage-show-tabs-<?php echo $id ?>" class="manage-show-tabs">
				<ul>
					<li><a href="#manage-show-info">Profile</a></li>
					<li><a href="#manage-show-posts">Posts</a></li>
					<li><a href="#manage-show-playlist">Playlist</a></li>
				</ul>
				<div id="manage-show-info">
					<div id="manage-image">
						<img src="/wp-content/uploads/show_images/<?php echo $ashow->avatar_file_name ?>"><br>
						<input type="text" disabled="true" value="<?php echo $ashow->avatar_file_name ?>"><button type="button">Upload Image</button>
					</div>
					<div id="manage-data">
						<ul>
							<li><input type="text" id="manage-title-<?php echo $id ?>" value="<?php echo $ashow->title ?>"></li>
							<li><input type="text" id="manage-genre-<?php echo $id ?>" value="<?php echo $ashow->genre ?>"></li>
							<li><textarea id="manage-description-<?php echo $id ?>" rows="10"><?php echo $ashow->description ?></textarea></li>
							<li>
								<input type="text" id="manage-djs-<?php echo $id ?>" disabled="true" value="Enter DJ Name...">
								<div id="manage-djs-list-<?php echo $id ?>"></div>
							</li>
						</ul>
					</div>
				</div>
				<div id="manage-show-posts">
				</div>
				<div id="manage-show-playlist">
				</div>
			</div>
		</div>
		<script>
			jQuery('#manage-show-'+<?php echo $id ?>).dialog({
				autoOpen: false,
				buttons: [
					{
						text: "Cancel",
						click: function() {
							jQuery(this).dialog('close')
							jQuery('#mask').animate({
								opacity: 0
							}, 500, function() {
								jQuery('#mask').css('display', 'none');
							})
						}
					},
					{
						text: "Save",
						click: function() {
							jQuery(this).dialog('close')
							jQuery('#mask').animate({
								opacity: 0
							}, 500, function() {
								jQuery('#mask').css('display', 'none');
							})
						}
					}
				],
				draggable: false,
				modal: true,
				resizable: false,
				title: '<?php echo $ashow->title; ?>',
				width: 700,
				height: 500,
				closeOnEscape: false
			})
			jQuery('#manage-show-tabs-'+<?php echo $id ?>).tabs()
		</script> <?php
	}
}
?>
<script>
	function manage(id) {
		jQuery("#manage-show-"+id).dialog('open');
		jQuery('#mask').css('display', 'block');
		jQuery('#mask').animate({
			opacity: .85
		}, 1000)
	}
</script>