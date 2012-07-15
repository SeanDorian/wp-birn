<?php
/**
 * Template Name: CP View Profile
 */
$id = $_GET['user'];
$dj = get_userdata($id);
$uroles = $wpdb->get_results(
	"
	SELECT *
	FROM roles_users
	WHERE User_ID = $id
	ORDER BY Role_ID ASC
	"
);
get_header(); ?>
<div id="primary" >
	<div id="view-user">
		<div id="profile-image">
			<div class="BIRN-post-title"><?php echo $dj->first_name . ' ' . $dj->last_name ?></div>	
			<img width="100%" src="http://localhost:8888/wp-content/uploads/2012/06/LuisAugusto2.jpeg">
		</div>
		<ul id="user-info" style="width:63%">
			<div id="user-view">
				<?php include ('functions/profile-view.php') ?>
			</div>
			<li class="right-bottom profile-options">
				<?php
					foreach ($uroles as $ur) {
						if($permission >= 1
							|| $uid == $ur->User_ID) {
							echo '<button type="button" onclick="profile(0,'.$id.')" class="big left">View Profile</button>';
							break;
						}
					}
					if ($permission == 3) {
							echo '<button type="button" onclick="profile(1,'.$id.')" class="big middle">Roles &amp; Shows</button>';
					}
					if ($permission == 3) {
							 echo '<button type="button" onclick="profile(2,'.$id.')" class="big middle">Strikes</button>';
					}
					if ($permission >= 1) {
							 echo '<button type="button" onclick="profile(3,'.$id.')" class="big middle">Comments</button>';
					}
					foreach ($uroles as $ur) {
						if($ur->Role_ID == 9) {
							foreach ($roles as $role) {//No good either
								if($role->Role_ID <= 3 ||
									$role->Role_ID == 7 ||
									$uid == $ur->User_ID) {
										echo '<button type="button" onclick="profile(4,'.$id.')" class="big middle">Training</button>';
										break;
									}
							}
						}
					}
					if ($current_user->id == $id || $permission == 3) {
						echo '<button type="button" id="edit_profile" class="big middle">Edit Profile</button>';
						if ($current_user->id == $id) { ?>
							<script>
								jQuery("#edit_profile").click(function() {
									window.open('/cp/edit-profile', '_self')
								})
							</script> <?php
						} else {?>
							<script>
								jQuery("#edit_profile").click(function() {
									window.open('/cp/edit-profile/?admin=true&user=<?php echo $id ?>', '_self')
								})
							</script> <?php	
						}
					}
					?>
			</li>
		</ul>
	</div>
</div>
<script>
jQuery('.profile-options button:last').removeClass('middle').addClass('right')
jQuery('.profile-options button:first').addClass('selected')
</script>
<?php 
include (TEMPLATEPATH . '/cp-footer.php'); ?>
