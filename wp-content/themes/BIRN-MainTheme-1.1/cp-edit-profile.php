<?php
/**
 * Template Name: CP Edit Profile
 * Description: Profile Edit Page for CP
 */
$uid = get_current_user_id(); $s = true;
$ur = get_user_meta( $uid, 'user_role', $s );
if ($_GET['admin'] == true) {
	if (strpos($ur,'Faculty Advisor') !== false  
		|| strpos($ur,'Station Manager') !== false 
		|| strpos($ur,'Web Developer') !== false) {	
			$uid = $_GET['user'];
	}
}
$fn = get_user_meta( $uid, 'first_name', $s );
$ln = get_user_meta( $uid, 'last_name', $s );
$dj = get_user_meta( $uid, 'nickname', $s );
$em = get_user_meta( $uid, '_email', $s );
$ph = get_user_meta( $uid, 'user_phone', $s );
$ab = get_user_meta( $uid, 'description', $s );
if($_POST['password'] == $_POST['password-confirm']) {
	if($_POST['submit']) {
		update_user_meta( $uid, 'first_name', $_POST['firstname'], $fn );
		update_user_meta( $uid, 'last_name', $_POST['lastname'], $ln );
		update_user_meta( $uid, 'nickname', $_POST['djname'], $dj );
		update_user_meta( $uid, '_email', $_POST['email'], $em );
		if ($_POST['password'] != '') {
			wp_update_user( array ('ID' => $uid, 'user_pass' => $_POST['password']) ) ;
		}
		update_user_meta( $uid, 'user_phone', $_POST['phone'], $ph );
		update_user_meta( $uid, 'description', $_POST['about'], $ab );	
		?>
		<script type="text/javascript">
			var id = <?php echo $uid ?>;
			window.open('/cp/profile/?user='+id, '_self');
		</script>
		<?php
	}
} else {
	?><script type="text/javascript">
		alert('Password confirmation did not match. Your information was not saved.');
	</script><?php
}

get_header(); ?>

<div id="primary">
	<form action="" method="post" id="update-user">
		<div id="profile-image">
			<img width="100%" src="http://localhost:8888/wp-content/uploads/2012/06/LuisAugusto2.jpeg">
			Upload a New Image: <input type="file" name="image">
		</div>
		<ul id="user-info">
			<li>
				<div id="label">First Name:</div>
				<input type="text" name="firstname" value="<?php echo $fn ?>" />
				<div id="label">Last Name:</div>
				<input type="text" name="lastname" value="<?php echo $ln ?>" />
			</li>
			<li>
				<div id="label">DJ Name:</div>
				<input type="text" name="djname" value="<?php echo $dj ?>"/>
			</li>
			<li>
				<div id="label">New Password:</div>
				<input type="password" name="password" />
				<div id="label">Confirm:</div>
				<input type="password" name="password-confirm" />
			</li>
			<li>
				<div id="label">Email:</div>
				<input type="text" name="email" value="<?php echo $em ?>" />
				<div id="label">Phone:</div>
				<input type="text" name="phone" value="<?php echo $ph ?>" />
			</li>
			<li>
				<div id="label">About:</div>
				<textarea class="about" cols="50" rows="10" name="about" /><?php echo $ab ?></textarea>
			</li>
			<li>
				<input class="submit-button" style="float:right" value="Save Info" type="submit" name="submit" />
			</li>
		</ul>
	</form>
</div>

<?php include (TEMPLATEPATH . '/cp-footer.php'); ?>

