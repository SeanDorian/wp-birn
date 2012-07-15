<?php
	
	error_reporting(0);
	if ( $returnCSS ) {
		ob_start();
	} else {
		header("Content-type: text/css; charset: UTF-8");
		/** 
		 * Find wp-load.php
		 * defualt location from this directory is 
		 * "../../../../../wp-load.php"
		 *
		if ( file_exists( '../../../../../wp-load.php' ) ) {
		 */
			require_once( '../../../../../wp-load.php' );
		 /*
		}
		 */
	}
	global $wpdb, $custom_login;
?>
/**
 * Custom Login
 * @use: custom_login_get_setting( '' );
 */

/* Start Custom user input */
<?php echo wp_specialchars_decode( stripslashes( custom_login_get_setting( 'custom_css' ) ), 1, 0, 1 ) . "\n\n"; ?>
/* End custom user input */

/* html background */
html {
	background: <?php if ( custom_login_get_setting( 'html_background_color' ) != '' ) echo custom_login_get_setting( 'html_background_color' ); if ( custom_login_get_setting( 'html_background_url' ) != '' ) { ?> url( '<?php echo custom_login_get_setting( 'html_background_url' ); ?>' ) left top <?php echo custom_login_get_setting( 'html_background_repeat' ); } ?> !important;
}

<?php if ( custom_login_get_setting( 'html_background_url' ) != '' ) { ?>
body.login {
	background: transparent !important;
    }
<?php } ?>

<?php if ( custom_login_get_setting( 'html_border_top_color' ) != '' && !is_version( '3.0' ) ) { ?>
body.login {
	border-top-color: <?php echo custom_login_get_setting( 'html_border_top_color' ); ?>;
}
<?php } ?>

<?php if ( custom_login_get_setting( 'html_border_top_background' ) != '' && !is_version( '3.0' ) ) { ?>
body.login {
	background: transparent url( '<?php echo custom_login_get_setting( 'html_border_top_background' ); ?>' ) left top repeat-x !important;
}
<?php } ?>

/* Diplays the custom graphics for the login screen*/
#login form {
<?php if ( custom_login_get_setting( 'login_form_background_color' ) != '' ) { ?>
    background: <?php echo custom_login_get_setting( 'login_form_background_color' ); ?> url( '<?php echo custom_login_get_setting( 'login_form_background' ); ?>' ) center top no-repeat;
<?php } else { ?>
    background: transparent url( '<?php echo custom_login_get_setting( 'login_form_background' ); ?>' ) center top no-repeat;
<?php } ?>
<?php if ( custom_login_get_setting( 'login_form_padding_top' ) == true ) { ?>
	padding-top:20px; }
<?php } else { ?>
	padding-top:100px; }
<?php } ?>   

/* Hides the default Wordpress Login content*/
#login form {
    /* border radius */
    -moz-border-radius: <?php echo custom_login_get_setting( 'login_form_border_radius' ); ?>px;
    -khtml-border-radius: <?php echo custom_login_get_setting( 'login_form_border_radius' ); ?>px;
    -webkit-border-radius: <?php echo custom_login_get_setting( 'login_form_border_radius' ); ?>px;
    border-radius: <?php echo custom_login_get_setting( 'login_form_border_radius' ); ?>px;
    /* border */
    border: <?php echo custom_login_get_setting( 'login_form_border' ); ?>px solid <?php echo custom_login_get_setting( 'login_form_border_color' ); ?>;
    /* box shadow */
    -moz-box-shadow: <?php echo custom_login_get_setting( 'login_form_box_shadow_1' ); ?>px <?php echo custom_login_get_setting( 'login_form_box_shadow_2' ); ?>px <?php echo custom_login_get_setting( 'login_form_box_shadow_3' ); ?>px <?php echo custom_login_get_setting( 'login_form_box_shadow_4' ); ?>;
    -webkit-box-shadow: <?php echo custom_login_get_setting( 'login_form_box_shadow_1' ); ?>px <?php echo custom_login_get_setting( 'login_form_box_shadow_2' ); ?>px <?php echo custom_login_get_setting( 'login_form_box_shadow_3' ); ?>px <?php echo custom_login_get_setting( 'login_form_box_shadow_4' ); ?>;
    -khtml-box-shadow: <?php echo custom_login_get_setting( 'login_form_box_shadow_1' ); ?>px <?php echo custom_login_get_setting( 'login_form_box_shadow_2' ); ?>px <?php echo custom_login_get_setting( 'login_form_box_shadow_3' ); ?>px <?php echo custom_login_get_setting( 'login_form_box_shadow_4' ); ?>;
    box-shadow: <?php echo custom_login_get_setting( 'login_form_box_shadow_1' ); ?>px <?php echo custom_login_get_setting( 'login_form_box_shadow_2' ); ?>px <?php echo custom_login_get_setting( 'login_form_box_shadow_3' ); ?>px <?php echo custom_login_get_setting( 'login_form_box_shadow_4' ); ?>;
}

<?php if ( custom_login_get_setting( 'login_form_logo' ) == '' ) { ?>
#login h1 {
	display: none;
}
<?php } if ( custom_login_get_setting( 'login_form_logo' ) != '' ) { ?>
.login h1 a {
	background: url( '<?php echo custom_login_get_setting( 'login_form_logo' ); ?>' ) no-repeat scroll center top transparent;
}
<?php } ?>

label {
	color: <?php echo custom_login_get_setting( 'label_color' ); ?> !important;
}

<?php
	if ( $returnCSS ) {
		$css = ob_get_clean();
	}
?>