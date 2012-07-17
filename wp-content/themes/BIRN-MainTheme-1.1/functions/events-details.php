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
$eventID = ($_GET['eventid'] ? $_GET['eventid'] : $_POST['eventID']);
//Combine these functions maybe?
if ($action == 'cancel') {//When an event is cancelled
	$cancel = $wpdb->update(
		'production_events',
		array(
			'Complete' => 2
		),
		array(
			'ID' => $eventID
		)
	);
	$eventInfo = $wpdb->get_results(
		"
		SELECT *
		FROM production_events
		WHERE ID = $eventID
		"
		);
	foreach ($eventInfo as $ei) {
		$subject = 'Production Cancelled: '.$ei->Date_Formatted.' ('.$ei->Venue.', '.$ei->Title.')';
		$message = 'You are receiving this email because the '.$ei->Title.' event on '.$ei->Date_Formatted.' has been cancelled.'."\n\n";
		$message .= 'If you would like to participate in one of our other '.$ei->Venue.' events, please visit the production page (http://www.thebirn.com/cp/production/) on the CP to apply.'."\n\n";
		$message .= "\n\n".'Sincerely,'."\n".'The Production Team';
		$headers = 'From: Production@thebirn.com';
		$users = $wpdb->get_results(
			"
			SELECT *
			FROM production_subscription
			"
			);
		foreach ($users as $u) {
			$user_data = get_userdata($u->User);
			$subscribed = $user_data->_email;
			mail($subscribed,$subject,$message,$headers);
		}
		mail('production@thebirn.com',$subject,$message,$headers);
	}
};
if ($action == 'resume') {
	$cancel = $wpdb->update(
		'production_events',
		array(
			'Complete' => 0
		),
		array(
			'ID' => $eventID
		)
	);
$eventInfo = $wpdb->get_results(
	"
	SELECT *
	FROM production_events
	WHERE ID = $eventID
	"
	);
foreach ($eventInfo as $ei) {
	$subject = 'Production Resumed: '.$ei->Date_Formatted.' ('.$ei->Venue.', '.$ei->Title.')';
	$message = 'You are receiving this email because the '.$ei->Title.' event on '.$ei->Date_Formatted.' has been resumed.'."\n\n";
	$message .= 'If you would like to participate in one of our other '.$ei->Venue.' events, please visit the production page (http://www.thebirn.com/cp/production/) on the CP to apply.'."\n\n";
	$message .= "\n\n".'Sincerely,'."\n".'The Production Team';
	$headers = 'From: Production@thebirn.com';
	$users = $wpdb->get_results(
		"
		SELECT *
		FROM production_subscription
		"
		);
	foreach ($users as $u) {
		$user_data = get_userdata($u->User);
		$subscribed = $user_data->_email;
		mail($subscribed,$subject,$message,$headers);
	}
	mail('production@thebirn.com',$subject,$message,$headers);
}};
if ($action == 'save edit') {
	$eInfo = array($_POST['eName'], $_POST['type'], $_POST['date'], $_POST['call'], $_POST['desc'], $_POST['date_formatted']);
	$ePositions = $_POST['positions'];
	$newEvent = $wpdb->update( //Add Event using the arrays above
		'production_events',
		array(
			'Title' => $eInfo[0],
			'Venue' => $eInfo[1],
			'Date_Formatted' => $eInfo[2],
			'Date' => $eInfo[5],
			'Call_Time' => $eInfo[3],
			'Content' => $eInfo[4],
			'Complete' => 0,
			'Producer' => $ePositions[0],
			'Engineer' => $ePositions[1],
			'Assistant' => $ePositions[2],
			'Photographer' => $ePositions[3],
			'Videographer' => $ePositions[4],
			'DJ' => $ePositions[5],
			'Interviewer' => $ePositions[6],
			'Reviewer' => $ePositions[7],
			'Observer' => $ePositions[8]
		),
		array('ID' => $eventID)
	);
	if ($_POST['bigchange'] == 'true') {
		//Mail Subscribers and Production if there's a date or position change
		$eventInfo = $wpdb->get_results(
			"
			SELECT *
			FROM production_events
			WHERE ID = $eventID
			"
			);
		foreach ($eventInfo as $ei) {
			$subject = 'Production Update: '.$ei->Date_Formatted.' ('.$ei->Venue.', '.$ei->Title.')';
			$message = 'You are receiving this update because there has been a change to one of the production opportunities regarding the date or positions that need to be filled.'."\n\n";
			$message .= 'If you would like to participate in one of our '.$ei->Venue.' events, please visit the production page (http://www.thebirn.com/cp/production/?eventid='.$ei->ID.') on the CP to apply.'."\n\n";
			$message .= 'Here are the positions we need to have filled:'."\n";
			$message .= ($ei->Producer == 'true' ? 'Producer'."\n" : '');
			$message .= ($ei->Engineer == 'true' ? 'Engineer'."\n" : '');
			$message .= ($ei->Assistant == 'true' ? 'Assistant'."\n" : '');
			$message .= ($ei->Photographer == 'true' ? 'Photographer'."\n" : '');
			$message .= ($ei->Videographer == 'true' ? 'Videographer'."\n" : '');
			$message .= ($ei->DJ == 'true' ? 'DJ for Top/Tail'."\n" : '');
			$message .= ($ei->Interviewer == 'true' ? 'DJ for Interview'."\n" : '');
			$message .= ($ei->Reviewer == 'true' ? 'DJ for Review'."\n" : '');
			$message .= ($ei->Observer == 'true' ? 'Observers'."\n" : '');
			$message .= "\n".'Call Time is '.$ei->Call_Time.' SHARP'."\n\n";
			$message .= 'Here is some information about the show:'."\n".$ei->Content;
			$message .= "\n\n".'Sincerely,'."\n".'The Production Team';
			$headers = 'From: Production@thebirn.com';
			$users = $wpdb->get_results(
				"
				SELECT *
				FROM production_subscription
				"
				);
			foreach ($users as $u) {
				$user_data = get_userdata($u->User);
				$subscribed = $user_data->_email;
				mail($subscribed,$subject,$message,$headers);
			}
			mail('production@thebirn.com',$subject,$message,$headers);
		}
	}
}
if ($action == 'apply') {
	$positions = $_POST['positions'];
	$ePositions = array(
		"Booleans" => array(
			$positions[0],
			$positions[1],
			$positions[2],
			$positions[3],
			$positions[4],
			$positions[5],
			$positions[6],
			$positions[7],
			$positions[8]
		), 
		"Name" => array(
			0 => 'Producer',
			1 => 'Engineer',
			2 => 'Assistant',
			3 => 'Photographer',
			4 => 'Videographer',
			5 => 'DJ for Top/Tail',
			6 => 'DJ for Interview',
			7 => 'DJ for Review',
			8 => 'Observer'
		)
	);
	for ($i=0;$i<9;$i++) {
		$name = $ePositions["Name"][$i];
		if($ePositions["Booleans"][$i] == 'true') {
			$application = $wpdb->insert(
				'production_roster',
				array(
					"Event" => $eventID,
					"User" => $uid,
					"Position" => $name,
					"On_The_Roster" => 0
				)
			);
		} else if ($ePositions["Booleans"][$i] == 'false') {
			$application = $wpdb->query(
				"
				DELETE FROM production_roster
				WHERE Event = $eventID AND User = $uid AND Position = '$name'
				"	
			);			
		}
		
	}
}
if($action == 'start recruit') {
	foreach($_POST['notRecruit'] as $n) {
		$unassign = $wpdb->update(
			'production_roster',
			array(
				"On_The_Roster" => 0
			),
			array(
				"User" => $n['id'],
				"Position" => $n['label'],
				"Event" => $eventID
			)
		);
	}
	foreach($_POST['recruit'] as $r) {
		$assign = $wpdb->update(
			'production_roster',
			array(
				"On_The_Roster" => 1
			),
			array(
				"User" => $r['id'],
				"Position" => $r['label'],
				"Event" => $eventID
			)
		);
		$complete = $wpdb->update(
			'production_events',
			array(
				"Complete" => 1
			),
			array(
				"ID" => $eventID
			)
		);
	$eventInfo = $wpdb->get_results(
		"
		SELECT *
		FROM production_events
		WHERE ID = $eventID
		"
		);
	}
	foreach ($eventInfo as $ei) {
		$subject = 'Production Roster: '.$ei->Date_Formatted.' ('.$ei->Venue.', '.$ei->Title.')';
		$message = 'Thank you for volunteering for this event.'."\n\n";
		$message .= 'Here is the production roster for this event:'."\n";
		foreach($_POST['recruit'] as $r) {
			$ui = get_userdata($r['id']);
			$name = $ui->first_name.' '.$ui->last_name;
			$message .= $name.' - '.$r['label']."\n";
		}
		$message .= "\n";
		$message .= 'Call Time is '.$ei->Call_Time.' SHARP'."\n\n";
		$message .= 'Please respond to this email ASAP to confirm your participation.'."\n\n";
		$message .= "\n\n".'Sincerely,'."\n".'The Production Team';
		$headers = 'From: Production@thebirn.com';
		$users = $wpdb->get_results(
			"
			SELECT *
			FROM production_subscription
			"
			);
		foreach ($_POST['recruit'] as $r) {
			$user_data = get_userdata($r['id']);
			$subscribed = $user_data->_email;
			mail($subscribed,$subject,$message,$headers);
		}
		mail('production@thebirn.com',$subject,$message,$headers);
	};
}
$eventData = $wpdb->get_results(
	"
	SELECT *
	FROM production_events
	WHERE ID = $eventID
	"
	);
foreach($eventData as $ed) {
	switch ($ed->Complete) {
		case 0: $status = 'In Progress'; break;
		case 1: $status = 'Complete'; break;
		case 2: $status = 'Cancelled'; break;
	}
	if ($action == 'edit') { ?>
		<div class="gray-box" id="edit-production">
			<input type="hidden" id="big-change"><!--Signifies any major edits, such as date or positions-->
			<ul>
				<li><input type="text" style="width: 98%" value="<?php echo $ed->Title ?>" id="event-name-edit"></li>
				<li style="margin-bottom: 10px">
					<select id="event-type-edit" style="margin-top:10px">
						<option value="Unselected">Event Type...</option>
						<option value="Red Room">939 Red Room</option>
						<option value="Live Room">Live Room</option>
						<option value="BPC Broadcast">BPC Broadcast</option>
						<option value="Other">Other</option>
					</select>
					<span style="float:right">
						<input type="text" id="event-date-edit" onchange="bigchange()" value="<?php echo $ed->Date_Formatted ?>">
						<input type="text" id="event-call-edit" class="small" onchange="bigchange()" value="<?php echo $ed->Call_Time ?>">
						<input type="hidden" id="event-date-f-edit" value="<?php echo $ed->Date ?>">
					</span>
					<script>
					jQuery('#event-date-edit').datepicker({
						dateFormat: 'DD, MM dd, yy', //Makes the date look nice
						altField: '#event-date-f-edit', 
						altFormat: "yy/mm/dd (DD)" //Formats the date so that it sorts properly
					})
					jQuery('#event-type-edit option').each(function(index) { //This preselects the option from #event-type-edit
						if (jQuery(this).attr('value') == '<?php echo $ed->Venue ?>') {
							jQuery(this).attr('selected', 'selected')
						}
					})
					</script>
				</li>
				<li>Description:<br>
					<textarea rows="5" id="event-desc-edit"><?php echo $ed->Content ?></textarea>
				</li>
				<li><div style="text-align:center">Positions Needed:</div>
					<div style="display: inline-block;width:30%">
						<input type="checkbox" id="event-producer-edit" onchange="bigchange()"> Producer <br>
						<input type="checkbox" id="event-engineer-edit" onchange="bigchange()"> Engineer <br>
						<input type="checkbox" id="event-assistant-edit" onchange="bigchange()"> Assistant
					</div>
					<div style="vertical-align:top;display: inline-block;width:30%">
						<input type="checkbox" id="event-photographer-edit" onchange="bigchange()"> Photographer <br>
						<input type="checkbox" id="event-videographer-edit" onchange="bigchange()"> Videographer<br>
						<input type="checkbox" id="event-observer-edit" onchange="bigchange()"> Observer
					</div>
					<div style="vertical-align:top;display: inline-block;width:30%">
						<input type="checkbox" id="event-dj-edit" onchange="bigchange()"> DJ for Top/Tail <br>
						<input type="checkbox" id="event-reviewer-edit" onchange="bigchange()"> DJ for Review<br>
						<input type="checkbox" id="event-interviewer-edit" onchange="bigchange()"> Interviewer
					</div>
				</li>
				<li>
					<button type="button" class="big" style="float:right" onclick="eventOptions('save edit', <?php echo $ed->ID ?>)">Save</button>
					<button type="button" class="big" style="float:right" onclick="eventOptions('cancel edit', <?php echo $ed->ID ?>)">Cancel</button>
				</li>
			</ul>
		</div> <?php
		if ($ed->Producer == 'true') { ?><script>jQuery('#event-producer-edit').attr('checked', 'checked'); </script><?php }
		if ($ed->Engineer == 'true') { ?><script>jQuery('#event-engineer-edit').attr('checked', 'checked'); </script><?php }
		if ($ed->Assistant == 'true') { ?><script>jQuery('#event-assistant-edit').attr('checked', 'checked'); </script><?php }
		if ($ed->Photographer == 'true') { ?><script>jQuery('#event-photographer-edit').attr('checked', 'checked'); </script><?php }
		if ($ed->Videographer == 'true') { ?><script>jQuery('#event-videographer-edit').attr('checked', 'checked'); </script><?php }
		if ($ed->Observer == 'true') { ?><script>jQuery('#event-Observer-edit').attr('checked', 'checked'); </script><?php }
		if ($ed->DJ == 'true') { ?><script>jQuery('#event-dj-edit').attr('checked', 'checked'); </script><?php }
		if ($ed->Reviewer == 'true') { ?><script>jQuery('#event-reviewer-edit').attr('checked', 'checked'); </script><?php }
		if ($ed->Interviewer == 'true') { ?><script>jQuery('#event-interviewer-edit').attr('checked', 'checked'); </script><?php }
	} else if ($action == 'recruit') { ?>
		<div class="white-box" id="events-recruit">
			<div class="BIRN-post-title" style="font-size:1.5em"><?php echo $ed->Title.' ('.$status.')' ?></div>
			<?php
			echo ($ed->Producer == 'true' ? '<div id="posList-0" class="posList"><span>Producers</span></div>' : '');
			echo ($ed->Engineer == 'true' ? '<div id="posList-1" class="posList"><span>Engineers</span></div>': '');
			echo ($ed->Assistant == 'true' ? '<div id="posList-2" class="posList"><span>Assistants</span></div>' : '');
			echo ($ed->Photographer == 'true' ? '<div id="posList-3" class="posList"><span>Photographers</span></div>' : '');
			echo ($ed->Videographer == 'true' ? '<div id="posList-4" class="posList"><span>Videographers</span></div>' : '');
			echo ($ed->DJ == 'true' ? '<div id="posList-5" class="posList"><span>DJs for Top/Tail</span></div>' : '');
			echo ($ed->Interviewer == 'true' ? '<div id="posList-6" class="posList"><span>DJs for Interview</span></div>' : '');
			echo ($ed->Reviewer == 'true' ? '<div id="posList-7" class="posList"><span>DJs for Review</span></div>' : '');
			echo ($ed->Observer == 'true' ? '<div id="posList-8" class="posList"><span>Observers</span></div>' : '');
			$eventApps = $wpdb->get_results(
				"
				SELECT *
				FROM production_roster
				WHERE Event = $eventID
				"
			);
			foreach($eventApps as $ea) { 
				$userInfo = get_userdata($ea->User);
				$username = $userInfo->first_name.' '.$userInfo->last_name; ?><script>
				var div;
				function appendToAppList(eventID, username, userID, position) {
					switch(position) {
						case 'Producer': div = 'posList-0';	break;
						case 'Engineer': div = 'posList-1';	break;
						case 'Assistant': div = 'posList-2';	break;
						case 'Photographer': div = 'posList-3';	break;
						case 'Videographer': div = 'posList-4';	break;
						case 'DJ for Top/Tail': div = 'posList-5';	break;
						case 'DJ for Interview': div = 'posList-6';	break;
						case 'DJ for Review': div = 'posList-7';	break;
						case 'Observer': div = 'posList-8';	break;
					}
					jQuery('#'+div).append('<div class="event-recruit-item"><input type="checkbox" class="'+position+'" title="'+userID+'"> <a href="/cp/profiles/?user='+userID+'" target="_blank">'+username+'</a></div>');
				}
				appendToAppList('<?php echo $eventID ?>', '<?php echo $username ?>', '<?php echo $ea->User ?>', '<?php echo $ea->Position ?>')
				</script><?php
				if ($ea->On_The_Roster == 1) { ?><script>
					var id = '<?php echo $ea->Position ?>';
					jQuery('.'+id).attr('checked','checked');
				</script><?php
				}
			}
			?>
			<div id="event-options">
				<button type="button" class="big left" onclick="eventOptions('cancel edit', <?php echo $ed->ID ?>)">Cancel</button>
				<button type="button" class="big right" onclick="eventOptions('start recruit', <?php echo $ed->ID ?>)">Finish Recruit</button>
			</div>
		</div><?php	
	} else { ?>
		<div class="white-box" id="event-details">
			<div class="BIRN-post-title" style="font-size:1.5em"><?php echo $ed->Title.' ('.$status.')' ?></div>
			<div style="text-align: center;font-size:1.1em;font-weight:bold"><?php echo $ed->Venue.' | Call Time: '.$ed->Date_Formatted.' at '.$ed->Call_Time ?></div>			
			<div style="float:left; width:50%">
				<?php echo nl2br($ed->Content) ?>
			</div>
			<div style="float:right; width:50%;text-align:right">
				<strong>Positions Needed to be Filled:</strong><br>
				<ul id="event-positions">
				<?php
				echo ($ed->Producer == 'true' ? '<li><input type="checkbox" id="producer" > Producer </li>' : '');
				echo ($ed->Engineer == 'true' ? '<li><input type="checkbox" id="engineer" > Engineer </li>': '');
				echo ($ed->Assistant == 'true' ? '<li><input type="checkbox" id="assistant" > Assistant </li>' : '');
				echo ($ed->Photographer == 'true' ? '<li><input type="checkbox" id="photographer" > Photographer </li>' : '');
				echo ($ed->Videographer == 'true' ? '<li><input type="checkbox" id="videographer" > Videographer </li>' : '');
				echo ($ed->DJ == 'true' ? '<li><input type="checkbox" id="dj-top" > DJ for Top/Tail </li>' : '');
				echo ($ed->Interviewer == 'true' ? '<li><input type="checkbox" id="dj-interview" > DJ for Interview </li>' : '');
				echo ($ed->Reviewer == 'true' ? '<li><input type="checkbox" id="dj-review" > DJ for Review </li>' : '');
				echo ($ed->Observer == 'true' ? '<li><input type="checkbox" id="observers" > Observers </li>' : '');
				if($ed->Complete >= 1) { ?><script>
					jQuery('#event-positions input').attr('disabled', 'true');</script><?php
				}
				$checkApp = $wpdb->get_results(
					"
					SELECT *
					FROM production_roster
					WHERE Event = $eventID AND User = $uid
					"
				);
				foreach($checkApp as $c) { ?><script>
					var id = '<?php echo $c->Position ?>'.toLowerCase()
					jQuery('#'+id).attr('checked','checked');
				</script><?php
				}
				?>
				</ul>
			</div>
			<div id="event-options">
				<?php
				if ($permission == 3) { 
					if ($ed->Complete != 2) { ?>
						<button type="button" class="big left" onclick="eventOptions('cancel',<?php echo $ed->ID ?>)">Cancel Event</button> <?php
					} else if ($ed->Complete == 2) { ?>
						<button type="button" class="big left" onclick="eventOptions('resume',<?php echo $ed->ID ?>)">Resume Event</button> <?php
					}
				?>
					<button type="button" class="big middle" onclick="eventOptions('edit', <?php echo $ed->ID ?>)">Edit</button>
					<button type="button" class="big middle" onclick="eventOptions('recruit', <?php echo $ed->ID ?>)">Recruit</button>
					<button type="button" id="event-apply" class="big middle" onclick="eventOptions('apply', <?php echo $ed->ID ?>)">Update Application</button> <?php
				} else { ?>
					<button type="button" id="event-apply" class="big left" onclick="eventOptions('apply', <?php echo $ed->ID ?>)">Update Application</button> <?php
				}
				?>
				<button type="button" class="big right" onclick="eventOptions('Go Back')">Go Back</button>
			</div>
		</div> <?php
	}
}
?>