<?php
include("../../../../wp-blog-header.php");
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
	if ($role->Role_ID <= 4) {
		$permission = 3;
		break;
	} else {
		$permission = 0;
	}
}
$action = $_POST['action'];
$eventID = $_POST['eventID'];
$eventData = $wpdb->get_results(
	"
	SELECT *
	FROM production_events
	WHERE ID = $eventID
	"
	);
foreach($eventData as $ed) { ?>
	<div class="white-box" id="event-details">
		<div class="BIRN-post-title" style="font-size:1.5em"><?php echo $ed->Title ?></div>
		<div style="float:left; width:50%"><?php echo nl2br($ed->Content) ?>
		</div>
		<div style="float:right; width:50%;text-align:right">
			<strong>Positions Needed to be Filled:</strong><br>
			<ul id="event-positions">
			<?php
			echo ($ed->Producer == 'true' ? '<li><input type="checkbox" id="producer" onclick="flashApply()"> Producer </li>' : '');
			echo ($ed->Engineer == 'true' ? '<li><input type="checkbox" id="engineer" onclick="flashApply()"> Engineer </li>': '');
			echo ($ed->Assistant == 'true' ? '<li><input type="checkbox" id="assistant" onclick="flashApply()"> Assistant </li>' : '');
			echo ($ed->Photographer == 'true' ? '<li><input type="checkbox" id="photographer" onclick="flashApply()"> Photographer </li>' : '');
			echo ($ed->Videographer == 'true' ? '<li><input type="checkbox" id="videographer" onclick="flashApply()"> Videographer </li>' : '');
			echo ($ed->DJ == 'true' ? '<li><input type="checkbox" id="dj-top" onclick="flashApply()"> DJ for Top/Tail </li>' : '');
			echo ($ed->Interviewer == 'true' ? '<li><input type="checkbox" id="dj-interview" onclick="flashApply()"> DJ for Interview </li>' : '');
			echo ($ed->Reviewer == 'true' ? '<li><input type="checkbox" id="dj-review" onclick="flashApply()"> DJ for Review </li>' : '');
			echo ($ed->Observer == 'true' ? '<li><input type="checkbox" id="observers" onclick="flashApply()"> Observers </li>' : '');
			?>
			</ul>
		</div>
		<div id="event-options">
			<?php
			if ($permission == 3) { ?>
				<button type="button" class="big left">Cancel Event</button>
				<button type="button" class="big middle">Edit</button>
				<button type="button" class="big middle">Recruit</button>
				<button type="button" id="event-apply" class="big middle">Update Application</button> <?php
			} else { ?>
				<button type="button" id="event-apply" class="big left">Update Application</button> <?php
			}
			?>
			<button type="button" class="big right">Go Back</button>
		</div>
	</div>
<?php }
?>