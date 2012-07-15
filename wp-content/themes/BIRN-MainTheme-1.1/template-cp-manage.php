<?php
/**
 * Template Name: CP Manage
 * Description: The MG Page of the CP
 */
get_header();
$uid = get_current_user_id(); $s = true;
$ur = get_user_meta( $uid, 'user_role', $s );
?>

<div id="primary">
	<div id="tabs" style="border: none">
		<div id="secondary-menu" class="gray-box">
	    	<ul style="border:none">
	        	<li><a href="#tabs-1">Front End Content</a></li>
	        	<li><a href="#tabs-2">Manage Calendar</a></li>
	        	<li><a href="#tabs-3">Add Users</a></li>
	        	<li><a href="#tabs-4">Add Shows</a></li>
	        	<li><a href="#tabs-5">Manage Signups</a></li>
	        	<li><a href="#tabs-6">Manage Training</a></li>
	        	<li><a href="#tabs-7">Manage Strikes</a></li>
	    	</ul>
		</div>
	<div id="mg-content">
	    <div id="tabs-1">
			This will show the News and Events on the front page of the site. Also edit the About and Contact Page on the front site.
	    </div>
	    <div id="tabs-2">
			This is how you add events to the calendar
	    </div>
	    <div id="tabs-3">
			<?php
				if ( $_POST['new-submit']) {
					$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
					$message = "Your new account for the BIRN has been created. To log in, go to http://www.thebirn.com/cp. Your username is ".$_POST['new-email']." and your password is ".$random_password.". You can change your password by logging in and clicking on 'Manage Profile' in the top menu.";
					wp_create_user( $_POST['new-email'], $random_password, $_POST['new-email'] );
					$user = get_user_by('email', $_POST['new-email']);
					$new_id = $user->ID;
					update_user_meta( $new_id, 'first_name', $_POST['new-first-name']);
					update_user_meta( $new_id, 'last_name', $_POST['new-last-name']);
					update_user_meta( $new_id, '_email', $_POST['new-email']);	
					update_user_meta( $new_id, 'nickname', $_POST['new-first-name']);	
					update_user_meta( $new_id, 'user_role', $_POST['new-role']);					
					mail($_POST['new-email'],'Your Account Has Been Created',$message,"From: webteam@thebirn.com");
					?>
						<script type="text/javascript">
						alert('An email has been sent to the user.')
						window.open('/cp/manage/#tabs-3', '_self');
						</script><?php
				}
			?>
			<form action="" method="post" id="add-user-form">
				First Name: <input type="text" name="new-first-name">
				Last Name: <input type="text" name="new-last-name">
				Email: <input type="email" name="new-email"><br>
				Roles: <input style="margin-left: 32px;width:365px"type="text" name="new-role">
				<input type="submit" value="Add User" class="submit-button" name="new-submit">
			</form>	<br>
			Acceptable Roles: <em>Faculty Advisor, Station Manager, Production, Communications, Web Developer, Music Director, Trainer, DJ, Noob, Inactive</em>
			
		</div>
	    <div id="tabs-4">
			show page
	    </div>
	    <div id="tabs-5">
			manage signups
	    </div>
	    <div id="tabs-6">
			manage user
	    </div>
	    <div id="tabs-7">
			manage strike
	    </div>
	</div>
</div>
</div>

<?php

if (strpos($ur,'Faculty Advisor') !== false //You can only view this page if you are a Faculty Advisor, Station Manager, or Web Developer
	|| strpos($ur,'Station Manager') !== false 
	|| strpos($ur,'Web Developer') !== false) {	
		//You can view everything!
} else if (strpos($ur,'Communications') !== false
	|| strpos($ur,'Production') !== false) {?>
		<script type="text/javascript">
			jQuery('#tabs-3,#tabs-4,#tabs-5,#tabs-6, #tabs-7, #tabs ul > li:gt(1)').remove();//Can't view the last four tabs
		</script><?php
		if (strpos($ur,'Production') !== false) {?>
			<script type="text/javascript">
				jQuery('#tabs-1,#tabs ul > li:eq(0)').remove();//Can't view the first tab
			</script><?php
		}
} else { ?>
	<script type="text/javascript">
		jQuery('#primary').remove();
		window.open('/cp', '_self');
	</script><?php
}


include (TEMPLATEPATH . '/cp-footer.php'); ?>

