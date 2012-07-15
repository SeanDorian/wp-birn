<?php include("../../../../wp-blog-header.php");
$id = $_GET['user'];
$action = $_POST['action'];
$step = $_POST['step'];
$user = $_POST['user'];
$showID = $_POST['id'];
if ($action == 'complete') {
	$wpdb->insert( 
		'roles_users', 
		array( 
			'User_ID' => $_POST['user'], 
			'Role_ID' => $_POST['step'] 
		)
	);
};
if ($action == 'remove') {
	$wpdb->query( 
		"
         DELETE FROM roles_users
		 WHERE Role_ID = $step
		 AND User_ID = $user
		"
	);	
};
if ($action == 'remove-show') {
	$wpdb->query( 
		"
         DELETE FROM shows_users
		 WHERE user_id = $user
		 AND show_id = $showID
		"
	);	
};
if ($action == 'add-show') {
	$wpdb->insert( 
		'shows_users', 
		array( 
			'user_id' => $user, 
			'show_id' => $showID 
		)
	);
};
$roles = $wpdb->get_results(
	"
	SELECT *
	FROM roles
	"
	);
echo '<ul id="training-list" style="width: 50%">';
foreach ($roles as $role) {
	echo "<li id='item-".$role->ID."'>";
	$check = $wpdb->get_results(
		"
		SELECT *
		FROM roles_users
		WHERE User_ID = $id
		AND Role_ID = $role->ID
		"
		);
			if($check) {
				echo '<input type="checkbox" class="complete" checked="checked" id="'.$role->ID.'" onclick="updateRoles('.$id.', '.$role->ID.')">';
				?><script>
					jQuery('li#item-'+<?php echo $role->ID ?>).animate({
						backgroundColor: 'yellow'
					}, 1000)
				</script><?php
			} else {
				echo '<input type="checkbox" id="'.$role->ID.'" onclick="updateRoles('.$id.', '.$role->ID.')">';
			};

	echo $role->Name;
	echo "</li>";
};
echo '</ul>';
?>
<script>
	var shows = [];
	var myShows = [];
</script>
<?php
$shows = $wpdb->get_results(
	"
	SELECT *
	FROM shows
	"
	);
foreach ($shows as $s) {
	?><script>
		shows.push({label: '<?php echo $s->title ?>', id: '<?php echo $s->id ?>'})
	</script><?php
}
$myShows = $wpdb->get_results(
	"
	SELECT *
	FROM shows_users
	WHERE user_id = $id
	"
	);
foreach ($myShows as $ms) {
	$shows = $wpdb->get_results(
		"
		SELECT *
		FROM shows
		WHERE id = $ms->show_id
		ORDER BY title ASC
		"
		);
	foreach ($shows as $show) {
		?><script>
			jQuery('#show-list').append('<div class="my-shows" id="my-shows-<?php echo $show->id; ?>"><?php echo $show->title; ?><span onclick="removeShow('+<?php echo $show->id ?>+','+<?php echo $id ?>+')">x</span></div>')
		</script><?php
	}
}
?>
<div id="show-list">
	<input type="text" style="width: 95%; float:right" id="show-input" value="Add Show">
</div>
<script>
	jQuery('#show-input').click(function(){
		jQuery('#show-input').select()
	})
	jQuery('#show-input').autocomplete({
		source: shows,
		focus: function( event, ui ) {
						$( "#show-input" ).val( ui.item.label );
						return false;
					},
		select: function(event, ui) {			
			addShow(ui.item.id, <?php echo $id; ?> ,ui.item.label)
		}
	})
</script>