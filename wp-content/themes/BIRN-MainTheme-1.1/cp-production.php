<?php
/**
 * Template Name: CP Production
 */
/* 
Positions: Producer, Engineer, Assistant, Photographer, Videographer, DJ for Top/Tail, Interviewer
*/
get_header(); ?>
<style>
button {
	margin: 0;
} 
</style>
<div id="primary" >
	<div id="page-header">
		<div id="page-title">
			Production
		</div>
		<?php
		if($permission >= 2.5) { ?>
			<button type="button" class="big" onclick="production('new')">New Event</button> <?php
		}
		$subscription = $wpdb->get_results(
			"
			SELECT *
			FROM production_subscription
			"
			);
		$isSubscribed = 'Subscribe';
		foreach($subscription as $sub) {
			if ($sub->User == $uid) {
				$isSubscribed = 'Unsubscribe';
				break;
			}			
		}
		?>
			<button id="sub-button" type="button" style="float:right" class="big" onclick="production('<?php echo $isSubscribed ?>', <?php echo $uid ?>)"><?php echo $isSubscribed; ?></button>
		<!--Needs to check and see if you're already subscribed, then change text and function to unsubscribe-->
	</div>
	<?php
	if($permission >= 2) { ?>
		<div class="gray-box hide" id="new-production">
			<ul>
				<li><input type="text" style="width: 98%" value="Event Name" id="event-name"></li>
				<li style="margin-bottom: 10px">
					<select id="event-type" style="margin-top:10px">
						<option value="Unselected">Event Type...</option>
						<option value="Red Room">939 Red Room</option>
						<option value="Live Room">Live Room</option>
						<option value="BPC Broadcast">BPC Broadcast</option>
						<option value="Other">Other</option>
					</select>
					<span style="float:right"><input type="text" id="event-date" value="Date"><input type="text" id="event-call" class="small" value="5:00pm"><input type="hidden" id="event-date-f"></span>
				</li>
				<li>Description:<br>
					<textarea rows="5" id="event-desc"></textarea>
				</li>
				<li><div style="text-align:center">Positions Needed:</div>
					<div style="display: inline-block;width:30%">
						<input type="checkbox" id="event-producer"> Producer <br>
						<input type="checkbox" id="event-engineer"> Engineer <br>
						<input type="checkbox" id="event-assistant"> Assistant
					</div>
					<div style="vertical-align:top;display: inline-block;width:30%">
						<input type="checkbox" id="event-photographer"> Photographer <br>
						<input type="checkbox" id="event-videographer"> Videographer<br>
						<input type="checkbox" id="event-observer"> Observer
					</div>
					<div style="vertical-align:top;display: inline-block;width:30%">
						<input type="checkbox" id="event-dj"> DJ for Top/Tail <br>
						<input type="checkbox" id="event-reviewer"> DJ for Review<br>
						<input type="checkbox" id="event-interviewer"> Interviewer
					</div>
				</li>
				<li>
					<button type="button" class="big" style="float:right" onclick="production('add')">Add Event</button>
					<button type="button" class="big" style="float:right" onclick="production('cancel')">Cancel</button>
				</li>
			</ul>
		</div> <?php
	}
	?>
	<div id="content">
		<?php
		if ($_GET['eventid']) {
			include ('functions/events-details.php');	
		} else {
			include ('functions/events.php');	
		}
		?>		
	</div>
</div>
<?php 
include (TEMPLATEPATH . '/cp-footer.php'); ?>
