<?php
/**
 * Theme header for the CP. Loads CSS and jQuery.
 * Displays all of the <head> section and everything up till <div id="main">
 */
?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->

<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="/wp-content/themes/birn-maintheme-1.1/cp-style.css" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */

	wp_head();
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
		if ($role->Role_ID == 10) {
			$permission = -1;
			break;
		} else if ($role->Role_ID >= 8) {
			$permission = 0;
			break;
		} else if ($role->Role_ID >= 6) {
			$permission = 1;
			break;	
		} else if ($role->Role_ID >= 5) {
			$permission = 2;
			break;
		} else if ($role->Role_ID >= 4) {
			$permission = 2.5;
			break;
		} else if ($role->Role_ID >= 1) {
			$permission = 3;
			break;
		}
	}

?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>-->
<script src="/jquery-ui.min.js" type="text/javascript"></script>
<!--<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css" rel="stylesheet" />-->
<link type="text/css" href="/jquery-ui.css" rel="stylesheet" />

</head>
<body>
<div id="mask"></div><!--Used to create modals-->
<div id="main-body">
		<header id="cp-header">
				<a class="cp-logo" href="/cp">[control panel]</a>
				<div id="welcome-text">
					<?php
				    	echo 'Welcome back, ' . $current_user->user_firstname . "!<br>";
						echo 'You have 0 Notifications'
					?>
				</div>
				<div class="user-img">
						
				</div>
				<div id="secondary-menu" class="gray-box">	
					<a href="/cp/profiles/?user=<?php echo $current_user->id ?>">Your Profile</a>
					<a href="/cp/manage-shows">Your Show(s)</a>
					<a href="<?php echo wp_logout_url(); ?>" title="Logout">Logout</a>
				</div>					
				<div id="cp-menu">
					<a class="cp-menu" href="/cp">Main</a>|
					<a class="cp-menu" href="/cp/production">Production</a>|
					<a class="cp-menu" href="/cp/members">Members</a>|
					<a class="cp-menu" href="/cp/shows">Shows</a>|
					<a class="cp-menu" href="/cp/resources">Resources</a>|
					<a class="cp-menu" href="/cp/problem-reports">Problem Reports</a>
					<?php
				 		if ($permission >= 2){
							echo "| <a class='cp-menu' href='/cp/manage'>Manage</a>";
						}
					?>
				</div>
		</header>

		<div id="main">
			