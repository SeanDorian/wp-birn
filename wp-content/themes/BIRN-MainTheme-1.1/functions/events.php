<?php
include("../../../../wp-blog-header.php");
$action = $_POST['action'];
$user = $_POST['user'];
if($action == 'add') {
	$eInfo = array($_POST['eName'], $_POST['type'], $_POST['date'], $_POST['call'], $_POST['desc'], $_POST['date_formatted']);
	$ePositions = $_POST['positions'];
	$newEvent = $wpdb->insert( //Add Event using the arrays above
		'production_events',
		array(
			'Title' => $eInfo[0],
			'Venue' => $eInfo[1],
			'Date_Formatted' => $eInfo[5],
			'Date' => $eInfo[2],
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
		)
	);
	//Mail Subscribers and Production
	$eventInfo = $wpdb->get_results(
		"
		SELECT *
		FROM production_events
		WHERE ID = $wpdb->insert_id
		"
		);
	foreach ($eventInfo as $ei) {
		$subject = 'New Production Opportunity: '.$ei->Date_Formatted.' ('.$ei->Venue.', '.$ei->Title.')';
		$message = 'If you would like to participate in one of our '.$ei->Venue.' events, please visit the production page (http://www.thebirn.com/cp/production) on the CP to apply.'."\n\n";
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
	//	mail('production@thebirn.com',$subject,$message,$headers);
	}
}
if($action == 'Subscribe') {
	$newSub = $wpdb->insert(
		'production_subscription',
		array(
			'User' => $user
		)
	);
}
if($action == 'Unsubscribe') {
	$removeSub = $wpdb->query(
		"
		DELETE FROM production_subscription
		WHERE User = $user
		"
		);
}
?>
<div class="production-filter" style="float:left">
	<button type="button" id="all-venues" class="left" onclick="production('A', 'All', 0)">All Venues</button>
	<button type="button" id="939-venue" class="middle" onclick="production('A', 'Red Room', 1)">939 Red Room</button>
	<button type="button" id="live-venue" class="middle" onclick="production('A', 'Live Room', 2)">Live Room</button>
	<button type="button" id="bpc-venue" class="middle" onclick="production('A', 'BPC Broadcast', 3)">BPC Broadcast</button>
	<button type="button" id="other-venue" class="right" onclick="production('A', 'Other', 4)">Other</button>
</div>
<div class="production-filter" style="float:right">
	<button type="button" id="all-events" class="left" onclick="production('B', -1)">All Events</button>
	<button type="button" id="progress-events" class="middle" onclick="production('B', 0)">In Progress</button>
	<button type="button" id="complete-events" class="middle" onclick="production('B', 1)">Complete</button>
	<button type="button" id="cancelled-events" class="right" onclick="production('B', 2)">Cancelled</button>
</div>
<table id="event-list"  class="white-box" style="clear:both" width="100%">
	<thead>
		<th>Date</th>
		<th>Call Time</th>
		<th>Type</th>
		<th>Title</th>
		<th>Status</th>
	</thead>
	<tbody>
		<?php
		$eventList = $wpdb->get_results(
			"
			SELECT *
			FROM production_events
			ORDER BY Date ASC
			"
			);
		foreach ($eventList as $event) {
			if ($event->Complete == 0) {
				$status = 'In Progress';
			} else if ($event->Complete == 1) {
				$status = 'Complete';
			} else {
				$status = 'Cancelled';
			}
			echo '<tr id="event-'.$event->ID.'" class="el es-'.$event->Complete.'" onclick="production(&#39;show event&#39;, '.$event->ID.')"><td>'.$event->Date_Formatted.'</td><td>'.$event->Call_Time.'</td><td>'.$event->Venue.'</td><td>'.$event->Title.'</td><td>'.$status.'</td></tr>';
		}
		?>
	</tbody>
</table>
<script>
	jQuery('#all-venues, #progress-events').css('background-color', '#ddd')
	jQuery('.es-1, .es-2').hide();
</script>