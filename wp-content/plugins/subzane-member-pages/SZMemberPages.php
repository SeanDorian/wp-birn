<?php
/*  Copyright 2011  Andreas Norman

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Generalx Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

		Plugin Name: Norman Member Pages
		Plugin URI: http://www.andreasnorman.se/norman-member-pages/
		Description: SubZane Member Pages is a free membership management system for WordPress&reg; that restricts content to registered users.
		Author: Andreas Norman
		Version: 0.8
		Author URI: http://www.andreasnorman.se
*/

class SubZaneMemberPagesPlugin {
	public $pluginPath;
	public $pluginUrl;
	
	public function __construct() {
		$this->pluginPath = dirname(__FILE__);
		$this->pluginUrl = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

		add_action('add_meta_boxes', array($this, 'add_metaboxes') );
		add_action('save_post', array($this, 'save') );
		add_action('admin_init', array($this, 'add_listinfo'));
		add_action('admin_init', array($this, 'settings_init'));
		add_action('admin_head', array($this, 'admin_css'));
		add_action('admin_menu', array($this, 'admin_add_page'));
		add_action('get_header', array($this, 'hasPermission'));
		add_action('wp_head', array($this, 'head'));
	}
	
	function head() {
		if (isset($_GET['no_auth'])) {
		?>
		<script type="text/javascript" charset="utf-8">
			alert('You have no authorization to view that page and have been redirected to the home page.');
		</script>
		<?php
		}
	}

	function admin_add_page() {
		add_users_page('Norman Member Pages', 'Norman Member Pages', 'manage_options', 'subzane_member_pages_page', array($this, 'site_settings_page'));
	}

	function settings_init() {
		add_settings_section('settings_main', 'General settings for Norman Member Pages', array($this, 'settings_main_text'), 'szmemberpages_settings');
		add_settings_field('enable_restrictions', 'Enable restrictions', array($this, 'enable_restrictions_text'), 'szmemberpages_settings', 'settings_main');
		add_settings_field('typeof_restriction', 'Base restriction on', array($this, 'typeof_restriction_text'), 'szmemberpages_settings', 'settings_main');
		add_settings_field('user_roles', 'User roles', array($this, 'user_roles_text'), 'szmemberpages_settings', 'settings_main');
		add_settings_section('settings_footer', '', array($this, 'settings_footer_text'), 'szmemberpages_settings');
		register_setting('settings','settings');
	}	

	function admin_css() {
	?>
	<style type="text/css">
		#szmp { width: 200px; }
	</style>
	<?php	
	}	
	
	function add_listinfo() {
		add_filter('manage_pages_columns', array($this, 'szmp_column') );
		add_action('manage_pages_custom_column', array($this, 'szmp_value'), 10, 2);
	}
	
	function szmp_column($cols) {
		$cols['szmp'] = 'Permissions';
		return $cols;
	}

	function szmp_value($column_name, $id) {
		$settings = get_option('settings');
		if ($column_name == 'szmp') {
			if ($settings['typeof_restriction'] == 'users') {
				$restriction_users = get_post_meta($id, 'SubZaneMemberPages_users', true);
				$selected_users = explode(';', $restriction_users);
				if (!empty($selected_users[0])) {
					foreach ($selected_users as $user_id) {
						$ud = get_userdata( $user_id );
						echo '- <b>'.$ud->display_name.' ('.$ud->user_nicename.')</b><br>';
					}
				} else {
					echo '<i>All allowed</i>';
				}
			if ($settings['typeof_restriction'] == 'roles') {
					$restriction_roles = get_post_meta($id, 'SubZaneMemberPages_roles', true);
					$selected_roles = explode(';', $restriction_roles);
					if (!empty($selected_roles[0])) {
						foreach ($selected_roles as $role) {
							echo '- <b>'.$role.'</b><br>';
						}
					} else {
						echo '<i>All allowed</i>';
					}
				}
			} else {
				$restriction_roles = get_post_meta($id, 'SubZaneMemberPages_roles', true);
				$selected_roles = explode(';', $restriction_roles);

				$restriction_users = get_post_meta($id, 'SubZaneMemberPages_users', true);
				$selected_users = explode(';', $restriction_users);

				if (!empty($selected_roles[0]) || !empty($selected_users[0])) {
					if (!empty($selected_roles[0])) {
						echo '<b>Roles:</b><br/>';
						foreach ($selected_roles as $role) {
							echo '- '.$role.'<br/>';
						}
					}
					
					if (!empty($selected_users[0])) {
						echo '<b>Users:</b><br/>';
						foreach ($selected_users as $user_id) {
							$ud = get_userdata( $user_id );
							echo '- '.$ud->display_name.' ('.$ud->user_nicename.')<br>';
						}
					}
				} else {
					echo '<i>All allowed</i>';
				}
			}
		}
	}

	function add_metaboxes() {
		$settings = get_option('settings'); 
		if ($settings['typeof_restriction'] == 'users') {
			add_meta_box('SubZaneMemberPages', 'SubZane Member Pages', array($this, 'metabox_users'), 'page');
		} else if ($settings['typeof_restriction'] == 'roles') {
			add_meta_box('SubZaneMemberPages', 'SubZane Member Pages', array($this, 'metabox_roles'), 'page');
		} else {
			add_meta_box('SubZaneMemberPages', 'SubZane Member Pages', array($this, 'metabox_both'), 'page');
		}

	}
	
	function metabox_both() {
		global $post, $wp_roles;
		$settings = get_option('settings'); 

		if ($settings['enable_restrictions'] == 1) {
			$all_users = get_users();
			$all_roles = $wp_roles->roles;
		  wp_nonce_field( plugin_basename( __FILE__ ), 'SubZaneMemberPages_noncename' );
			$restriction_users = get_post_meta($post->ID, 'SubZaneMemberPages_users', true);
			$SubZaneMemberPages_recursive = get_post_meta($post->ID, 'SubZaneMemberPages_recursive', true);
			$selected_users = explode(';', $restriction_users);
			
			$restriction_roles = get_post_meta($post->ID, 'SubZaneMemberPages_roles', true);
			$SubZaneMemberPages_recursive = get_post_meta($post->ID, 'SubZaneMemberPages_recursive', true);
			$selected_roles = explode(';', $restriction_roles);
			
			?>
			<script type="text/javascript" charset="utf-8">
			jQuery(document).ready( function($) {
				$("#lbl_No_restriction").click(function() {
					if ($(this).is(':checked')) {
						$(".users_checkboxes").attr('checked', false);
						$(".roles_checkboxes").attr('checked', false);
					}
				});

				$(".users_checkboxes").click(function() {
					$("#lbl_No_restriction").attr('checked', false);
				});

				$(".roles_checkboxes").click(function() {
					$("#lbl_No_restriction").attr('checked', false);
				});
			});	
			</script>
			<p>Select all users that should have access to this page.</p>
			<p><input type="checkbox" name="all" value="all" id="lbl_No_restriction" <?php if ($selected_users[0] == '') {echo 'checked="checked"';} ?>> <label for="lbl_No_restriction"><b>No restrictions</b></label></p>
			<hr />
			<h4>Roles</h4>
			<?php
			foreach ($all_roles as $role) {
			?>
			<p><input <?php if (in_array($role['name'], $selected_roles)) {echo 'checked="checked"';} ?> class="roles_checkboxes" type="checkbox" name="SubZaneMemberPages_roles[]" value="<?php echo $role['name'] ?>" id="lbl<?php echo $role['name'] ?>"> <label for="lbl<?php echo $role['name'] ?>"><?php echo $role['name'] ?></label></p>
			<?php
			}
			?>
			<hr />
			<h4>Users</h4>
			<?php
			foreach ($all_users as $user) {
			?>
			<p><input <?php if (in_array($user->ID, $selected_users)) {echo 'checked="checked"';} ?> class="users_checkboxes" type="checkbox" name="SubZaneMemberPages_users[]" value="<?php echo $user->ID ?>" id="lbl<?php echo $user->ID ?>"> <label for="lbl<?php echo $user->ID ?>"><?php echo $user->display_name ?> (<?php echo $user->user_nicename ?>)</label></p>
			<?php
			}
			?>
			<hr />
			<p><input <?php if ($SubZaneMemberPages_recursive == 1) {echo 'checked="checked"';} ?> type="checkbox" name="SubZaneMemberPages_recursive" value="1" id="lbl_recursive"> <label for="lbl_recursive"><b>Recursive</b> (<em>All children of this page will inherit the same restrictions.</em>)</label></p>
			<?php
		} else {
		?>
		<p><em>You'll need to <a href="users.php?page=subzane_member_pages_page">enable restrictions</a> in the plugin settings to use this</em></p>
		<?php
		}
	}

	function metabox_users() {
		global $post, $wp_roles;
		$settings = get_option('settings'); 

		if ($settings['enable_restrictions'] == 1) {
			$all_users = get_users();
		  wp_nonce_field( plugin_basename( __FILE__ ), 'SubZaneMemberPages_noncename' );
			$restriction_users = get_post_meta($post->ID, 'SubZaneMemberPages_users', true);
			$SubZaneMemberPages_recursive = get_post_meta($post->ID, 'SubZaneMemberPages_recursive', true);
			$selected_users = explode(';', $restriction_users);
			?>
			<script type="text/javascript" charset="utf-8">
			jQuery(document).ready( function($) {
				$("#lbl_No_restriction").click(function() {
					if ($(this).is(':checked')) {
						$(".users_checkboxes").attr('checked', false);
					}
				});

				$(".users_checkboxes").click(function() {
					$("#lbl_No_restriction").attr('checked', false);
				});
			});	
			</script>
			<p>Select all users that should have access to this page.</p>
			<p><input type="checkbox" name="all" value="all" id="lbl_No_restriction" <?php if ($selected_users[0] == '') {echo 'checked="checked"';} ?>> <label for="lbl_No_restriction"><b>No restrictions</b></label></p>
			<hr />
			<?php
			foreach ($all_users as $user) {
			?>
			<p><input <?php if (in_array($user->ID, $selected_users)) {echo 'checked="checked"';} ?> class="users_checkboxes" type="checkbox" name="SubZaneMemberPages_users[]" value="<?php echo $user->ID ?>" id="lbl<?php echo $user->ID ?>"> <label for="lbl<?php echo $user->ID ?>"><?php echo $user->display_name ?> (<?php echo $user->user_nicename ?>)</label></p>
			<?php
			}
			?>
			<hr />
			<p><input <?php if ($SubZaneMemberPages_recursive == 1) {echo 'checked="checked"';} ?> type="checkbox" name="SubZaneMemberPages_recursive" value="1" id="lbl_recursive"> <label for="lbl_recursive"><b>Recursive</b> (<em>All children of this page will inherit the same restrictions.</em>)</label></p>
			<?php
		} else {
		?>
		<p><em>You'll need to <a href="users.php?page=subzane_member_pages_page">enable restrictions</a> in the plugin settings to use this</em></p>
		<?php
		}
	}
	
	function metabox_roles() {
		global $post, $wp_roles;
		$settings = get_option('settings'); 

		if ($settings['enable_restrictions'] == 1) {
			$all_roles = $wp_roles->roles;
		  wp_nonce_field( plugin_basename( __FILE__ ), 'SubZaneMemberPages_noncename' );
			$restriction_roles = get_post_meta($post->ID, 'SubZaneMemberPages_roles', true);
			$SubZaneMemberPages_recursive = get_post_meta($post->ID, 'SubZaneMemberPages_recursive', true);
			$selected_roles = explode(';', $restriction_roles);
			?>
			<script type="text/javascript" charset="utf-8">
			jQuery(document).ready( function($) {
				$("#lbl_No_restriction").click(function() {
					if ($(this).is(':checked')) {
						$(".roles_checkboxes").attr('checked', false);
					}
				});

				$(".roles_checkboxes").click(function() {
					$("#lbl_No_restriction").attr('checked', false);
				});
			});	
			</script>
			<p>Select all user roles that should have access to this page.</p>
			<p><input type="checkbox" name="all" value="all" id="lbl_No_restriction" <?php if ($selected_roles[0] == '') {echo 'checked="checked"';} ?>> <label for="lbl_No_restriction"><b>No restrictions</b></label></p>
			<hr />
			<?php
			foreach ($all_roles as $role) {
			?>
			<p><input <?php if (in_array($role['name'], $selected_roles)) {echo 'checked="checked"';} ?> class="roles_checkboxes" type="checkbox" name="SubZaneMemberPages_roles[]" value="<?php echo $role['name'] ?>" id="lbl<?php echo $role['name'] ?>"> <label for="lbl<?php echo $role['name'] ?>"><?php echo $role['name'] ?></label></p>
			<?php
			}
			?>
			<hr />
			<p><input <?php if ($SubZaneMemberPages_recursive == 1) {echo 'checked="checked"';} ?> type="checkbox" name="SubZaneMemberPages_recursive" value="1" id="lbl_recursive"> <label for="lbl_recursive"><b>Recursive</b> (<em>All children of this page will inherit the same restrictions.</em>)</label></p>
			<?php
		} else {
		?>
		<p><em>You'll need to <a href="users.php?page=subzane_member_pages_page">enable restrictions</a> in the plugin settings to use this</em></p>
		<?php
		}
	}
			
	function save($post_id) {
		$settings = get_option('settings');
	  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	      return;
	  if ( !wp_verify_nonce( $_POST['SubZaneMemberPages_noncename'], plugin_basename( __FILE__ ) ) )
	      return;

	  if ( 'page' == $_POST['post_type'] ) {
	    if ( !current_user_can( 'edit_page', $post_id ) )
	        return;
	  } else {
	    if ( !current_user_can( 'edit_post', $post_id ) )
	        return;
	  }
		
		if ($settings['typeof_restriction'] == 'users' || $settings['typeof_restriction'] == 'both') {
			if (!empty($_POST['SubZaneMemberPages_users'])) {
				$SubZaneMemberPages_users = implode(';', $_POST['SubZaneMemberPages_users']);
			} else {
				$SubZaneMemberPages_users = $_POST['SubZaneMemberPages_users'];
			}
		}

		if ($settings['typeof_restriction'] == 'roles' || $settings['typeof_restriction'] == 'both') {
			if (!empty($_POST['SubZaneMemberPages_roles'])) {
				$SubZaneMemberPages_roles = implode(';', $_POST['SubZaneMemberPages_roles']);
			} else {
				$SubZaneMemberPages_roles = $_POST['SubZaneMemberPages_roles'];
			}
		}
		
		if ($settings['typeof_restriction'] == 'users') {
			if (isset($_POST['SubZaneMemberPages_recursive'])) {
				$args = array('child_of' => $post_id);
				$children = get_pages( $args );
				foreach ($children as $child) {
					$this->update_custom_meta($child->ID, $SubZaneMemberPages_users, 'SubZaneMemberPages_roles');
				}
			}
			$this->update_custom_meta($post_id, $SubZaneMemberPages_users, 'SubZaneMemberPages_users');
			$this->update_custom_meta($post_id, $_POST['SubZaneMemberPages_recursive'], 'SubZaneMemberPages_recursive');
		} else if ($settings['typeof_restriction'] == 'roles') {
			if (isset($_POST['SubZaneMemberPages_recursive'])) {
				$args = array('child_of' => $post_id);
				$children = get_pages( $args );
				foreach ($children as $child) {
					$this->update_custom_meta($child->ID, $SubZaneMemberPages_roles, 'SubZaneMemberPages_roles');
				}
			}
			$this->update_custom_meta($post_id, $SubZaneMemberPages_roles, 'SubZaneMemberPages_roles');
			$this->update_custom_meta($post_id, $_POST['SubZaneMemberPages_recursive'], 'SubZaneMemberPages_recursive');
		} else {
			if (isset($_POST['SubZaneMemberPages_recursive'])) {
				$args = array('child_of' => $post_id);
				$children = get_pages( $args );
				foreach ($children as $child) {
					$this->update_custom_meta($child->ID, $SubZaneMemberPages_users, 'SubZaneMemberPages_users');
					$this->update_custom_meta($child->ID, $SubZaneMemberPages_roles, 'SubZaneMemberPages_roles');
				}
			}

			$this->update_custom_meta($post_id, $SubZaneMemberPages_roles, 'SubZaneMemberPages_roles');
			$this->update_custom_meta($post_id, $_POST['SubZaneMemberPages_recursive'], 'SubZaneMemberPages_recursive');
			$this->update_custom_meta($post_id, $SubZaneMemberPages_users, 'SubZaneMemberPages_users');
			$this->update_custom_meta($post_id, $_POST['SubZaneMemberPages_recursive'], 'SubZaneMemberPages_recursive');
		}
	}

	function update_custom_meta($postID, $newvalue, $field_name) {
		if (!get_post_meta($postID, $field_name)) {
			add_post_meta($postID, $field_name, $newvalue);
		} else {
			update_post_meta($postID, $field_name, $newvalue);
		}
	}		
	
	function hasRestrictions($page_id) {
		$restriction_users = get_post_meta($page_id, 'SubZaneMemberPages_users', true);
		$restriction_roles = get_post_meta($page_id, 'SubZaneMemberPages_roles', true);
		if (empty($restriction_users) && empty($restriction_roles)) {
			return false;
		} else {
			return true;
		}
	}
	
	function hasPermission() {
		$settings = get_option('settings'); 
		if ($settings['enable_restrictions'] == 1) {
			if ($this->hasRestrictions(get_the_ID())) {
				if ($settings['typeof_restriction'] == 'users') {
					$restriction_users = get_post_meta(get_the_ID(), 'SubZaneMemberPages_users', true);
					if (empty($restriction_users)) {
						return true;
					} else {
						$selected_users = explode(';', $restriction_users);
						if (count($selected_users) <= 0) {
							return true;
						} else {
							if (is_user_logged_in()) {
								$current_user = wp_get_current_user();
								foreach ($selected_users as $user_id) {
									if ($user_id == $current_user->ID) {
										return true;
									}
								}
								wp_redirect( home_url().'?no_auth=1' );
								exit;
							} else {
								wp_redirect( wp_login_url( get_permalink() ) );
								exit;
							}
						}
					}
				} else if ($settings['typeof_restriction'] == 'roles') {
					$restriction_roles = get_post_meta(get_the_ID(), 'SubZaneMemberPages_roles', true);
					if (empty($restriction_roles)) {
						return true;
					} else {
						$selected_roles = explode(';', $restriction_roles);
						if (count($selected_roles) <= 0) {
							return true;
						} else {
							if (is_user_logged_in()) {
								foreach ($selected_roles as $role) {
									if (current_user_can(strtolower($role))) {
										return true;
									}
								}
								wp_redirect( home_url().'?no_auth=1' );
								exit;
							} else {
								wp_redirect( wp_login_url( get_permalink() ) );
								exit;
							}
						}
					}
				} else {
					$restriction_roles = get_post_meta(get_the_ID(), 'SubZaneMemberPages_roles', true);
					$restriction_users = get_post_meta(get_the_ID(), 'SubZaneMemberPages_users', true);
					if (empty($restriction_roles) && empty($restriction_users)) {
						return true;
					} else {
						$selected_roles = explode(';', $restriction_roles);
						$selected_users = explode(';', $restriction_users);
						if (count($selected_roles) <= 0 && count($selected_users) <= 0) {
							return true;
						} else {
							if (is_user_logged_in()) {
								foreach ($selected_roles as $role) {
									if (current_user_can(strtolower($role))) {
										return true;
									}
								}
								$current_user = wp_get_current_user();
								foreach ($selected_users as $user_id) {
									if ($user_id == $current_user->ID) {
										return true;
									}
								}
								wp_redirect( home_url().'?no_auth=1' );
								exit;
							} else {
								wp_redirect( wp_login_url( get_permalink() ) );
								exit;
							}
						}
					}
				}
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
	
	function enable_restrictions_text() {
		$settings = get_option('settings');
		?>
		<input id="settings[enable_restrictions]" name="settings[enable_restrictions]" size="40" type="checkbox" value="1" <?php echo ($settings['enable_restrictions']==1?'checked="checked"':''); ?> />
		<span class="description">Enable or disable the restrictions.</span>
		<?php		
	}
	
	function typeof_restriction_text() {
		$settings = get_option('settings');
		?>
		<input id="szmp_users" name="settings[typeof_restriction]" size="40" type="radio" value="users" <?php echo ($settings['typeof_restriction']=='users'?'checked="checked"':''); ?> /> <label for="szmp_users">Users</label><br/>
		<input id="szmp_roles" name="settings[typeof_restriction]" size="40" type="radio" value="roles" <?php echo ($settings['typeof_restriction']=='roles'?'checked="checked"':''); ?> /> <label for="szmp_roles">Roles</label><br/>
		<input id="szmp_both" name="settings[typeof_restriction]" size="40" type="radio" value="both" <?php echo ($settings['typeof_restriction']=='both'?'checked="checked"':''); ?> /> <label for="szmp_both">Both</label>
		<?php		
	}
	
	function user_roles_text() {
		?>
		To add and configure new User Roles you could install plugins such as <a href="http://wordpress.org/extend/plugins/user-role-editor/">User Role Editor</a>. It's free and works great together with SubZane Member Pages.
		<?php
	}
	
	function settings_main_text() {}
	
	function settings_footer_text() {
		?>
		<h2>Need Support?</h2>
		<p>For questions, issues or feature requests, please post them in the <a href="http://wordpress.org/tags/SubZane-Member-Pages?forum_id=10">WordPress Forum</a> and make sure to tag the post with "Norman-Member-Pages".</p>
		<h2>Like To Contribute?</h2>
		<p>If you would like to contribute, the following is a list of ways you can help:</p>
		<ul>
			<li>» Blog about or link to SubZane YouTube Plugin so others can find out about it</li>
			<li>» Report issues, provide feedback, request features, etc.</li>
			<li>» <a href="http://wordpress.org/extend/plugins/SubZane-Member-Pages/">Rate the plugin on the WordPress Plugins Page</a></li>
			<li>» <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LH7UZV983QMWC">Make a donation</a></li>
		</ul>
		<h2>Other Links</h2>
		<ul>
			<li>» <a href="http://twitter.com/andreasnorman">@andreasnorman</a> on Twitter</li>
			<li>» <a href="http://www.andreasnorman.se">andreasnorman.se</a></li>
			<li>» <a href="http://www.andreasnorman.se/norman-member-pages/">Norman Member Pages on andreasnorman.se</a></li>
		</ul>
		</div>
		<?php	
	}
	
	function site_settings_page(){
		?><div class="wrap">
			<h2>SubZane Member Pages</h2>
			<form method="post" action="options.php">
				<?php settings_fields('settings'); ?>
				<?php do_settings_sections('szmemberpages_settings'); ?>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"  /></p>
			</form>
		</div><!--END wrap-->
		<?php
	}	
	
}

$SubZaneMemberPagesPlugin = new SubZaneMemberPagesPlugin();

?>