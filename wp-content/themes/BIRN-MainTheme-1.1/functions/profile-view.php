<?php include("../../../../wp-blog-header.php"); 
$id = $_GET['user'];
$dj = get_userdata($id);
$uroles = $wpdb->get_results(
	"
	SELECT *
	FROM roles_users
	WHERE User_ID = $id
	ORDER BY Role_ID ASC
	"
);?>
<li>
	<div id="label">Real Name: </div><div id="info"><?php echo $dj->first_name . ' ' . $dj->last_name ?></div>
	<div id="label">DJ Name: </div><div id="info"><?php echo $dj->nickname ?></div>
</li>
<li>
	<div id="label">Roles: </div><div id="info" class="roles">
		<?php 
		foreach ($uroles as $role) {
			$titles = $wpdb->get_results(
				"
				SELECT *
				FROM roles
				WHERE ID = $role->Role_ID
				"
			);
			foreach ($titles as $title) {
				echo $title->Name.', ';
			}
		}
		?>
	</div>
	<div id="label">Active Since: </div><div id="info"><?php echo $dj->user_registered ?></div>
</li>
<li>
	<div id="label">Shows: </div><div id="info" class="show-info">
		<?php
		$usershows = $wpdb->get_results( 
			"
			SELECT * 
			FROM shows_users
			WHERE user_id = $id
			"
		);
		foreach ($usershows as $usershow){
			$show = $usershow->show_id;
			$listshows = $wpdb->get_results( 
				"
				SELECT * 
				FROM shows
				WHERE id = $show AND active = 1
				"
			);
			foreach ($listshows as $listshow){
				echo '<em><a target="_blank" href="/shows/?show='.$listshow->id.'">'.$listshow->title.', </a></em>';
			}
		}
		?>
	</div>
</li>
<li>
	<div id="label">Email: </div><div id="info"><?php echo $dj->_email ?></div>
	<div id="label">Phone: </div><div id="info"><?php echo $dj->user_phone ?></div>
</li>
<li>
	<div id="label">About: </div><br><?php echo $dj->description ?>
</li>
<script>
jQuery('.show-info > em:last').find('a').text(jQuery('.show-info > em:last').find('a').text().slice(0,-2));
jQuery('.roles').text(jQuery('.roles').text().slice(0,-3))
</script>