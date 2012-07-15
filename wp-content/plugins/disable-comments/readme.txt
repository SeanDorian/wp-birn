=== Disable Comments ===
Contributors: solarissmoke
Tags: comments, disable, global
Requires at least: 3.2
Tested up to: 3.4
Stable tag: 0.4

Allows administrators to globally disable comments on their site. Comments can be disabled according to post type.

== Description ==

This plugin allows administrators to globally disable comments on any post type (posts, pages, attachments, etc.) so that these settings cannot be overridden for individual posts. It also removes all comment-related fields from edit and quick-edit screens.

Additionally, comment-related items can be removed from the Dashboard, Widgets, the Admin Menu and the Admin Bar.

If you come across any bugs or have suggestions, please contact me at [rayofsolaris.net](http://rayofsolaris.net). Please check the [FAQs](http://rayofsolaris.net/code/disable-comments-for-wordpress#faq) for common issues.

== Changelog ==

= 0.5 =
* Allow temporary disabling of comments site-wide by ensuring that original comment statuses are not overwritten when a post is edited.

= 0.4 =
* Added the option to disable the Recent Comments template widget.
* Bugfix: don't show admin messages to users who don't can't do anything about them.

= 0.3.5 =
* Bugfix: Other admin menu items could inadvertently be hidden when 'Remove the "Comments" link from the Admin Menu' was selected.

= 0.3.4 =
* Bugfix: A typo on the settings page meant that the submit button went missing on some browsers. Thanks to Wojtek for reporting this.

= 0.3.3 =
* Bugfix: Custom post types which don't support comments shouldn't appear on the settings page
* Add warning notice to Discussion settings when comments are disabled

= 0.3.2 =
* Bugfix: Some dashboard items were incorrectly hidden in multisite

= 0.3.1 =
* Compatibility fix for WordPress 3.3

= 0.3 =
* Added the ability to remove links to comment admin pages from the Dashboard, Admin Bar and Admin Menu

= 0.2.1 =
* Usability improvements to help first-time users configure the plugin.

= 0.2 =
* Bugfix: Make sure pingbacks are also prevented when comments are disabled.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The plugin settings can be accessed via the 'Settings' menu in the administration area
 
