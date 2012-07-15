<?php
/**
 * Plugin Name: Custom Login lite
 * Plugin URI: http://austinpassy.com/wordpress-plugins/custom-login
 * Description: A simple way to customize your WordPress login screen! Use the built in, easy to use <a href="./options-general.php?page=custom-login">settings</a> page to do the work for you. So simple a neanderthal can do it! Now featuring a HTML &amp; CSS box for advanced users. Share you designs on <a href="http://flickr.com/groups/custom-login/">Flickr</a> or upgrade to the <a href="http://thefrosty.com/custom-login-pro/">PRO</a> version!
 * Version: 1.0.2
 * Author: Austin Passy
 * Author URI: http://frostywebdesigns.com
 *
 * @copyright 2009 - 2012
 * @author Austin Passy
 * @link http://frostywebdesigns.com/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package CustomLogin
 */

/* Set up the plugin. */
add_action( 'plugins_loaded', 'custom_login_setup' );

/**
 * Sets up the Custom Login plugin and loads files at the appropriate time.
 *
 * @since 0.8
 */
function custom_login_setup() {
	/* Load translations. */
	load_plugin_textdomain( 'custom-login', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/* Set constant path to the Cleaner Gallery plugin directory. */
	define( 'CUSTOM_LOGIN', 'custom-login' );
	define( 'CUSTOM_LOGIN_SETTINGS', 'custom_login_settings' );
	define( 'CUSTOM_LOGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'CUSTOM_LOGIN_ADMIN', CUSTOM_LOGIN_DIR . '/library/admin/' );

	/* Set constant path to the Cleaner Gallery plugin URL. */
	define( 'CUSTOM_LOGIN_URL', plugin_dir_url( __FILE__ ) );
	define( 'CUSTOM_LOGIN_CSS', CUSTOM_LOGIN_URL . 'library/css/' );
	define( 'CUSTOM_LOGIN_JS', CUSTOM_LOGIN_URL . 'library/js/' );

	if ( is_admin() ) {
		require_once( CUSTOM_LOGIN_ADMIN . 'admin.php' );
		
		if ( custom_login_get_setting( 'hide_dashboard' ) != true )
			require_once( CUSTOM_LOGIN_ADMIN . 'dashboard.php' );
	}
	
	/* Add a settings page to the plugin menu */
	add_filter( 'plugin_action_links', 'custom_login_plugin_actions', 10, 2 );
	
	/* Filter in your URL */
	add_filter( 'login_headerurl', 'custom_login_url' );
	/* Filter in your description */
	add_filter( 'login_headertitle', 'custom_login_title' );
	
	/* Load the login head */
	add_action( 'login_head', 'custom_login' );
	//add_action( 'login_head', 'wp_print_scripts' );
	add_action( 'login_head', 'custom_login_html' );

	do_action( 'custom_login_loaded' );
}

/**
 * Replace the defualt link to your URL
 *
 * @since 0.8
 */
function custom_login_url() {
	return get_bloginfo( 'siteurl' );
}

/**
 * Replace the defualt title to your description
 *
 * @since 0.8
 */
function custom_login_title() {
	return get_bloginfo( 'description' );
}

/**
 * Function for quickly grabbing settings for the plugin without having to call get_option() 
 * every time we need a setting.
 *
 * @since 0.8
 */
function custom_login_get_setting( $option = '' ) {
	global $custom_login;

	if ( !$option )
		return false;

	if ( !isset( $custom_login->settings ) )
		$custom_login->settings = get_option( 'custom_login_settings' );

	return $custom_login->settings[$option];
}

/**
 * WordPress 3.x check
 *
 * @since 0.8
 */
if ( !function_exists( 'is_version' ) ) {
	function is_version( $version = '3.0' ) {
		global $wp_version;
		
		if ( version_compare( $wp_version, $version, '<' ) ) {
			return false;
		}
		return true;
	}
}

/**
 * Add stylesheet to the login head
 * @since 0.8
 */
function custom_login() {
	global $custom_login;
	
	echo '<meta name="generator" content="Custom Login" />' . "\n";
	
	wp_register_script( 'gravatar', CUSTOM_LOGIN_JS . 'gravatar.js', array( 'jquery' ), '1.2', false );
	
	if ( ( custom_login_get_setting( 'custom_html' ) != '' || custom_login_get_setting( 'gravatar' ) != false ) && get_option( 'users_can_register' ) ) {
		wp_print_scripts( array( 'jquery', 'gravatar' ) );
	}
	if ( custom_login_get_setting( 'custom_html' ) != '' ) {
		wp_print_scripts( array( 'jquery' ) );
	}
	wp_register_style( 'custom-login-defualt', CUSTOM_LOGIN_CSS . 'custom-login.css', false, 0.8, 'screen' );
	wp_register_style( 'custom-login', CUSTOM_LOGIN_CSS . 'custom-login.css.php', false, 0.8, 'screen' );
	
	if ( custom_login_get_setting( 'custom' ) != false ) {		
		wp_print_styles( 'custom-login' );		
	} else {		
		wp_print_styles( 'custom-login-defualt' );		
	}
}

/**
 * Add html after the body class
 * @since 0.4.6
 */
function custom_login_html() {
	global $custom_login;
	
	if ( custom_login_get_setting( 'custom_html' ) != '' || custom_login_get_setting( 'gravatar' ) != false ) { ?>
<script type='text/javascript'>
//<![CDATA[
jQuery(document).ready(
	function($) {
		<?php if ( custom_login_get_setting( 'custom_html' ) != '' ) { ?>
		$('html body.login').append('<?php echo wp_specialchars_decode( stripslashes( custom_login_get_setting( 'custom_html' ) ), 1, 0, 1 ); ?>');
		<?php } ?>
		
		<?php if ( custom_login_get_setting( 'gravatar' ) != false && get_option( 'users_can_register' ) ) { ?>
		var email = $('#user_email').size();		
		if (email > 0) {
		$('#user_email').parent().parent().css('position','relative');
		$('#user_email').parent().append('<span id="working"></span>');
		$('#user_email').parent().append('<img id="gravatar" style="display:none; position:relative; top:10px; width:37px" />');
		$('#user_email').css('width','80.4%').getGravatar({
			avatarSize: 48,
			start: function(){
				$('#working').fadeIn('fast');
			},
			stop: function(){
				$('#working').fadeOut('slow');
			},
			url: '/includes/get-gravatar.php',
			fallback: 'http://0.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536'
		});
		
		$('#working').hide();
		$('img#gravatar').delay(2000).fadeTo(400,1);
		}
		<?php } ?>
	}
);
//]]>
</script><?php
	}

}

?>