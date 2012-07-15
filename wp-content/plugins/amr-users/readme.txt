=== amr users ===
Contributors: anmari
Tags: user, users, reports, lists, stats, statistics, members, membership, authors, subscribers, post counts, comment counts, csv, export, search
Version: 3.3.6
Requires at least: 2.7 
Tested up to: 3.3.1
Stable tag: 3.3.6

User listings, member directories, search, filter, export.  Digs deep into data created by other plugins to produce a unified user listings.  Add-ons available for integration with other data tables. 

== Description ==
Configurable user listings by meta keys and values, comment count and post count. Includes User Search, bulk actions like delete, configurable action links, display, inclusion, exclusion, sorting configuration and an option to export to CSV.  Make some lists public to use in with a shortcode.  You must ensure that you suitably define the fields, lists and protection for the shortcode to manage your users privacy.

For more information, please see the [user list plugin website](http://wpusersplugin.com/) 

Some lists pre-configured to get you going - you can add more or change these. You must have some data to see the fields.  In the Screenshots you may see data from subscribe 2, register plus and your members plugins.

Cacheing used to improve the response for large user sites.  Cache's will be updated on update of user records, or by cron job, or on manual request.

Addons are available for special requirements (subscribe2 integration, cimy extra fields interation, multi site)  Want more? please post a detailed feature request.

You may also be interested in amr-user-templates [a wordpress user admin screens plugin](http://webdesign.anmari.com/plugins/amr-user-templates/) 
Simplify the  admin screens (dashboard boxes, screen options etc) of any new users (or reset existing) by role. 

Please check your system meets the following requirements:

*	PHP > 5.2 

*	The filter extension is enabled by default as of PHP 5.2.0 http://au.php.net/manual/en/filter.installation.php

* 	The DateTime Class enabled (should be in php 5.2) http://php.net/manual/en/function.date-create.php


== Changelog ==
= Version 3.3.6 =
*   Fix: a bug in 'including' ie filtering users with some user meta fields
*   Fix: foreign characters will work in before/after fields
*   Fix: cached html was preventing sorting when cache in use.  Transient Cached html will not be used when sorting
*   Fix: Delete uninstall had a bug - fixed
*   Add: More enabling for custom navigation like alphabetical navigation coming soon in 'plus'
*   Change: Moved out of the 'general' tab, the stats section that check queries against your db.  Large db's on slow or low memory sites may have problems when testing the stats.  This can make access to settings awkward.

= Version 3.3.5 =
*   Dummy update to see if wp will update the version number
*   Includes provisional code for custom or alphabetical navigation available soon in amr-users-plus

= Version 3.3.4 =
*   Add: Some changes for better multisite integration if amr-users-multisite available
*   Add: some html cacheing using transients for initial display only
*   Fix: pagination on filtering, searching

= Version 3.3.3 =
*   Fix: if using amr-users-plus-ym addon, and had ym fields then checkbox was aimed at ym, not bulk delete - now will cope with either.
*   Fix: removed ability to hide 'ID' from configuration.  If someone hid the id, then checkboxes etc do not function.  Id need not be displayed, but must be available.

= Version 3.3.2 =
*   Fix: multi level sorting went a bit loopy after a change for php 5.3.  Fixed.  Can now sort up to 5 levels deep.
*   Change: main settings - the info boxes will be closed on initial viewing
*   Enable: ability to do ym (your members) plugin updates in bulk from a bulk selection in list added.  This appears if there is a ym field in the user list. Requires the [amr-users-plus-ym addon](http://wpusersplugin.com/related-plugins/amr-users-plus-ym/) .

= Version 3.3.1 =
*   A version upgrade check and a Deactivate will now drop the cache and logging tables.  Don't panic - they will be created again when necessary. This ensures that any table changes required will be done. It also helps reset the autoincrement counter too which was causing some problems in larger, more active sites.
*   Reset all options will also drop tables (resets autoincrement).  
*  	Clear all cache entries will truncate tables (emptying them and resetting the auto increment).
*   Add: added option to switch off auto cache on user update - cron or manual cacheing only.  For sites with lots of user activity and possibly other plugins causing frequent updates.
*   Fix: an incorrect message when no search records were returned
*   Change: flag set so will only do cache update request once per page updates (It can trigger on user update and  meta update - batch update request will only trigger on first one. 

= Version 3.3 =
*   Add: use transients to better prevent runs overlapping
*   Change: changed array sorting to avoid php 5.3 call_user_func_array bug when using using array_multisort
*   Tested: on wp 3.3

= Version 3.2.1 =
*   Add: ability to copy and delete list individually
*   Fix: Refresh link text & overview settings temporarily got stuck - fixed

= Version 3.2 =
*   Added: alternate method to fetch data to hopefully use less memory - tested so far with 8,860 users, 117,839 user meta records and 34 different user meta keys - used 118 Mb, took 4-10 seconds.
*   Change: cache updates now done in batches of 1000 to avoid mysql time errors
*   Change: changes to admin screen to allow quotes, slashes etc.
*   Changes as requested by S2Member users who have multiple choice custom fields (ie an array as the nested value)
*   Changes to reduce memory usage and mysql usage (exclude any nicename excluded mainfields)
*   Add: Changes to allow addition of filter functionality if add on plugins are activated (for filtering or s2 integration)
*   Change: Search tweaked a bit to allow search as 'or' on search terms.
*   Fix: top checkbox will now check all checkbox lines if ticked
*   Fix: bottom bulk action was not working correctly - fixed


= Version 3.1 =
*   Add: custom headings, instead of nicenames.  See edit headings link from configure screen or view screen.
*   Add: improved verion change checking - auto rebuild of nicenames when upgrading.
*   Fix: foreign language characters were displaying strangely in admin before/after fields. fixed.
*   Removed: facebook like inside the plugin - decided I did not 'like' (huh!) that one could get people to like a page that they were not on.  Changed to a link to plugin website.

= Version 3.0 =
*   Add: search within cache.
*   Add: bulk delete from selection
*   Add: filter the list from the url query parameters, hide or show the filter column ?filter=hide&column_name=value
*   Add: front end download of csv file (public reports only)
*   Add: per page specification from list
*   Add: exclude some fields from all list configuration - slight reduction in memory, and simpler lists.
*   Add: memory tracking code when cacheing when WP_DEBUG on
*   Fix: pagination on last page not quite right - fixed.
*   Change: if report not cached will rebuild in realtime immediately, do not have to request

= Version 2.3.14 =
*   removed use of WP_PLUGIN_URL and _DIR as when not defined, other constant setup went slightly wonky.
*   also on some php hosts, the sequence of includes meant that the !function_exists test did not always work as expected.

= Version 2.3.13 =
*   dummy update as it looks like a file previously uploaded was corrupted and svn would not reupdate without a change.

= Version 2.3.12 =
*   add code to trap and handle odd warnings experienced by some users on subscription.

= Version 2.3.11 =
*   tweak to allow pluggable functions so you canformat each field any way you like.  Existing formatting functions are pluggable and you can add your own using the technical field name and prefix with ausers_format_xxxx_xxxx.  See the ausers-pluggable.php file.

= Version 2.3.10 =
*   added code back to cater for wp < 3.1 still using deprecated functions
*   added check for tables required in settings as activation hook in wp does not always fire.   

= Version 2.3.9 =
*   fix to prevent warn ing being issued when all reports rescheduled on user update (allow no list parameter
*   found and replaced another deprecated wp function

= Version 2.3.8 =
*   renamed a function that clashed with another plugin's function
*   change WP_SITEURL to siteurl()
*   removed some deprecated code
*   fixed (i hope) the cacheing status 'in progress' bug that was happening for 2 and 3rd reports 
*   fixed the rss news bug

= Version 2.3.7 =
*   small one line fix for when sorting lists - ie initial sort is fine, but sorting columns when viewing gives error.  Bug was introduced when a change made for php 5.3 users - sorry!

= Version 2.3.6 =
*   A quick fix - need highlighted by Phil wanting a custom gravatar url to produce an img tag.  Before and after html will now cope with double quotes.  Please note if you have special before/after code, this will be in yiour csv file too. A way around this would be to have a separate simpler list for csv export.
*   Now you can build reports on custom post types too!  Amazingly useful for one of my other projects that I'll be telling you about soon.
*   The admin settings screens will now not show a field if it is not switched on, or used for ordering or selection - the page should render faster now. 
*   Option to switch off the css provided - see settings overview


= Version 2.3.5 =
*	Can now deal with content that has quotes etc - add slashes and strip slashes.  And of course foreign characters work too - just make sure you have all your encodings sorted in your wp site and open office or excel. See the plugin site formore info 
*   CSV Filtered option renamed as .txt export option with some other tweaks too - see the hover text.  Aimed at those poor ms excel users... maybe it will help a bit. 
*   Added ability to request regular rebuild of cache for those who have plugins that do not trigger the update of the user profile.   

= Version 2.3.4 =
*	Changed display order to allow decimals and interpret decimals as follows: 3.1 and 3.2 mean the first and second values in the third column.  This should give a lot more flexibility in formatting, although I think still need flexibility in applying links, and in css without having to mdofiy theme etc - it's coming....
*   Added 'before' and 'after' fields so you can add html around the values, especially if combining fields. Eg: a "&nbsp;" or a "<br />".  
*   Bug Fix on sorting, now sort values before applying links etc, and before paginating!

= Version 2.3.3 =
*	Bug Fix - Plays better with S2member now - I'm embarrassed, I'm not even going to tell you what it was.   
*   Fixed a few other minor details that were annoying me - highlight some text on the log screen etc.
*   Add option to rebuild cache for ALL reports in one go.

= Version 2.3.2 =
*	Added option to not have sorting links on the lists.  Specify this next to list name in main settings.
*   If you run lists without configuring nice names, plugin will attempt to make column headings look nice anyway.
*   Bug & Feature Request Tracking Proper Bug notes with adequate detail and Feature Requests may be logged or voted for at [bugs.anmari.com](http://bugs.anmari.com).  the $vote is an indicative amount to indicate how much you want a feature.  

= Version 2.3.1 =
*    fixed bug for versions less than 3.0 that do not have the list-users capability.  User List access also allowed if user has 'edit users' access.
*    switched defaults headings request for shortcode, so that by default headings will be shown.  If you do not want headings user headings=false in the shortcode.
*    Added option to have "carriage returns" filtered out of your csv export lines as requested by [wjm](http://webdesign.anmari.com/exporting-a-wordpress-user-list-to-a-csv-file/#comment-4311)
    
= Version 2.3 =
*    Widget is now available in a rudimentary fashion.  Please ensure that you design reports carefully, not too many columns, and not too many entries.  It is using the same code as the shortcode, without the headings.  Some themes do not take kindly to tables in the sidebar, so please test thoroughly with the size of data and theme you expect to use.
*    Changed capabilities to use new in 3.0 'list_users'.  So now if user can 'manage options' they can configure the reports.  If they can 'list users' they can access the user lists and export to csv too.
*    Fixed 'privacy' bug - an editor or person able to publish posts would have been able to access the user lists via shortcode even if they did not have capability to 'list users'.  Each list now has a public checkbox.  Only 'public' lists may be accessed via the shortcode by people who do not have the 'list users' capability.  If the shortcode requests a non public list, rather than display a visible error,  a comment will be written to the page for your information when testing.
*    Removed forced ID in first column on display - still appears in csv. Is required in cache for additional field functions on display.
*    The user url column will now contain clickable urls if you request that column to be displayed.
*    CSV export link had the wrong hover text, although it did the right action - fixed.
*    Removed the superfluous links at top of view user lists - use the links in the side menu.  These were causing a problem for some people in some browsers.


= Version 2.2.3 =
*    fixed situation where many lists, or long names caused the nav menu to be off the page in some browsers.  Added whitespace: normal to override wordpress admin default styling.  Thanks to wjm for bringing it to my attention and his suggested code. See http://webdesign.anmari.com/exporting-a-wordpress-user-list-to-a-csv-file/comment-page-1/#comment-4311
*    other minor html generation and/or css changes.
*    tested in wp 3.0.  Added some additional "excluded" fields added or changed in wp 3.0 to avoid cluttering up the list of possible fields.  See ameta-includes.php for the list.

= Version 2.2.2 =
*    CSV bug fix - last line was being missed on csv export!


= Version 2.2.1 =
*    Apologies - a little bug got introduced when users do not values in some fields - use version 2.2.1 or 2.1.

= Version 2.2 =
*    Applied a bit more rigour to the code, no major functionality change.
*    Added the limited comment total functionality back with a warning about it's usage - see href="http://webdesign.anmari.com/comment-totals-by-authors/
*    Fixed bug where htmlentities was used instead of htmlspecialchars.  This messed up foreign characters.
*    Added security check that only users who can edit-users may rebuild cache etc. NOTE: there is no seurity check on who can see lists via the shortcode.  If you create a list and make it availble via shortcode, you are responsible for controlling access and/or determining the data displayed.


= Version 2.1 =
*    Fixed bug for people using php < 5.3 (me! too) and who may have had a comma in their user meta data.  The php function str_getcsv does not exist until php 5.3, and  my quick pseudo function did not anticipate commas within the user meta data (bad).  It now does although still a simple function tailored to this specific use.  So it has been renamed and if another plugin has defined a str_getcsv function, (or if using php 5.3 up), then that function will be used.
*    Also ran quick test using a wp 3.0 beta instance and all seems fine. 

= Version 2 =
*   Major change for sites with many users - all reports are prepared in background and cached.  New cache requested after every user update (at this point std user events only).  You can also request your own updates.  Currently no regular cache update set, but most likely this iwll be done in a future version.
*   Background Events are logged for visibility of what caused a cache request.  Log is cleaned up regularly.
*   Cache Status page 
*   'Role' added - this is not actually stored in the user meta tabel, but is 'prepared' or calculated by wordpress.  Many roles are allowed for.  The current version of wordpress just pops the first role up and serves it up as the role.  I have therefore called this 'first role' in case anyone has configured others.  You can of course change the name via the nice names settings.

= Version 1.4.5 =
*   Allowed for less than wordpress 2.8 for non essential plugin news in admin home
*   Allowed for situation where user has not configured 'nicenames' at all

= Version 1.4.4 =
*   Added exclusion of deprecated duplicate fields (eg: wordpress currently returns both user_description and description, regardless of what is in the database. Only the latter is required now).
*   0 post counts and comments will not be listed
*   if plugin wp-stats is enabled and a stats page has been specified in it's settings, then any non zero comment counts will link to a list of comments by the author (Note this only applies to registered users)
*   Fixed problem where updated nice names where not being correctly accessed.

= Version 1.4.3 =
*   Fixed addition of extra lists - now uses prev list as default entries.  NB Must configure and update before viewing.
*   Added RSS feed links to highlight any plugin related news

= Version 1.4.2 =
*   Hmm now using get_bloginfo('wpurl') not get_option!! - otherwise if no WP_SITEURL defined in config, admin loses colours!

= Version 1.4.1 =
*   Defined WP_SITEURL if not defined, using get_bloginfo('wp-url') (not 'siteurl') so both wordpress relocated, and standard wordpress will work.

= Version 1.4 =
*   Changed get_bloginfo('url') to get_bloginfo('siteurl') for those that have wordpress relocated
*   Put the CSV export back - got temporarily lost due to adding possibility of not having it on the front end 
*   Thanks to kiwicam for quick pickup and detailed specific response!

= Version 1.3 =
*   Changed WP_SITEURL to get_bloginfo('url') for those that do not have WP_SITEUEL defined (was in edit link)
*   Added column titles to the csv file
*   Made some updates as suggested by http://planetozh.com/blog/2009/09/wordpress-plugin-competition-2009-43-reviews/.  Note that as we are not running any DB update queries, some of the comments are not strictly relevant.
*   added ability to access a list via shortcode. Your Themes table styling will apply.
*   improved ability to select data - can include only blanks, or exclude if blank.
*   the following fields will automaically be hyperlinked as follows:
*       email address - mailto link
*       user login - edit user link 
*       post count - author archive link

= Version 1.2 =
*   Fixed bug that had crept in where some aspects were not updating in the admin pages
*   Fixed problem with multiple exclusions and inclusions
*   Changed empty to check to null check as 0 is considered empty, but may be a valid inclusion or exclusion value.
*   Changed admin area to separate pages in attempt to simplify the setup.

= Version 1.1 =
*   Allowed for situation where there may be no user meta data for a user record.
*   Tested on 2.8.4

= Version 1.1 =
*   Fixed an inappropriate nonce security check which caused a plugin conflict.

= Version 1 =
*   Initial Listing

== Installation ==

From wordpress admin folder, click add new, search for "amr user", select and install.

OR 

1.  Download and Unzip the folder into your wordpress plugins folder.
2.  Activate the plugin through the 'Plugins' menu in WordPress
3.  You must configure this plugin for your environment:  
4.  Configure or add listings in the settings panel, Configure the nicenames, Configure the lists.
5.  For shortcode, create page or post, enter in text [userlist list=n].  Note some minor css is added - primarily your themes table css will be used.


== Screenshots ==

1. Default list 1
2. Default list 2
3. Default list 3
4. Main Settings Page
5. Configure Nice Names
6. Configure a list
7. CSV Export
8. CSV Imported again!
9. Shortcode simple
10. Shortcode with extras
