<?php include("../../../../wp-blog-header.php");
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
$id = $_GET['user'];
$action = $_POST['action'];
$step = $_POST['step'];
$user = $_POST['user'];
if ($action == 'complete') {
	$wpdb->insert( 
		'training_users', 
		array( 
			'User_ID' => $_POST['user'], 
			'Step_ID' => $_POST['step'] 
		)
	);
};
if ($action == 'remove') {
	$wpdb->query( 
		"
         DELETE FROM training_users
		 WHERE Step_ID = $step
		 AND User_ID = $user
		"
	);	
};
$steps = $wpdb->get_results(
	"
	SELECT *
	FROM training
	"
	);
echo '<ul id="training-list">';
foreach ($steps as $step) {
	echo "<li id='item-".$step->ID."'>";
	$check = $wpdb->get_results(
		"
		SELECT *
		FROM training_users
		WHERE User_ID = $id
		AND Step_ID = $step->ID
		"
		);
	if ($permission >= 1) {
		if($check) {
			echo '<input type="checkbox" class="complete" checked="checked" id="'.$step->ID.'" onclick="updateTraining('.$id.', '.$step->ID.')">';
		} else {
			echo '<input type="checkbox" id="'.$step->ID.'" onclick="updateTraining('.$id.', '.$step->ID.')">';
		};
	}
	if($check) {
		foreach ($check as $c) {
			?><span style="float:right">Completed On: <?php echo $c->date; ?></span><?php		
		}
		?><script>
			jQuery('li#item-'+<?php echo $step->ID ?>).animate({
				backgroundColor: 'yellow'
			}, 1000)
			jQuery('li#item-'+<?php echo $step->ID ?>+' span').text(jQuery('li#item-'+<?php echo $step->ID ?>+' span').text().slice(0,-9))
		</script><?php
	}
	echo $step->step;
	echo "</li>";
};
echo '</ul>';
?>