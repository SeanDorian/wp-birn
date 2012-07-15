<?php
/**
 * Theme header. Displays BIRN Logo, Banner, and Menu, as well as loads CSS.
 * Displays all of the <head> section and everything up until <div id="main">
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
<!--[if !(IE 6
IE 7) | !(IE 8)  ]><!-->
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
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
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
?>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/scripts.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/smoothness/jquery-ui.css" rel="stylesheet" />
</head>

<body id="site-body">
	<div id="site-page">
		<div id="main-header">
			
			<div id="header-banner">
				<?php if (function_exists("easing_slider")){ easing_slider(); }; ?>
				<div id="logo">
					<a href="http://www.thebirn.com"><img src="/wp-content/uploads/2012/04/birn-header1.png"/></a>
				</div>

				<div id="stream-links">
					<div class="expand2"><a onclick="window.open('/listen/birn-presents','','width=540,height=500,left=0,top=0');">BIRN Presents [Red Room Concerts]</a></div>
					<div class="expand"><a onclick="window.open('/listen/birn-5','','width=460,height=500,left=0,top=0');">BIRN 5 [International Network]</a></div>
					<div class="expand"><a onclick="window.open('/listen/birn-4','','width=460,height=500,left=0,top=0');">BIRN 4 [Famous Alumni Recordings]</a></div>
					<div class="expand"><a onclick="window.open('/listen/birn-3','','width=460,height=500,left=0,top=0');">BIRN 3 [Alumni News &amp; Music]</a></div>
					<div class="expand"><a onclick="window.open('/listen/birn-2','','width=460,height=500,left=0,top=0');">BIRN 2 [Concerts, Clinics, &amp; More]</a></div>
					<div class="expand"><a onclick="window.open('/listen/birn-1','','width=460,height=500,left=0,top=0');">BIRN 1 [Student Radio]</a></div>
					<a class="listen" >Listen:</a>			
				</div>

				<div id="webcam">
					<img class="webcam" src="/wp-content/uploads/2012/05/webcam-e1336421564152.png"><br>
					<a class="room1" onclick="window.open('webcam-1','','width=450,height=350,left=800,top=130');">Broadcast</a>
					<a class="room2" onclick="window.open('webcam-2','','width=450,height=350,left=800,top=130');">Live Room</a>
				</div>
			</div>
			
			<div class="main-menu">
				<a href="/">Home</a> | <a  href="/calendar">Schedule</a> | <a  href="/about-the-birn">Playlist</a> | <a  href="/about-the-birn">About</a> | <a  href="/contact">Contact</a>
			</div>

		</div><!--Main Header--> 
		<div id="main-site-content">
		
