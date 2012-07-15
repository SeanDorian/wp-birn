<?php
/**
 * Template Name: CP Edit Shows
 * Description: Show Edit Page for CP
 */
if ($_GET['admin'] == true && $permission == 3) {
	$show = $_GET['show'];
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

