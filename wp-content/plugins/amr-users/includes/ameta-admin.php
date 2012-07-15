<?php
/* This is the amr  admin section file */
function amr_about_users () {
global $wpdb;			
	$wpdb->show_errors();
	if (is_multisite() and is_network_admin()) {
		$where = '';
		$wheremeta = '';
		_e('This is a multi-site network.  All users shown here.');
		echo '<br />';
	}
	else { $where = ' INNER JOIN ' . $wpdb->usermeta .  
       ' ON      ' . $wpdb->users . '.ID = ' . $wpdb->usermeta . '.user_id 
        WHERE   ' . $wpdb->usermeta .'.meta_key =\'' . $wpdb->prefix . 'capabilities\'' ;

		_e('This website with blog_id='.$GLOBALS['blog_id'].'and prefix='.$wpdb->prefix .' has:', 'amr-users');
	}	
	echo '<ul>';
	if (is_multisite() and is_network_admin()) {
		echo '<li>';
			
		$sql = "SELECT count(*) FROM " . $wpdb->blogs;	

		$results = $wpdb->get_col( $sql, 0 );	
		foreach ($results as $i => $total) {
				printf(__('%s sites', 'amr-users'),number_format($total,0,'.',','));
		}
		echo '</li>';
	}
	echo '<li>';
		
	$sql = "SELECT count(*) FROM " . $wpdb->users.$where;	

	$results = $wpdb->get_col( $sql, 0 );	
	foreach ($results as $i => $total) {
			printf(__('%s users', 'amr-users'),number_format($total,0,'.',','));
	}
	echo '</li>';	
	echo '<li>';

	if (!empty($where))  // then we already know we are in a sub blog
		$wheremeta = " WHERE ".$wpdb->usermeta.".user_id IN ".
	"(SELECT distinct user_id FROM ".$wpdb->usermeta." WHERE ".$wpdb->usermeta .".meta_key ='" . $wpdb->prefix . "capabilities')";
	
	$sql = "SELECT count(*) FROM $wpdb->usermeta ".$wheremeta; 
	$results2 = $wpdb->get_col( $sql, 0 );	

	foreach ($results2 as $i => $total) {
		printf(__('%s user meta records.', 'amr-users'),number_format($total,0,'.',',')); 
	}
	echo '</li>';
	echo '<li>';
	$sql = "SELECT meta_key, count(*) FROM $wpdb->usermeta ".$wheremeta." GROUP BY meta_key ORDER BY meta_key ASC "; 
	$results = $wpdb->get_col( $sql, 0 );	
	$total = count($results);
	printf(__('%s different user meta keys.', 'amr-users'),number_format($total,0,'.',',')); 
	echo '</li>';
	echo '<li>';
		printf(__('Wordpress Memory limit: %s ', 'amr-users'),WP_MEMORY_LIMIT); 
	echo '</li>';
	echo '<li>';
		printf(__('Php Memory Limit: %s ', 'amr-users'),ini_get('memory_limit')); 
	echo '</li>';
	echo '<li>';
		_e('Compare these limits to the stats shown in your cache status', 'amr-users');
	echo '</li>';	
	echo '</ul>';
	echo '<p>';
	_e('If the user and user meta numbers are large, you may experience problems with large lists.', 'amr-users');
	echo '<br /><br />';	

	_e('If this happens, try: increasing php memory, clean up users (get rid of the spammy users), clean up usermeta.  You may have records from inactive plugins.', 'amr-users');
	echo '<input id="submit" style= "float:right;" class="button-secondary subsubsub" name="testqueries" type="submit" value="';
	_e('Run test queries', 'amr-users'); 
	echo '" /><br /><br />';
	if (isset($_REQUEST['testqueries'])) {
		echo '<hr /><b>'.__('Running some test queries:', 'amr-users').'</b>';
		if (!defined('WP_DEBUG')) define('WP_DEBUG', true);
		$_REQUEST['mem'] = true;  // to make track progress work
		track_progress('Test full user query:');
		$sql = "SELECT * FROM $wpdb->users".$where;
		$results = $wpdb->get_col( $sql, 0 );	
		track_progress('After users - how was it?');
		track_progress('Test user meta query:');
		$sql = "SELECT * FROM $wpdb->usermeta".$wheremeta;
		$results = $wpdb->get_col( $sql, 0 );	
		track_progress('After usermeta - how was it?');
		echo '<hr /><b>'.__('If these queries completed, the "fetch users directly" method should work, even if the "wp_query" method fails.', 'amr-users').__('See "How to fetch data" in the general settings.', 'amr-users').'</b>';
	}
}
/* ----------------------------------------------------------------------------------- */
function ameta_allowed_html () {
//	return ('<p><br /><hr /><h2><h3><<h4><h5><h6><strong><em>');
	return (array(
		'br' => array(),
		'em' => array(),
		'span' => array(),
		'h1' => array(),
		'h2' => array(),
		'h3' => array(),
		'h4' => array(),
		'h5' => array(),
		'h6' => array(),
		'strong' => array(),
		'p' => array(),
		'abbr' => array(
		'title' => array ()),
		'img' => array('src'=>array(), 'alt'=>array() ),
		'acronym' => array(
			'title' => array ()),
		'b' => array(),
		'blockquote' => array(
			'cite' => array ()),
		'cite' => array (),
		'code' => array(),
		'del' => array(
			'datetime' => array ()),
		'em' => array (), 'i' => array (),
		'q' => array(
			'cite' => array ()),
		'strike' => array(),
		'div' => array()

		)); 
	}
/* ----------------------------------------------------------------------------------- */
function amr_load_scripts () {
	wp_enqueue_script('jquery');
}	
/* --------------------------------------------------------------------------------------------*/	
function amr_users_can_edit ($type) {
		if (is_admin() and isset($_GET[$type])
		and (current_user_can('manage_options') or current_user_can('manage_userlists') ) )
		return true;
		else return false;
}
/* --------------------------------------------------------------------------------------------*/	
function amr_manage_headings_submit () {
	if (amr_users_can_edit('headings'))
			$headings_submit = '<div style="float:left;">'
			.'<input type="submit" name="update_headings" id="update_headings" class="button-primary" value="'
			.__('Update Column Headings').'"/>&nbsp;'
			.'<input type="submit" name="reset_headings" id="reset_headings" class="button" value="'
			.__('Reset Column Headings').'"/>'

			.'</div>';
		else $headings_submit = '';	
		return $headings_submit;
}
/* --------------------------------------------------------------------------------------------*/	
function amr_allow_update_headings ($cols,$icols,$ulist, $sortable) {

	if (!empty($_POST['reset_headings'])) {// check for updates to headings
		amr_users_reset_column_headings ($ulist);
	}
	$cols = amr_users_get_column_headings  ($ulist, $cols, $icols);	
	
	if (!empty($_POST['update_headings'])) {// check for updates to headings
	
		foreach ($icols as $ic => $cv) {
			if (isset($_POST['headings'][$ic])) {				
				$customcols[$cv] = esc_html($_POST['headings'][$ic]);				
				if ($customcols[$cv] === $icols[$ic]) {// if same as default, do not save  !! NOT COLS
					unset($customcols[$cv]);
				}
			}
		}

		if (!empty($customcols)) amr_users_store_column_headings  ($ulist, $customcols);
	}
	
	$cols = amr_users_get_column_headings  ($ulist, $cols, $icols);
	
	$html = '';		
	foreach ($icols as $ic => $cv) { /* use the icols as our controlling array, so that we have the internal field names */
		if (!($ic == 'checkbox')) {   			
			$v 		= '<input type="text" size="'.
			min(strlen($cols[$ic]), 80)
			.'" name="headings['.$ic.']" value="'.$cols[$ic].'" />';
		}
		else $v = 	$cols[$ic];	
		
		$html 	.= '<td>'.$v.'</td>';
		
	}	
	$hhtml = '<tr>'.$html.'</tr>'; /* setup the html for the table headings */		
	return ($hhtml);		
}
/* -------------------------------------------------------------------------------------------------------------*/
if (!function_exists('amrmeta_validate_rows_per_page') ) {
	function amrmeta_validate_rows_per_page()	{ /* basically the number of lists & names */
	global $aopt;
	global $amain;
		
		if (function_exists( 'filter_var') ) {
			$int_ok = (filter_var($_POST["rows_per_page"], FILTER_VALIDATE_INT, 
				array("options" => array("min_range"=>1, "max_range"=>999))));
		}
		else $int_ok = (is_numeric($_POST["rows_per_page"]) ? $_POST["rows_per_page"] : false);
		if ($int_ok) {
			$amain['rows_per_page'] =  $int_ok;
			return (true);
		}			
		else {
			$amain['rows_per_page'] = 25;	
			}
}
}
/* -------------------------------------------------------------------------------------------------------------*/
if (!function_exists('amrmeta_validate_avatar_size') ) {
function amrmeta_validate_avatar_size()	{ /* basically the number of lists & names */
	global $aopt;
	global $amain;
		
		if (function_exists( 'filter_var') ) {
			$int_ok = (filter_var($_POST["avatar-size"], FILTER_VALIDATE_INT, 
				array("options" => array("min_range"=>1, "max_range"=>400))));
		}
		else $int_ok = (is_numeric($_POST["avatar-size"]) ? $_POST["avatar-size"] : false);
		if ($int_ok) {
			$amain['avatar-size'] =  $int_ok;
			return (true);
		}			
		else {
			return (__('Invalid avatar size','amr-users'));	
			}
}
}
/* -------------------------------------------------------------------------------------------------------------*/	
function amrmeta_validate_no_lists()	{ /* basically the number of lists & names */
	global $amain;
	global $aopt;
		
		if (function_exists( 'filter_var') ) {
			$int_ok = (filter_var($_POST["no-lists"], FILTER_VALIDATE_INT, 
				array("options" => array("min_range"=>1, "max_range"=>99))));
		}
		else $int_ok = (is_numeric($_POST["no-lists"]) ? $_POST["no-lists"] : false);
		if ($int_ok) {
			if ($int_ok > $amain['no-lists'] ) {
				for ($i = $amain['no-lists']+1; $i <= $int_ok; $i++)	{	
					$amain['names'][$i] = $amain['names'][$i-1].'-'.__('copy').' '.$i;
					$aopt['list'][$i] = $aopt['list'][$i-1];
				}				
			}
			else {/* we are reducing the number of lists and should possibly clean up the cache etc*/ 	
				$logcache = new adb_cache();	
				for ($i = $int_ok+1; $i <= $amain['no-lists']; $i++)	{
						$result = $logcache->clear_cache($logcache->reportid($i,'user'));		
						unset ($aopt['list'][$i]);
				}
			}
			$amain['no-lists'] =  $int_ok;

			return (true);
		}	
			
		else {
			return ($logcache->get_error('numoflists'));	
			}
}
/* -------------------------------------------------------------------------------------------------------------*/
function amrmeta_validate_names()	{ /*  the names of lists */
	global $amain;

	if (is_array($_POST['name']))  {
		foreach ($_POST['name'] as $i => $n) {		/* for each list */	
			$amain['names'][$i] = $n;		
		}
		return (true);
	}
	else { 
		amr_flag_error (adb_cache::get_error('nonamesarray'));
		return (false);
	}	
}	
/* -------------------------------------------------------------------------------------------------------------*/
function amrmeta_validate_text($texttype)	{ /*  the names of lists */
	global $amain;

	if (!empty($_POST[$texttype]))  {
		$amain[$texttype] = wp_kses($_POST[$texttype], ameta_allowed_html());	
	}
	else $amain[$texttype] =  '';
	return true;
}	
/* -------------------------------------------------------------------------------------------------------------*/	
function amrmeta_validate_mainoptions()	{ 
	global $amain;
	global $aopt;
	
	if (isset($_POST["no_credit"]) ) {
		$amain['no_credit'] = true;
	}
	else $amain['no_credit'] = false;
	
	if (!empty($_POST["use_wp_query"]) ) {
		$amain['use_wp_query'] = true;
	}
	else $amain['use_wp_query'] = false;
	
	if (isset($_POST["do_not_use_css"]) ) {
		$amain['do_not_use_css'] = true;
	}
	else $amain['do_not_use_css'] = false;
	
	if (isset($_POST['csv_text'])) {
		$return = amrmeta_validate_text('csv_text');
		if ( is_wp_error($return) )	echo $return->get_error_message();
	}
	if (isset($_POST['refresh_text'])) {
		$return = amrmeta_validate_text('refresh_text');
		if ( is_wp_error($return) )	echo $return->get_error_message();
	}
	
	if (isset($_REQUEST["rows_per_page"]) ) {
		$return = amrmeta_validate_rows_per_page();
		if ( is_wp_error($return) )	echo '<h2>'.$return->get_error_message().'</h2>';
	}	

	if (isset($_POST["avatar-size"]) ) { 
		$return = amrmeta_validate_avatar_size();
		if ( is_wp_error($return) )	echo '<h2>'.$return->get_error_message().'</h2>';		
	}

	
	$amain['notonuserupdate'] = false;
	if (isset($_POST['notonuserupdate'])) {
		$amain['notonuserupdate'] = true;
	}
	
	if (!isset ($amain['cache_frequency'] )) 
		$amain['cache_frequency'] = 'notauto';
	if (isset($_POST['cache_frequency'])) {
		if (!($_POST['cache_frequency'] == $amain['cache_frequency'])) {
			$amain['cache_frequency'] = $_POST['cache_frequency'];	
			ameta_schedule_regular_cacheing	($_POST['cache_frequency']); 

		}
		//else echo '<div class="message">'.__('No change in cache frequency','amr_users').'</div>';
	}	
	else $amain['cache_frequency'] = 'notauto';
	
	$amain['version'] = AUSERS_VERSION;
	
	if (isset($_POST))	{ 
		ausers_update_option ('amr-users-no-lists', $amain) ;
		ausers_update_option ('amr-users', $aopt);
	}
	return;
}
/* -------------------------------------------------------------------------------------------------------------*/	
function amrmeta_validate_overview()	{ 
	global $amain;
	global $aopt;

	
	/*if (isset($_POST["no-lists"]) ) {
		$return = amrmeta_validate_no_lists();
		if ( is_wp_error($return) )	echo '<h2>'.$return->get_error_message().'</h2>';		
	} */
	if (isset($_REQUEST['addnew'])) {  
		
		$amain['names'][] = __('New list');
		$amain['no-lists'] = count ($amain['names']);
	}	
	

	if (isset($_POST['name'])) {
		$return = amrmeta_validate_names();
		if ( is_wp_error($return) )	echo $return->get_error_message();
	}

	unset($amain['public']);
	if (isset($_POST['public'])) {	
		if (is_array($_POST['public']))  {
			foreach ($_POST['public'] as $i=>$y) $amain['public'][$i] = true;
		}
	}
	if (isset($_POST['checkedpublic'])) { /* admin has seen the message and navigated to the settings screen and saved */
		$amain['checkedpublic'] = true;
	}
	unset($amain['sortable']);
	if (isset($_POST['sortable'])) {	
		if (is_array($_POST['sortable']))  {
			foreach ($_POST['sortable'] as $i=>$y) $amain['sortable'][$i] = true;
		}
	}
	unset($amain['customnav']);
	if (isset($_POST['customnav'])) {	
		if (is_array($_POST['customnav']))  {
			foreach ($_POST['customnav'] as $i=>$y) $amain['customnav'][$i] = true;
		}
	}
	//
	foreach ($amain['names'] as $i=>$n) {
		$amain['show_search'][$i] = false;
		$amain['show_perpage'][$i] = false;
		$amain['show_headings'][$i] = false;
		$amain['show_csv'][$i] = false;
		$amain['filterable'][$i] = false;
		$amain['show_refresh'][$i] = false;
	}
	//
	if (isset($_POST['show_search'])) {	
		if (is_array($_POST['show_search']))  {
			foreach ($_POST['show_search'] as $i=>$y) $amain['show_search'][$i] = true;
		}
	}
	if (isset($_POST['show_perpage'])) {	
		if (is_array($_REQUEST['show_perpage']))  {
			foreach ($_REQUEST['show_perpage'] as $i=>$y) $amain['show_perpage'][$i] = true;
		}
	}
	if (isset($_POST['show_headings'])) {	
		if (is_array($_REQUEST['show_headings']))  {
			foreach ($_REQUEST['show_headings'] as $i=>$y) $amain['show_headings'][$i] = true;
		}
	}
	if (isset($_POST['show_csv'])) {	
		if (is_array($_REQUEST['show_csv']))  {
			foreach ($_REQUEST['show_csv'] as $i=>$y) $amain['show_csv'][$i] = true;
		}
	}
	if (isset($_POST['show_refresh'])) {	
		if (is_array($_REQUEST['show_refresh']))  {
			foreach ($_REQUEST['show_refresh'] as $i=>$y) $amain['show_refresh'][$i] = true;
		}
	}
	
	$amain['version'] = AUSERS_VERSION;
	$amain['no-lists'] = count($amain['names']);
	if (isset($_POST)) {	
		ausers_update_option ('amr-users-no-lists', $amain);
		ausers_update_option ('amr-users', $aopt);
		}
	return;
}
    /* -------------------------------------------------------------------------------------------------------------*/
function amrmeta_validate_nicenames()	{
	global $amr_nicenames;
	
		if (empty ($amr_nicenames)) echo 'Unexpected problem - No nicenames !!!';
		
		$amr_nicenames['ID'] = '';
		
		if (isset($_POST['nn'])) { 
			
			if (is_array($_POST['nn'])) {
				foreach ($_POST['nn'] as $i => $v) {
					if (empty($v)) $amr_nicenames[$i] = '';
					else { 
						if	($s = esc_attr($v))  		
							$amr_nicenames[$i] = $s;
						else { 
							echo '<h2>Error in string:'.$s.'</h2>';
							return(false);
							}	
					}
					}
	
				}
			else {
				echo '<h2>Array of names not passed</h2>';
				return(false);
				}
			}
		ausers_update_option ('amr-users-nicenames', $amr_nicenames);		
		echo '<div class="message">'.__('Options Updated', 'amr-users').'</div>'; 	
		$excluded = array(); 
		if ((isset($_POST['nex'])) and (is_array($_POST['nex']))) {
			foreach ($_POST['nex'] as $i => $v) {
				if ($v) $excluded[$i] = true; 
			}
				
		}
		ausers_update_option('amr-users-nicenames-excluded', $excluded);	
		return (true);	
	}
	/* -------------------------------------------------------------------------------------------------------------*/
function ameta_listnicefield ($nnid, $nnval, $v, $v2=NULL) {
	
		echo "\n\t".'<li><label class="lists" for="nn'.$nnid.'"  '.(is_null($v2)?'>':' class="nested" >') .$v.' '.$v2.'</label>'
		.'<input type="text" size="50" id="nn'.$nnid.'"  name="nn['.$nnid.']"  value= "'.$nnval.'" /></li>'; 
	}
	/* ---------------------------------------------------------------------*/
function ausers_submit () {	
	return ('
	<div style="clear: both; float:right; padding-right:100px;" class="submit">
		<input type="hidden" name="action" value="save" />
		<input class="button-primary" type="submit" name="update" value="'. __('Update', 'amr-users') .'" />
		<input type="submit" name="reset" value="'. __('Reset all options', 'amr-users') .'" />
	</div>');
	}
		/* ---------------------------------------------------------------------*/
function alist_update () {	
	return ('
	<div style="float:left; padding: 0 10px;" class="submit">
		<input class="button-primary" type="hidden" name="action" value="save" />
		<input class="button-primary" type="submit" name="update" value="'. __('Update', 'amr-users') .'" />
	</div>');
	}
/* ---------------------------------------------------------------------*/
function alist_rebuild () {	
	return ('<div style="clear: both; padding: 20px;" class="submit">
			<input type="submit" class="button-primary" name="rebuildback" value="'.__('Rebuild cache in background', 'amr-users').'" />
			</div>');
	}
/* ---------------------------------------------------------------------*/
function alist_trashlogs () {	
	return ('<div style="clear: both; padding: 20px;" class="submit">
			<input type="submit" class="button" name="trashlog" value="'.__('Delete the cache log records', 'amr-users').'" />
			</div>');
	}
/* ---------------------------------------------------------------------*/
function alist_trashcache () {	
	return ('<div style="clear: both; padding: 20px;" class="submit">
			<input title="'.__('Delete the actual cache records.','amr-users').'" type="submit" class="button" name="trashcache" value="'.__('Delete all cache entries', 'amr-users').'" />
			</div>');
	}
/* ---------------------------------------------------------------------*/	
function alist_trashcache_status () {	
	return ('<div style="clear: both; padding: 20px;" class="submit">
			<input title="'.__('Does not delete report cache, only the status records.','amr-users').'" type="submit" class="button" name="trashcachestatus" value="'.__('Delete all cache status records', 'amr-users').'" />
			</div>');
	}
	/* ---------------------------------------------------------------------*/
function alist_rebuildreal ($i=1) {	
	return ('<br /><h3>'
		.__('Warning','amr-users').'</h3>'.__('Rebuilding in realtime can take a long time. Consider running a background cache instead.','amr-users').'<p>'
		.__('If you choose realtime, keep the page open after clicking the button.','amr-users').'</p>'
		.'<div style="clear: both; padding: 20px;" class="submit">
			<input type="hidden" name="rebuildreal" value="'.$i.'" />
			<input type="submit" name="rebuild" value="'.__('Rebuild in realtime', 'amr-users').'" />
			<input type="submit" class="button-primary" name="rebuildback" value="'.__('Rebuild in background', 'amr-users').'" />
			</div>');
	}
	/* ---------------------------------------------------------------------*/
function alist_rebuild_names () {	
	return ('
	<div style="float:left; padding: 0 10px;" class="submit">
		<input type="hidden" name="action" value="save" />
		<input type="submit" name="rebuild" value="'. __('Find any new fields. Be patient!', 'amr-users') .'" />
	</div>');
	}
	/* ---------------------------------------------------------------------*/
function alist_rebuild_names_update () {	
	return ('
	<div style="float:left; padding: 0 10px;" class="submit">
		<input type="hidden" name="action" value="save" />
		<input class="button-primary" type="submit" name="update" value="'. __('Update', 'amr-users') .'" />
		<input type="submit" name="rebuild" value="'. __('Find any new fields. Be patient!', 'amr-users') .'" />
		<input type="submit" name="resetnice" value="'. __('Reset and make new nice names', 'amr-users') .'" />
	</div>');
	}	
	/* ---------------------------------------------------------------------*/	
function ameta_list_nicenames_for_input($nicenames) {
	/* get the standard names and then the  meta names  */
		if (!($excluded = ausers_get_option('amr-users-nicenames-excluded'))) 
			$excluded = array();
			
		ksort($nicenames);	
		
		echo "\n\t".'<div class="clear">';
		echo '<h3>'.__('Nicer names for list headings','amr-users').'</h3>'
		.'<table class="widefat">';
		echo '<tr><th> </th><th>'
		.__('Nice Name','amr-users')
		.'</th><th>'
		.__('Exclude from Reports?','amr-users')
		.'</th></tr>';
		foreach ($nicenames as $i => $v ) {
			echo "\n\t".'<tr>'
			.'<td><label for="nn'.$i.'" >'.$i.'</label></td><td>'
			.'<input type="text" size="40" id="nn'.$i.'"  name="nn['.$i.']"  value= "'.$v.'" />';
			echo '</td><td>';
			if ($i==='ID') echo ' ' ;
			else {
				echo '<input type="checkbox" id="nex'.$i.'"  name="nex['.$i.']"';
				if (!empty($excluded[$i])) echo ' value=true checked="checked" ';
				echo ' />';
			}
			echo '</td></tr>';
			
		}	
		echo "\n\t".'</table>
		<div class="clear"></div>
		</div>';
		return;	
		
	}
	/* ---------------------------------------------------------------------*/	
function amrmeta_check_find_fields() {
global $amr_nicenames;

	$amr_nicenames = ausers_get_option ('amr-users-nicenames');  // refetch so have all includidng excluded
	if (is_wp_error($amr_nicenames) or (empty ($amr_nicenames))) { /* ***  Check if we have nicenames already built */
		echo '<h3 style="clear:both;">'.__('List of possible fields not yet built.', 'amr-users').'</h3>';
		track_progress('Before counting users');
		$result = count_users();
		track_progress('After counting users');
		$total_users = $result['total_users'];
		if ($total_users > 1000) { 
			amr_message(	__('You have many users. Please be very patient when you rebuild.', 'amr-users'));
			echo '<p>';
			foreach ($result['avail_roles'] as $i => $t) {
				echo '<br />'.__($i).' '.$t;
			}
			echo '<p>';
			echo alist_rebuild_names();
			return;
		}
		else {
			echo '<h3 style="clear:both;">'.__('Automatically rebuilding list of possible fields now.', 'amr-users').'</h3>';
			track_progress('Before rebuilding names');
			$amr_nicenames = ameta_rebuildnicenames();
			
			track_progress('After rebuilding names');
			echo '<h3 style="clear:both;">'.__('List Rebuilt', 'amr-users').'</h3>';
		}

	}

	}
	/* ---------------------------------------------------------------------*/	
function amrmeta_nicenames_page() {
	/* may be able to work generically */
	global $amr_nicenames;
	global $ausersadminurl;
	
	if (isset($_POST['action']) and !($_POST['action'] === "save")) return;
	
	echo '<div class="clear" style="clear:both;">';
	if (isset($_POST['update']) and ($_POST['update'] === "Update")) {/* Validate the input and save */
			if (amrmeta_validate_nicenames()) { // updates inside the function now
			}
			else echo '<h2>'.__('Validation failed', 'amr-users').'</h2>'; 	
		}
	if (isset($_POST['resetnice'])) {  
		delete_option('amr-users-nicenames'); // delete then rebuild
	}
	if (isset($_POST['rebuild']) or isset($_POST['resetnice'])) {/* Rebuild the nicenames - could take a while */	
				$amr_nicenames = ameta_rebuildnicenames ();
				echo '<h3>'.__('Rebuild Complete.', 'amr-users')
					.' <a href="'.wp_nonce_url($ausersadminurl.'&am_page=nicenames','amr-meta').'" >'.__('Edit the nice names.').'</a></h3>'; 
		}
	else {
		amrmeta_check_find_fields();
	}

	echo alist_rebuild_names_update();
	ameta_list_nicenames_for_input($amr_nicenames); 

	}	//end amrmeta nice names option_page
////	echo '<div class="clear"></div><!tryingto stop wp admin footer creeping up';
//	echo '</div>';	
//}
/* ---------------------------------------------------------------------*/
function amrmeta_validate_listfields()	{
	global $aopt;

/* We are only coming here if there is a SAVE, now there may be blanked out fields in all areas - except must have something selected*/

	if ( get_magic_quotes_gpc() ) {
		$_POST      = array_map( 'stripslashes_deep', $_POST );
	}
				
	if (isset($_POST['list'])) {
		if (is_array($_POST['list'])) {/*  do we have selected, etc*/
			foreach ($_POST['list'] as $i => $arr) {		/* for each list */	
				unset($aopt['list'][$i]);	
				if (is_array($arr))  {/*  */

					if (is_array($arr['selected']))  {/*  do we have  name, selected, etc*/		
						
						foreach ($arr['selected'] as $j => $v) {
							$v = trim($v);
							if ((empty($v)) or ($v == '0')  ) unset ($aopt['list'][$i]['selected'][$j] );
							else {
								if ($s = filter_var($v, FILTER_VALIDATE_FLOAT,
									array("options" => array("min_range"=>1, "max_range"=>999))))
									$aopt['list'][$i]['selected'][$j] = $s;
								else {
									echo '<h2>Error in display order for '.$j.$s.'</h2>';
									return(false);
								}
							}							
						}
//						asort ($aopt['list'][$i]['selected']); /* sort at update time so we don't have to sosrt every display time */
					}
					else {
						echo '<h2>No fields selected for display</h2>'; return (false);
					}
					
					/* Now check included */
					
					if (is_array($arr['included']))  {		
						
						foreach ($arr['included'] as $j => $v) {
							if (a_novalue($v)) 
								unset($aopt['list'][$i]['included'][$j]);
							else {
								$aopt['list'][$i]['included'][$j] 
									= explode (',', filter_var($v, FILTER_SANITIZE_STRING));
								$aopt['list'][$i]['included'][$j] = array_map('trim', $aopt['list'][$i]['included'][$j] );
								}
						}	
					}
															
					unset($aopt['list'][$i]['includeonlyifblank']);
					if (isset($arr['includeonlyifblank']) and is_array($arr['includeonlyifblank']))  {						
						foreach ($arr['includeonlyifblank'] as $j => $v) {
							$aopt['list'][$i]['includeonlyifblank'][$j] = true; 
							}	
						}	
					
					/* Now check excluded */
					if (is_array($arr['excluded']))  {		
						foreach ($arr['excluded'] as $j => $v) {
							if (a_novalue($v)) unset($aopt['list'][$i]['excluded'][$j]);
							else 
							$aopt['list'][$i]['excluded'][$j] 
								= explode(',', filter_var($v, FILTER_SANITIZE_STRING));
							}	
						}	
					/* Now check what to do with blanks */
					unset($aopt['list'][$i]['excludeifblank']);
					if (isset($arr['excludeifblank']) and is_array($arr['excludeifblank']))  {						
						foreach ($arr['excludeifblank'] as $j => $v) {
							$aopt['list'][$i]['excludeifblank'][$j] = true;
							}	
						}	
						
							
						
					/* Now check sortby */
					unset ($aopt['list'][$i]['sortby']	);		/* unset all sort by's in case non eare set in the form */	
					if (isset($arr['sortby']) and is_array($arr['sortby']))  {
						foreach ($arr['sortby'] as $j => $v) {						
							if (a_novalue($v)) unset ($aopt['list'][$i]['sortby'][$j]);
							else $aopt['list'][$i]['sortby'][$j]  = $v;	
						}	
					}
					/* Now check sortdir */
					unset ($aopt['list'][$i]['sortdir']	);		/* unset all sort directions */		
					if (isset($arr['sortdir']) and is_array($arr['sortdir']))  {				
						foreach ($arr['sortdir'] as $j => $v) {									
							if (!(a_novalue($v))) $aopt['list'][$i]['sortdir'][$j] = $v;
							else $aopt['list'][$i]['sortdir'][$j] = 'SORT_ASC';
						}	
					}
										/* Now check before*/
					unset ($aopt['list'][$i]['before']	);		/* unset all  */		
					if (isset($arr['before']) and is_array($arr['before']))  {				
						foreach ($arr['before'] as $j => $v) {									
							if (!(a_novalue($v))) 
								$aopt['list'][$i]['before'][$j] = (esc_html($v));
							else $aopt['list'][$i]['before'][$j] = '';
						}	
					}
															/* Now check after*/
					unset ($aopt['list'][$i]['after']	);		/* unset all  */		
					if (isset($arr['after']) and is_array($arr['after']))  {				
						foreach ($arr['after'] as $j => $v) {									
							if (!(a_novalue($v))) 
							$aopt['list'][$i]['after'][$j] = esc_html($v);
							else $aopt['list'][$i]['after'][$j] = '';
						}	
					}
															/* Now check links*/
					unset ($aopt['list'][$i]['links']	);		/* unset all  */		
					if (isset($arr['links']) and is_array($arr['links']))  {				
						foreach ($arr['links'] as $j => $v) {									
							if (!empty($v)) $aopt['list'][$i]['links'][$j] = ($v);
							else $aopt['list'][$i]['links'][$j] = 'none';
						}	
					}
				}
			}
		ausers_update_option ('amr-users', $aopt);
	}
	else {
		echo '<h3>'.__('At least some display order must be specified for the list to be meaningful').'</h3>';
		return (false);
		}
	}
	
return (true);	
}
/* ---------------------------------------------------------------------*/
function amrmeta_listfields_page($listindex) {
	global $aopt;

		
		if (isset($_POST['action']) and ($_POST['action'] == "save")) {/* Validate the input and save */

			if (amrmeta_validate_listfields($listindex)) {
				//ausers_update_option ('amr-users', $aopt); already done
				echo '<div class="updated fade">';
				echo '<p>'.__('Options Updated', 'amr-users').'</p>'; 
				echo '<p>'.__('Live rebuild will start.', 'amr-users').'</p>';
				echo '</div>';
				echo '<div class="clear"></div>';
				
				amr_rebuild_in_realtime_with_info ($listindex);
 
				
				}
			else echo '<h2>'.__('List Fields Validation failed', 'amr-users').'</h2>'; 	

		}
		else {
			
			amr_listfields( $listindex);
		}

	}	
	/* ---------------------------------------------------------------------*/
function amr_rebuildwarning ( $list ) {
	
	$logcache = new adb_cache();

	if ($logcache->cache_in_progress($logcache->reportid($list,'user'))) {
		$text = sprintf(__('Cache of %s already in progress','amr-users'),$list);
		$logcache->log_cache_event($text);
		echo $text;
		return;
	}	
	else {
		$text =$logcache->cache_already_scheduled($list);  
		if (!empty($text)) {
			$new_text = __('Report ','amr-users').$list.': '.$text;
			$logcache->log_cache_event($new_text); 
			echo  '<div id="message" class="updated fade"><p>'.$new_text.'</p></div>'."\n";	
			return;	
		}
	}	
	echo alist_rebuildreal($list);	
	return;
	
	}
	/* ---------------------------------------------------------------------*/	
function amr_listfields( $listindex = 1) {
	global $aopt;
	global $amain;
	global $amr_nicenames, $excluded_nicenames,$ausersadminurl;
	
	ameta_options();  // should handle emptiness etc
	$linktypes = amr_linktypes();

	/* check if we have some options already in Database. - use their names, if not, use default, else overwrite .*/
	if (!($checkifusingdefault = ausers_get_option ('amr-users-nicenames')) or (empty($amr_nicenames))) {
		//$text = __('Possible fields not configured! default list being used. Please build complete nicenames list.','amr-users');
		amrmeta_check_find_fields();
		
		exit;
	}
	else $text = 
	__('Please rebuild field nicenames when you have added fields or data values.','amr-users');	
	
	echo '<br /><br /><em style="clear:both;">'.$text
		.' <a title="'.__('Go to nice names','amr-users').'" href="'
		.wp_nonce_url(add_query_arg('am_page','nicenames',$ausersadminurl),'amr-meta').'">'
		.__('Go','amr-users')
		.'</a></em><br />';
		
	echo alist_update();		
	$config = &$aopt['list'][$listindex];
	
	$sel = &$config['selected'];
	/* sort our controlling index by the selected display order for ease of viewing */
	
	foreach ($amr_nicenames as $i => $n) {  
		if ((isset ($config['selected'][$i])) or
			(isset ($config['sortby'][$i])) or
			(isset ($config['included'][$i])) or
			(isset ($config['includeonlyifblank'][$i])) or
			(isset ($config['excluded'][$i])) or
			(isset ($config['excludeifblank'][$i])) or
			(isset ($config['sortdir'][$i])) 
			)
			$keyfields[$i] = $i;
		
	}
	if (isset ($keyfields))	
		$nicenames = auser_sortbyother($amr_nicenames, $keyfields); /* sort for display with the selected fields first */
	else 
		$nicenames = $amr_nicenames;

	if (count ($sel) > 0) {	
		uasort ($sel,'amr_usort');
		$nicenames = auser_sortbyother($nicenames, $sel); /* sort for display with the selected fields first */
	} 
	

	/*  List the fields for the specified list number, and for the configuration type ('selected' etc) */
		/*** would be nice to srt, but have to move away from nicenames as main index then */	
//		echo '<a name="list'.$i.'"> </a>';

		echo AMR_NL.'<div class="clear userlistfields">';
		echo '<b>'.sprintf(__('Configure list %s: %s','amr-users'),$listindex,$amain['names'][$listindex])
			.' | '.au_buildcache_link(__('Rebuild cache now','amr-users'),$listindex,$amain['names'][$listindex])
			.' | '.au_headings_link($listindex,$amain['names'][$listindex])
			.' | '.au_filter_link($listindex,$amain['names'][$listindex])
			.' | '.au_custom_nav_link($listindex,$amain['names'][$listindex])
			.' | '
			.'<span style="clear:both; text-align: right;">'.au_view_link(__('View','amr-users'), $listindex,$amain['names'][$listindex]).'</span>'
			.'</b>'; 

		echo '<table class="widefat" style="padding-right: 2px;"><thead  style="text-align:center;"><tr>'
			.AMR_NL.'<th style="text-align:right;">'.__('Field name','amr-users').'</th>'
			.AMR_NL.'<th style="width:1em;"><a href="#" title="'.__('Blank to hide, Enter a integer to select and specify column order.  Eg: 1 2 6 8', 'amr-users').'"> '.__('Display order','amr-users').'</a></th>'
			.AMR_NL.'<th><a href="#" title="'.__('Html to appear before if there is a value', 'amr-users').'"> '.__('Before:','amr-users').'</a></th>'
			.AMR_NL.'<th><a href="#" title="'.__('Html to appear after if there is a value', 'amr-users').'"> '.__('After:','amr-users').'</a></th>'

			.AMR_NL.'<th style="width:2em;"><a href="#" title="'.__('Type of link to be generated on the field value', 'amr-users').'"> '.__('Link Type:','amr-users').'</a></th>'
			.AMR_NL.'<th><a href="#" title="'.__('Eg: value1,value2', 'amr-users').'"> '.__('Include:','amr-users').'</a></th>'
			.AMR_NL.'<th><a href="#" title="'.__('Tick to include a user ONLY if there is no value', 'amr-users').'"> '.__('Include ONLY if Blank:','amr-users').'</a></th>'
			.AMR_NL.'<th><a href="#" title="'.__('Eg: value1,value2', 'amr-users').'"> '.__('But Exclude:','amr-users').'</a></th>'
			.AMR_NL.'<th><a href="#" title="'.__('Tick to exclude a user if there is no value', 'amr-users').'"> '.__('Exclude if Blank:','amr-users').'</a></th>'

			.AMR_NL.'<th style="width:1em;"><a href="#" title="'
				.__('Enter integers, need not be contiguous', 'amr-users')
				.' '
				.__('Maximum 2 sort level. Can switch off display.', 'amr-users')
				.'"> '.__('Sort Order:','amr-users').'</a></th>'
			.AMR_NL.'<th style="width:2em;"><a href="#" title="'.__('For sort order.  Default is ascending', 'amr-users').'"> '.__('Sort Descending:','amr-users').'</a></th>'

			.AMR_NL.'</tr></thead><tbody>';
	
			foreach ( $nicenames as $i => $f )		{		/* list through all the possible fields*/			
				echo AMR_NL.'<tr>';
				$l = 'l'.$listindex.'-'.$i;
				if ($i === 'comment_count') $f .= '<a title="'.__('Explanation of comment total functionality','amr-users')
				.'" href="http://wpusersplugin.com/comment-totals-by-authors/">**</a>';
				echo '<td style="text-align:right;">'.$f .'</td>';
					echo '<td><input type="text" size="1" id="'.$l.'" name="list['.$listindex.'][selected]['.$i.']"'. 
				' value="';
				if (isset($sel[$i]) or 
					(!empty($config['included'][$i])) or 
					(!empty($config['excludeifblank'][$i])) or 
					(!empty($config['excludeifblank'][$i])) or 
					(!empty($config['includeonlyifblank'][$i])) or 
					(!empty($config['sortby'][$i])) or
					(!empty($config['sortdir'][$i])) 
					)  {
									
					if (isset($sel[$i]))	echo $sel[$i];			
					echo '" /></td>';

					if (!empty($sel[$i]) ) {
						/* don't need label - use previous lable*/	
						echo '<td><input type="text" size="10"  name="list['.$listindex.'][before]['.$i.']"';
						if (isset ($config['before'][$i])) echo ' value="'
						.stripslashes($config['before'][$i]).'"';  //handle slashes returned by quotes
						echo ' /></td>';  // do not use htmlentities2 here - break foreigh chars

						echo '<td><input type="text" size="10"  name="list['.$listindex.'][after]['.$i.']"';
						if (isset ($config['after'][$i])) echo ' value="'
						.stripslashes($config['after'][$i]).'"';
						echo ' /></td>';
					}
					else echo '<td>-</td><td>-</td>';
					
					if (isset($sel[$i]) and !strpos($sel[$i],'.')) {									// if not a partial cell, then can have link type
					//if (isset($sel[$i]) and !strpos($sel[$i],'.')) {			
						echo '<td><select id="links'.$l.'" '
						.' name="list['.$listindex.'][links]['.$i.']" >';
						foreach ($linktypes as $lti => $linktype ) {
							 echo ' <option value="'.$lti.'" ';
							 if (!empty ($config['links'][$i]) and ($config['links'][$i] === $lti ))  
								echo ' selected = "selected" ';
							 echo ' >'.$linktype.'</option>';
							
						}	
						echo '</select></td>';
					}
					else echo '<td>-</td>';

//	echo '<td><select name="list['.$listindex.'][included]['.$i.']"';
//	echo amr_users_dropdown ($choices, $config['included'][$i]);
//	echo '</select>';
					
					echo '<td><input type="text" size="20"  name="list['.$listindex.'][included]['.$i.']"';
					if (isset ($config['included'][$i])) echo ' value="'.implode(',',$config['included'][$i]) .'"';
					
					echo ' /></td>';
					
					$l = 'c'.$listindex.'-'.$i;
					echo '<td><input type="checkbox"  name="list['.$listindex.'][includeonlyifblank]['.$i.']"';
					if (isset ($config['includeonlyifblank'][$i]))	{
						echo ' checked="checked" />';
						if (isset ($config['excludeifblank'][$i])) /* check for inconsistency and flag */
							echo '<span style="color:#D54E21; font-size:larger;">*</span>';
					}
					else echo '/>';
					echo '</td>';
					
					$l = 'x'.$listindex.'-'.$i;
					echo '<td><input type="text" size="20" id="'.$l.'" name="list['.$listindex.'][excluded]['.$i.']"';
					if (isset ($config['excluded'][$i])) echo ' value="'.implode(',',$config['excluded'][$i]) .'"';
					echo ' /></td>';

					$l = 'b'.$listindex.'-'.$i;
					echo '<td><input type="checkbox" id="'.$l.'" name="list['.$listindex.'][excludeifblank]['.$i.']"';
					if (isset ($config['excludeifblank'][$i]))	{
						echo ' checked="checked" />';
						if (isset ($config['includeonlyifblank'][$i])) /* check for inconsistency and flag */
							echo '<span style="color:#D54E21; font-size:larger;">*</span>';
					}
					else echo '/>';
					echo '</td>';


					$l = 's'.$listindex.'-'.$i;
					echo '<td>'
					.'<input type="text" size="2" id="'.$l.'" name="list['.$listindex.'][sortby]['.$i.']"';
					if (isset ($config['sortby'][$i]))  echo ' value="'.$config['sortby'][$i] .'"';
					echo ' /></td>'
					.'<td><input type="checkbox" id="sd'.$l.'" name="list['.$listindex.'][sortdir]['.$i.']"';
					 echo ' value="SORT_DESC"';
					if (isset ($config['sortdir'][$i]))  echo ' checked="checked"';
					echo ' />'
					.'</td>';

				
}
				else {
					echo '" /></td>';
					echo '<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>'
					.'<td>&nbsp;-&nbsp;</td>';
				}
				

				echo '</tr>';
			}
		echo AMR_NL.'</tbody></table></div>';
	return;	
	}
/* ---------------------------------------------------------------------*/	
function au_filter_link($i,$name) {
	
	if (!function_exists('amr_offer_filtering')) {
			return ('<a style="color: #AAAAAA;" href="http://wpusersplugin.com/related-plugins/amr-users-plus/" '.
			'title="'.__('Activate or acquire amr-user-plus addon for real time filtering','amr-users').'" ' 
			.'>'.__('Edit filtering').'</a>');
	}
	
	if (isset($_REQUEST['filtering'])) 
	return ('<b><a style="color: #006600;" href="'.admin_url('users.php?page=ameta-list.php?ulist='.$i
	.'">'.__('Exit filtering', 'amr-users').'</a></b>'));
	
	$t = '<a style="color:#D54E21; " href="'
		.admin_url('users.php?page=ameta-list.php?ulist='.$i.'&filtering=1')
		.'" title="'.sprintf(__('Realtime filtering %u: %s', 'amr-users'),$i, $name).'" >'
		.__('Edit filtering', 'amr-users')
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_custom_nav_link($i,$name) {
global $ausersadminurl;
	
	if (!function_exists('amr_custom_navigation_admin_form')) {
			return ('<a style="color: #AAAAAA;" href="http://wpusersplugin.com/related-plugins/amr-users-plus/" '.
			'title="'.__('Activate or acquire amr-user-plus addon for custom (eg: alphabetical) navigation','amr-users').'" ' 
			.'>'.__('Edit navigation').'</a>');
	}
	
	if (isset($_REQUEST['custom_navigation'])) 
	
	return ('<b><a style="color: #006600;" href="'.admin_url('users.php?page=ameta-admin.php?ulist='.$i
	.'">'.__('Exit navigation', 'amr-users').'</a></b>'));
	
	$url = wp_nonce_url(add_query_arg(array(
		'am_page'=>'custom_navigation',
		'ulist'=>$i)
		,$ausersadminurl),'amr-meta');
	$t = '<a style="color:#D54E21; " href="'
		.$url
		.'" title="'.sprintf(__('Custom navigation %u: %s', 'amr-users'),$i, $name).'" >'
		.__('Edit navigation', 'amr-users')
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_headings_link( $i,$name) {
	if (isset($_REQUEST['headings'])) 
	return ('<a href="'.admin_url('users.php?page=ameta-list.php?ulist='.$i
	.'">'.__('Exit headings').'</a>'));
	$t = '<a style="color:#D54E21;" href="'
		.admin_url('users.php?page=ameta-list.php?ulist='.$i.'&headings=1')
		.'" title="'.sprintf(__('Edit the column headings %u: %s', 'amr-users'),$i, $name).'" >'
		.__('Edit headings', 'amr-users')
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_configure_link($text, $i,$name) {
	global $ausersadminurl;
	$t = '<a style="color:#D54E21;" href="'.wp_nonce_url($ausersadminurl.'&amp;ulist='.$i,'amr-meta')
		.'" title="'.sprintf(__('Configure List %u: %s', 'amr-users'),$i, $name).'" >'
		.$text
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_delete_link ($text, $i,$name) {
	$url = remove_query_arg('copylist');
	$t = '<a href="'.wp_nonce_url(add_query_arg('deletelist',$i,$url),'amr-meta')
		.'" title="'.sprintf(__('Delete List %u: %s', 'amr-users'),$i, $name).'" >'
		.$text
		.'</a>';
	return ($t);
	}
/* ---------------------------------------------------------------------*/	
function au_copy_link ($text, $i,$name) {
	$url = remove_query_arg('deletelist');
	$t = '<a href="'.wp_nonce_url(add_query_arg('copylist',$i,$url),'amr-meta')
		.'" title="'.sprintf(__('Copy list to new %u: %s', 'amr-users'),$i, $name).'" >'
		.$text
		.'</a>';
	return ($t);
	}	
/* ---------------------------------------------------------------------*/	
function au_buildcache_link($text, $i,$name) {
	global $ausersadminurl;
	$t = '<a href="'.wp_nonce_url($ausersadminurl.'&amp;am_page=rebuildwarning&amp;ulist='.$i,'amr-meta')
		.'" title="'.__('Rebuild list in realtime - could be slow!', 'amr-users').'" >'
		.$text
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_buildcachebackground_link() {
	global $ausersadminurl;
	$t = '<a href="'.wp_nonce_url($ausersadminurl.'&amp;am_page=rebuildcache','amr-meta')
		.'" title="'.__('Build Cache in Background', 'amr-users').'" >'
		.__('Build Cache for all', 'amr-users')
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_view_link($text, $i, $title) {
	$t = '<a style="text-decoration: none;" href="'
// must be a ?	.add_query_arg('ulist',$i,'users.php?page=ameta-list.php')
		.'users.php?page=ameta-list.php?ulist='.$i
	.'" title="'.$title.'" >'
		.$text
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_csv_link($text, $i, $title) {
	global $ausersadminurl;
	$t = '<a style="color:#D54E21;" href="'.wp_nonce_url($ausersadminurl.'&amp;csv='.$i,'amr-meta').'" title="'.$title.'" >'
		.$text
		.'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_cachelog_link() {
	global $ausersadminurl;
	$t = '<a href="'
	.wp_nonce_url(add_query_arg('am_page','cachelog',$ausersadminurl),'amr-meta').'" title="'.__('Log of cache requests','amr-meta').'" >'.__('Cache Log','amr-users').'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function au_cachestatus_link() {
	global $ausersadminurl;
	$t = '<a href="'
	.wp_nonce_url(add_query_arg('am_page','cachestatus',$ausersadminurl),'amr-meta').'" title="'.__('Cache Status','amr-meta').'" >'.__('Cache Status','amr-users').'</a>';
	return ($t);
}
/* ---------------------------------------------------------------------*/	
function amr_meta_reset() {
global $aopt;
global $amain;
global $amr_nicenames,$ausersadminurl;

	if (ausers_delete_option ('amr-users')) echo '<h2>'.__('Deleting number of lists and names in database','amr-users').'</h2>';
//	else echo '<h3>'.__('Error deleting number of lists and names in database.','amr-users').'</h3>';
	if (ausers_delete_option ('amr-users'.'-no-lists')) echo '<h2>'.__('Deleting all lists settings in database','amr-users').'</h2>';
//	else echo '<h3>'.__('Error deleting all lists settings in database','amr-users').'</h3>';
	if (ausers_delete_option ('amr-users-nicenames')) echo '<h2>'.__('Deleting all nice name settings in database','amr-users').'</h2>';
	if (ausers_delete_option ('amr-users-nicenames-excluded')) echo '<h2>'.__('Deleting all nice name exclusion settings in database','amr-users').'</h2>';
//	else echo '<h3>'.__('Error deleting all lists settings in database','amr-users').'</h3>';
	if (ausers_delete_option ('amr-users-cache-status')) echo '<h2>'.__('Deleting cache status in database','amr-users').'</h2>';
	if (ausers_delete_option ('amr-users-original-keys')) echo '<h2>'.__('Deleting original keys mapping in database','amr-users').'</h2>';	

	if (ausers_delete_option ('amr-users-custom-headings')) echo '<h2>'.__('Deleting custom-headings in database','amr-users').'</h2>';	
	if (ausers_delete_option ('amr-users-filtering')) echo '<h2>'.__('Deleting amr-users-filtering in database','amr-users').'</h2>';
	if (ausers_delete_option ('amr-users-prefixes-in-use')) echo '<h2>'.__('Deleting amr-users-prefixes-in-use in database','amr-users').'</h2>';
		
	
	
	
//	else echo '<h3>'.__('Error deleting all lists settings in database','amr-users').'</h3>';
//	if (delete_option ('amr-users-cachedlists')) echo '<h2>'.__('Deleting cached lists info in database','amr-users').'</h2>';
//	else echo '<h3>'.__('Error deleting all lists settings in database','amr-users').'</h3>';
	
	$c = new adb_cache();
	//$c->clear_all_cache();
	$c->deactivate();
	echo '<h2>'.__('All cached listings cleared.','amr-users').'</h2>';
	unset ($aopt);
	unset ($amain);
	unset ($amr_nicenames);
	echo '<h2><a href="'.$ausersadminurl.'">'.__('Click to return to default settings','amr-users').'</a></h2>';
	die;
}
/* ---------------------------------------------------------------------*/	
function amru_related() {
	echo '<p>'.
	__('Related plugins are continually being developed in response to requests. They are packaged separately so you only add what you need.')
	.'<p>';
	echo '<ul>';
	echo '<li>';
	echo '<a href="http://wpusersplugin.com/related-plugins/amr-cron-manager/" >amr cron manager</a> - ';
	_e('Improve visibility and manage the cron schedules');
	echo '</li>';
	echo '<li>';
	echo '<a href="http://wpusersplugin.com/related-plugins/amr-users-plus/" >amr users plus</a> - ';
	_e('Adds functionality such as complex filtering');
	echo '</li>';
	echo '<li>';
	echo '<a href="http://wpusersplugin.com/related-plugins/amr-users-plus-s2/" >amr users plus s2</a> - ';
	_e('Adds subscribers in the separate subscribe2 table to the user lists');
	echo '</li>';
	echo '<li>';
	echo '<a href="http://wpusersplugin.com/related-plugins/amr-users-plus-cimy/" >amr users plus cimy</a> - ';
	_e('Makes the separate "cimy extra fields" table look like normal user meta data');
	echo '</li>';
	echo '<li>';
	echo '<a href="http://wpusersplugin.com/related-plugins/amr-users-plus-ym/" >amr users plus ym</a> - ';
	_e('Adds bulk ym updates and better formatting of ym fields.');
	echo '</li>';
	echo '<li>';
	echo '<a href="http://wpusersplugin.com/related-plugins/amr-users-multisite/" >amr users multi site</a> - ';
	_e('Makes amr users operate in the network pages across the sites.');
	echo '</li>';

	echo '</ul>';
	echo '<a href="http://wpusersplugin.com/related-plugins" >'.
	__('... there may be more.')
	.'</a>';
	
	}
/* ---------------------------------------------------------------------*/	
function amr_test_your_db() { /* the main setting spage  - num of lists and names of lists */
	amr_mimic_meta_box('about', 'About your user database','amr_about_users', false);
}
	/* ---------------------------------------------------------------------*/	
function amr_meta_general_page() { /* the main setting spage  - num of lists and names of lists */
	global $amain;

	amr_mimic_meta_box('related', 'Related plugins','amru_related', true);

	if (empty($amain)) $amain = ameta_no_lists();

	if ((!ameta_cache_enable()) or  (!ameta_cachelogging_enable())) 
			echo '<h2>Problem creating DB tables</h2>';
	
/* validation will have been done */
		$freq = array ('notauto'=> __('No - on standard user update only', 'amr-users'), 
		
					'hourly'    => __('Hourly', 'amr-users'), 
					'twicedaily'=> __('Twice daily', 'amr-users'), 
					'daily'     => __('Daily', 'amr-users'),
//					'monthly'     => __('Monthly', 'amr-users')
						);

//		if (!empty($amain['cache_frequency']))  
		echo ausers_submit();
		$amain['csv_text'] = empty($amain['csv_text']) 
		?('<img src="'.plugins_url('amr-users/images/file_export.png').'" alt="'.__('Csv') .'"/>') 
		:($amain['csv_text']);
		$amain['refresh_text'] = empty($amain['refresh_text']) 
		? ('<img src="'.plugins_url('amr-users/images/rebuild.png').'" alt="'.__('Refresh user list cache') .'"/>' )
		:($amain['refresh_text']);
		
		if (!(isset ($amain['checkedpublic']))) {
			echo '<input type="hidden" name="checkedpublic" value="true"/>'; }
		if (!isset ($amain['cache_frequency'])) 
			$freqchosen = 'notauto'; 
		else 
			$freqchosen = $amain['cache_frequency'];
		if (isset ($amain['do_not_use_css']) and ($amain['do_not_use_css'])) 
			$do_not_use_css = ' checked="checked" ';
		else 
			$do_not_use_css = '';
		
		echo '<div class="clear wrap">';	
		echo '<ul style="padding: 5px;">
		<li>';
		echo '<h3 id="about">'.__('About', 'amr-users').'</h3>';
		
		echo '<p><b>'.__('Shortcodes to add to pages:', 'amr-users').'</b>&nbsp;  [userlist] &nbsp;&nbsp;or &nbsp;&nbsp;[userlist list=n]</p>';
		amr_users_say_thanks_opportunity_form();
		echo '<br /></li>
		<li><br />';

		
		echo '<h3>';
		_e('How to fetch data?');
		echo '</h3><input type="radio"  name="use_wp_query" value="1" ';
		if (!empty($amain['use_wp_query'])) echo ' checked="checked" ';
		echo '> ';	
		_e('Fetch user data with wp_query? &nbsp; ', 'amr-users');	
		echo ' <em>';_e('WordPress does some extra work which requires more memory');echo '</em>';
		echo '<br />';		
	//	echo '</label>';
	//	echo '<label for="use_wp_query">';

		echo '<input type="radio"  name="use_wp_query" value="0" ';
		if (empty($amain['use_wp_query'])) echo ' checked="checked" ';
		echo '> ';
		_e('Fetch user data directly? &nbsp; ', 'amr-users');
		echo ' <em>';_e('This seems to use less memory, better for very large databases.');echo '</em>';
		echo '<br /><br />';		
	
		//echo '</label>';
		echo '<h3 id="general">'.__('General Options', 'amr-users').'</h3>';		
		echo '<label for="do_not_use_css">';
		_e('Do not use css provided, my theme css is good enough', 'amr-users'); 
		echo '</label>
			<input type="checkbox" size="2" id="do_not_use_css" 
					name="do_not_use_css" ';
		echo empty($amain['do_not_use_css']) ? '' :' checked="checked" '; 
		echo '/></li>
		<li>
			<label for="csv_text">';
		_e('Text for csv link', 'amr-users'); 
		echo ' <em>';
		_e('(May be plain text or an icon link)', 'amr-users');
		echo ' </em>';
		echo '</label><br />
			<input type="text" size="130" id="csv_text" 
					name="csv_text" value="';
		echo esc_attr($amain['csv_text']); 
		echo '"/>'.' '.__('Preview:','amr-users').' '.
		'<a href="#" title="'.__('This will be a link','amr-users').'" >'. $amain['csv_text'].'</a>'
		.'</li>';
		echo '<li>
			<label for="refresh_text">';
		_e('Text for cache refresh link', 'amr-users'); 
		echo '</label><br />
			<input type="text" size="130" id="refresh_text" 
					name="refresh_text" value="';
		echo esc_attr($amain['refresh_text']); 
		echo '"/>'.
		' '.__('Preview:','amr-users').' '.
		'<a href="#" title="'.__('This will be a link','amr-users').'" >'. $amain['refresh_text'].'</a>'
		.'</li>
			<li>
			<label for="rows_per_page">';
		_e('Default rows per page:', 'amr-users'); 
		echo '</label><br />
			<input type="text" size="2" id="rows_per_page" 
					name="rows_per_page" value="';
		echo empty($amain['rows_per_page']) ? 50 :$amain['rows_per_page']; 
		echo '"/></li>
		<li>
			<label for="avatar-size">';
		_e('Avatar size:', 'amr-users');
		
		echo ' 20,40, 80, 160, 200 </label>'.
		'<a title="gravatar size info" href="http://en.gravatar.com/site/implement/images/">'.__('Info').'</a>'
		.'<br />
			<input type="text" size="2" id="avatar-size" 
					name="avatar-size" value="';
		echo ((empty($amain['avatar-size'])) ? '' :$amain['avatar-size'] ); // because it is new and I hate notices
		echo '"/></li>';
			
		echo '<li>
			<h3>';
		_e('Activate regular cache rebuild ? ', 'amr-users'); 
		echo '</h3><span><em>';
		
 
		_e('The cache log will tell you the last few times that the cache was rebuilt and why. ', 'amr-users'); 
		echo '<br />';
		_e('A cron plugin may also be useful.', 'amr-users'); 
		echo '<br />';
		_e('If you have very frequent user updates consider only cacheing at regular intervals', 'amr-users'); 
		echo '<br />';
		_e('Are you tracking every page? every login.. who knows?!', 'amr-users'); 	
		echo '</em>	</span>	<br />';
		echo '<br /><label for="notonuserupdate">
			<input type="checkbox" size="2" id="notonuserupdate" 
				name="notonuserupdate" ';
		echo (empty($amain['notonuserupdate'])) ? '' :' checked="checked" '; 
		echo '/>';
		_e('Do not re-cache on every user update', 'amr-users'); 
		echo '</label>';
		echo '<br /><em>';
		_e('To switch off all auto cacheing, select "Do not.." above AND "No..." below.', 'amr-users'); 
		echo '<br /></em>';
		foreach ($freq as $i=> $f) { 
				echo '<br /><label><input type="radio" name="cache_frequency" value="'.$i.'" ';
 				if ($i == $freqchosen) echo ' checked="checked" ';  
				echo '/>';
				echo $f; 
				echo '</label>';			
			} 
			echo '<br />
			</li></ul>
			</div><!-- end of next wrap -->	'
			.'<div class="clear"> </div>';	
			

}
/* ---------------------------------------------------------------------*/	
function amr_meta_overview_page() { /* the main setting spage  - num of lists and names of lists */
	global $amain;

	if (empty($amain)) $amain = ameta_no_lists();

	if ((!ameta_cache_enable()) or  (!ameta_cachelogging_enable())) 
			echo '<h2>Problem creating DB tables</h2>';
	
	echo ausers_submit();

	if (!(isset ($amain['checkedpublic']))) {
			echo '<input type="hidden" name="checkedpublic" value="true"/>'; }

		echo '<div class="wrap">';
//		echo '<label for="no-lists">';
		_e('Number of User Lists:', 'amr-users');
//		echo '</label><br />
//			<input type="text" size="2" id="no-lists" 
//					name="no-lists" value="';
		echo $amain['no-lists']; 
//		echo '"/></li>
		echo '</ul>';		
		echo '<h3 id="lists">'.__('Lists Overview').'</h3>';	
		if (isset ($amain['names'])) { 
			echo '<table class="widefat"><thead><tr>
			<th class="show">';
			_e('Public', 'amr-users'); 
			echo '&nbsp;<a class="tooltip" href="#" title="';
			_e('List may be viewed in public pages', 'amr-users'); 
			echo '">?</a></th>
			<th class="show">';
			_e('Show search', 'amr-users'); 
			echo '&nbsp;<a class="tooltip" href="#" title="';
			_e('If list is public, show user search form.', 'amr-users'); 
			echo '">?</a></th>
			<th class="show">';
			_e('Show per page', 'amr-users'); 
			echo '&nbsp;<a class="tooltip" href="#" title="';
			_e('If list is public, show per page option.', 'amr-users'); 
			echo '">?</a></th>
			<th class="show">';
			_e('Show headings', 'amr-users'); 
			echo '&nbsp;<a class="tooltip" href="#" title="';
			_e('If list is public, show column headings.', 'amr-users'); 
			echo '">?</a></th>
			<th class="show">';
			_e('Show csv link', 'amr-users'); 
			echo '&nbsp;<a class="tooltip" href="#" title="';
			_e('If list is public, show a link to csv export file', 'amr-users'); 
			echo '">?</a></th>
			<th class="show">';
			_e('Show refresh', 'amr-users'); 
			echo '&nbsp;<a class="tooltip" href="#" title="';
			_e('If list is public, show a link to refresh the cache', 'amr-users'); 
			echo '">?</a></th>
			<th class="show">';
			_e('Sortable', 'amr-users'); 
			echo '&nbsp;<a class="tooltip" href="#" title="';
			_e('Offer sorting of the cached list by clicking on the columns.', 'amr-users'); 
			echo '">?</a></th>';
			echo '<th class="show">';
			_e('Show custom navigation', 'amr-users'); 
			echo '&nbsp;<a class="tooltip" href="#" title="';
			_e('Offer custom navigation to find users. ', 'amr-users'); 
			_e('Requires the amr-users-plus addon.', 'amr-users'); 
			echo '">?</a></th>';
			echo '<th>';
			_e('Name of List', 'amr-users'); 
			echo '</th>
			<th>';
			_e('Actions', 'amr-users'); 
			echo '</th>
			</tr></thead><tbody>';
			
			foreach ($amain['names'] as $i => $name) {
			//for ($i = 1; $i <= $amain['no-lists']; $i++)	{
				echo '<tr><td align="center"><input type="checkbox" id="public'
					.$i.'" name="public['. $i .']" value="1" ';
				if (isset($amain['public'][$i])) {
					echo 'checked="checked" /></td>';
//	
					echo '<td align="center"><input type="checkbox" id="show_search'
						.$i.'" name="show_search['. $i .']" value="1" ';
					if (!empty($amain['show_search'][$i])) echo 'checked="Checked"'; 
					echo '/></td>';
//
					echo '<td align="center"><input type="checkbox" id="show_perpage'
						.$i.'" name="show_perpage['. $i .']" value="1" ';
					if (!empty($amain['show_perpage'][$i])) echo 'checked="Checked"'; 
					echo '/></td>';
					//
					echo '<td align="center"><input type="checkbox" id="show_headings'
						.$i.'" name="show_headings['. $i .']" value="1" ';
					if (!empty($amain['show_headings'][$i])) echo 'checked="Checked"'; 
					echo '/></td>';
					//
					echo '<td align="center"><input type="checkbox" id="show_csv'
						.$i.'" name="show_csv['. $i .']" value="1" ';
					if (!empty($amain['show_csv'][$i])) echo 'checked="Checked"'; 
					echo '/></td>';
										//
					echo '<td align="center"><input type="checkbox" id="show_refresh'
						.$i.'" name="show_refresh['. $i .']" value="1" ';
					if (!empty($amain['show_refresh'][$i])) echo 'checked="Checked"'; 
					echo '/></td>';
				}
				else {
					echo '/></td><td> </td><td> </td><td>  </td><td> </td><td> </td>';
				}
//			
				echo '<td align="center">
					<input type="checkbox" id="sortable'.$i.'" name="sortable['.$i.']"  ';
				echo '	value="1" ';
				if (isset($amain['sortable'][$i])) echo 'checked="Checked"'; 
				echo '/></td>';
//				
				echo '<td align="center">
					<input type="checkbox" id="customnav'.$i.'" name="customnav['.$i.']"  ';
				echo '	value="1" ';
				if (isset($amain['customnav'][$i])) echo 'checked="Checked"'; 
				echo '/></td>';
				
				echo '<td>';
				echo $i.'&nbsp;';
				echo '<input type="text" size="40" id="name'
				.$i.'" name="name['. $i.']"  value="'.$amain['names'][$i].'" /></td>';
				echo '<td>'
					.au_configure_link('&nbsp;&nbsp;'.__('Configure','amr-users'),$i,$amain['names'][$i])
					.' |'.au_delete_link('&nbsp;&nbsp;'.__('Delete','amr-users'),$i,$amain['names'][$i])
					.' |'.au_copy_link('&nbsp;&nbsp;'.__('Copy','amr-users'),$i,$amain['names'][$i]).' |'
					.'<br />'.au_buildcache_link('&nbsp;&nbsp;'.__('Rebuild cache','amr-users'),$i,$amain['names'][$i])
					.' |'.au_view_link('&nbsp;&nbsp;'.__('View','amr-users'),$i,$amain['names'][$i]);
//					.' |'.au_csv_link('&nbsp;&nbsp;'.__('CSV Export','amr-users'),$i,$amain['names'][$i]
//						.__(' - Standard CSV with text.','amr-users'))
//					.' |'.au_csv_link('&nbsp;&nbsp;'.__('Txt Export','amr-users'),
//						$i.'&amp;csvfiltered',$amain['names'][$i]
//						.__('- a .txt file, with CR/LF filtered out, html stripped, tab delimiters, no quotes ','amr-users'));
					echo '</td></tr>';
				}
			};
		echo '</tbody></table></div><!-- end of one wrap --> <br />';
			
	//echo '<div style="clear: both; float:right; padding-right:100px;" class="submit">';
	echo	'<input class="button-primary" type="submit" name="addnew" value="'. __('Add new', 'amr-users') .'" />';
	//	</div>';
	

		echo '<div class="clear"> </div>';	
			

}						
/* ---------------------------------------------------------------------*/		
function a_currentclass($page){
	if ((isset($_REQUEST['am_page'])) and ($_REQUEST['am_page']===$page))
	return (' class="current" ');
	else return('');
}
/* ---------------------------------------------------------------------*/	
function amrmeta_admin_header() {
global $ausersadminurl;
	if (empty($ausersadminurl)) $ausersadminurl = ausers_admin_url ();
	echo AMR_NL.'<ul class="subsubsub" style="float:right;">';
	$t = __('Plugin News', 'amr-users');
	echo '<li><a target="_blank" href="'
	.htmlentities(add_query_arg('news','news',$ausersadminurl)).'" title="'.$t.'" >'.$t.'</a>|</li>';	
	echo '<li><a target="_blank" href="http://wpusersplugin.com/support">';
	_e('Support','amr-users');
	echo '</a>|</li>
	<li><a target="_blank" href="http://wordpress.org/extend/plugins/amr-users/">';
	_e('Rate it at Wordpress','amr-users');
	echo '</a>|</li>
	<li><a target="_blank" href="https://www.paypal.com/sendmoney?email=anmari@anmari.com">';
	_e('Say thanks to anmari@anmari.com','amr-users');
	echo '</a>|</li>
	<li>
	<a target="_blank" href="http://wpusersplugin.com/feed/">';
	_e('Rss feed','amr-users');
	echo '</a></li></ul>';
	
	echo AMR_NL.'<h2>'.__('Configure User Lists:','amr-users').AUSERS_VERSION.'</h2>'
	.AMR_NL.'<ul class="subsubsub">';	
	$t = __('General', 'amr-users');
	echo AMR_NL.'<li><a  href="'
	.$ausersadminurl.'" title="'.$t.'" >'.$t.'</a>|</li>';
	$t = __('Test your db', 'amr-users');
	echo AMR_NL.'<li><a  href="'
	.wp_nonce_url(add_query_arg('am_page','testyourdb',$ausersadminurl),'amr-meta').'" title="'.$t.'" >'.$t.'</a>|</li>';
	$t = __('Overview', 'amr-users');
	echo AMR_NL.'<li>&nbsp;<span class="step">1.</span><a  href="'
	.wp_nonce_url(add_query_arg('am_page','overview',$ausersadminurl),'amr-meta').'" title="'.$t.'" >'.$t.'</a>|</li>';
	$t = __('Nice Names', 'amr-users');
	echo '<li>&nbsp;<span class="step">'
	.'2.</span><a '.a_currentclass('nicenames').' href="'
	.wp_nonce_url(add_query_arg('am_page','nicenames',$ausersadminurl),'amr-meta').'" title="'.$t.'" >'.$t.'</a>|&nbsp;<span class="step">'
	.'3.</span></li></ul>';	
	$t = __('Rebuild Cache in Background', 'amr-users');
		
	
	list_configurable_lists();
	echo '<ul class="subsubsub"><li>&nbsp;<span class="step">4.</span>'.au_buildcachebackground_link().'|</li>';	
	echo '<li>&nbsp;<span class="step">5.</span>'.au_cachelog_link().'|</li>';	
	echo '<li>&nbsp;<span class="step">6.</span>'.au_cachestatus_link().'</li>';	
	echo '</ul>';
	return;
}
/* ---------------------------------------------------------------------*/
function amrmeta_confighelp() {
// style="background-image: url(images/screen-options-right-up.gif);"


	$html = '<p>'.__('Almost all possible user fields that have data are listed below.  If you have not yet created data for another plugin used in your main site, then there may be no related data here.  Yes this is a looooong list, and if you have a sophisticated membership system, it may be even longer than others.  The fields that you are working with will be sorted to the top, once you have defined their display order.', 'amr-users')
	.'</p><p>'
	.__('After a configuration change, the cached listing must be rebuilt for the view to reflect the change.', 'amr-users')
	.'</p><ol><li>'
	.__('Enter a number in the display order column to select a field for display and to define the display order.', 'amr-users')
	.'</li><li>'
	.__('Enter a number (1-2) to define the sort order for your list', 'amr-users')
	.'</li><li>'
	.__('Use decimals to define ordered fields in same column (eg: first name, last name)', 'amr-users')
	.'</li><li>'
	.__('If a sort order should be descending, such as counts or dates, click "sort descending" for that field.', 'amr-users')
	.'</li><li>'
	.__('From the view list, you will see the data values.  If you wish to include or exclude a record by a value, note the value, then enter that value in the Include or Exclude Column.  Separate the values with a comma, but NO spaces.', 'amr-users')
	.__('Note: Exclude and Include blank override any other value selection.', 'amr-users')
	.'</li></ol>';
	return ($html);
}
/* ----------------------------------------------------------------------------------- */	
function amrmeta_nicenameshelp() {
// style="background-image: url(images/screen-options-right-up.gif);"

	$html = '<ol>'
	.'<li>'.__('If you are not seeing all the fields you expect to see, then rebuild the list. Please note that what you see is dependent on the data in your system. If there is no meta data for a field you are expecting to see, it is impossible for that field to appear ', 'amr-users').'</li>'
	.'<li>'.__('If you add another user related plugin that adds meta data, first add some data to at least one user.  Then you may need to rebuild the list of fields below and/or reconfigure your reports if you want to see the new data.', 'amr-users').'</li>'
	.'</ol>';
	return( $html);
}
/* ----------------------------------------------------------------------------------- */	
function amrmeta_mainhelp($contextual_help, $screen_id, $screen) {
global $amr_pluginpage;

	if ($screen_id == $amr_pluginpage) {
	
	$contextual_help = 
	'<h3>Overview</h3>'
	.'<ol><li>'.__('Defaults lists are provided as examples only.  Please configure them to your requirements.', 'amr-users').'</li><li>'
	.__('To add, or delete a list, change the number of lists and press update.', 'amr-users').'</li><li>'
	.__('Update any new list details and configure the list.', 'amr-users').'</li><li>'
	.__('Each new list is copied from the last configured list.  This may be useful if configuring a range of similar lists - add the lists one by one - slowly incrementing the number of lists.', 'amr-users').'</li></ol>';
	
	
	$contextual_help .= '<h3>List Settings</h3>'.amrmeta_confighelp();
	$contextual_help .= '<h3>Nice Names</h3>'.amrmeta_nicenameshelp();
	return $contextual_help;
	}
}
/* ---------------------------------------------------------------------*/
function amr_trash_the_cache () { 

	ausers_delete_option ('amr-users-cache-status');
	$text = __('Cache status records deleted, try building cache again');
	$text = '<div id="message" class="updated fade"><p>'.$text.'<br/>'
	.'<a href="">'.__('Return', 'amr_users').'</a>'.'</p></div>'."\n";
	echo $text;

}
/* ---------------------------------------------------------------------*/
function amr_rebuild_in_realtime_with_info ($list) {
	if (amr_build_user_data_maybe_cache ($list)) {; 
	echo '<div class="update">'.sprintf(__('Cache rebuilt for %s ','amr-users'),$list).'</div>'; /* check that allowed */
	echo au_view_link(__('View Report','amr-users'), $list, __('View the recently cached report','amr-users'));
	}
	else echo '<div class="update">'.sprintf(__('Check cache log for completion of list %s ','amr-users'),$list).'</div>'; /* check that allowed */
}
/* ---------------------------------------------------------------------*/
function amr_get_alluserkeys(  ) {

global $wpdb;
/*  get all user data and attempt to extract out any object values into arrays for listing  */
	$keys = array(
		'avatar'=>'avatar',
		'comment_count'=>'comment_count',
		'post_count'=>'post_count');
		
	$post_types=get_post_types();  
	
	foreach ($post_types as $posttype) $keys[$posttype] = $posttype.'_count';
	
	$all = amr_get_usermasterfields(); 

	echo '<h3>'.sprintf(__('You have %s main user table fields'),count($all)).'</h3>';

		foreach ($all as $i2 => $v2){	
			if (!amr_excluded_userkey($v2) ) {
				$keys[$v2] = $v2;	
				echo '<br />'.__('Added to report DB:', 'amr-users').' '.$v2;
			}
			else echo '<br />'.__('Exclude (not applicable to reporting):', 'amr-users').' '.$v2;

		}
		/* Do the meta first  */
	$q =  "SELECT DISTINCTROW meta_key, meta_value FROM $wpdb->usermeta";

	if ($mkeys = amr_get_next_level_keys( $q)) {

		if (is_array($mkeys)) {
			$keys = array_merge ($keys, $mkeys);	
		}
	}

	unset($mkeys);
	
	$keys = apply_filters('amr_get_fields', $keys); //'avatar'=>'avatar',

	return($keys);
}
/** ----------------------------------------------------------------------------------- */
function amr_get_next_level_keys( $q) {
/*  get all user data and attempt to extract out any object values into arrays for listing  */
global $wpdb, $orig_mk;

	if (!$orig_mk = ausers_get_option('amr-users-original-keys')) 
		$orig_mk = array();
	
	$all = $wpdb->get_results($q, ARRAY_A); 
//	print_r ($all);
	if (is_wp_error($all)) {amr_flag_error ($all); return;}
	if (!is_array ($all)) return;
	echo '<br /><h3>'.sprintf(__('You have %u meta key records. '),count($all)).'</h3>';
	_e('...Deserialising and rationalising...');
	foreach ($all as $i2 => $v2) {  /* array of meta key, meta value*/
			/* Exclude non useful stuff */
//			print_r ($v2);
			$mk = $v2['meta_key'];
			$mv = $v2['meta_value'];	

			if (!amr_excluded_userkey($mk) ) {
				
				if (!empty($mv)) {
					$temp = maybe_unserialize ($mv);
					$temp = objectToArray ($temp); /* *must do all so can cope with incomplete objects */
					$key = str_replace(' ','_', $mk); /* html does not like spaces in the names*/
					if (is_array($temp)) {  
						foreach ($temp as $i3 => $v3) {
							
							if (is_array($v3) and function_exists('amr_dig_deeper')) { // *** needs work still
								if (WP_DEBUG) echo'<br /> ** go down a level for '.$i3;
								$key2 = $key.'_'.str_replace(' ','_', $mk); /* html does not like spaces in the names*/	
								$subkeys = amr_get_next_level_down($mk, $key2, $v3);
								echo '<br /> **** got back '.$subkeys;
								$keys = array_merge($keys,$subkeys);
							}
							else {	
								$mkey = $key.'-'.str_replace(' ','_', $i3); /* html does not like spaces in the names*/
								$keys[] = $mkey;
								$orig_mk[$mkey] = $mk;
								echo '<br />'.__('Added complex meta to report DB: ','amr-users').$mkey;
								}
							}
						}
					else { 
						if (!isset ($keys[$key])) {
							$keys[$key] = $key;
							$orig_mk[$key] = $mk;
							echo '<br />'.__('Added meta to report DB: ','amr-users').$key;
						}
					}
				}	
				else {
					if (!isset ($keys[$mk])) {
						$keys[$mk] = $mk;
						$orig_mk[$mk] = $mk;			// same same			
						echo '<br />'.__('Added to report DB: ','amr-users').$mk;
					}
				}
			}
			
	}		
	unset($all);
	ausers_update_option('amr-users-original-keys', $orig_mk);
	echo '<br />';

return ($keys);	
}
/* -------------------------------------------------------------------------------------------------------------*/	
function ameta_rebuildnicenames (){
	global $wpdb;
/*  */
//	amr_message (__('Rebuilding List of possible fields.  This could take a while - I have to query evey meta record, of which there can be multiple for each main record.  Please be patient...', 'amr-users'));
	/* check if we have some options already in Database. - use their names, if not, use default, else overwrite .*/
	flush(); /* try does not always work */
	$oldnn = ausers_get_option('amr-users-nicenames');
	$nn = ameta_defaultnicenames();  /* get the default list names required */

	/*	Add any new fields in */
	unset($list);
	$list = amr_get_alluserkeys();  /* maybe only do this if a refresh is required ? No only happens on admin anyway ? */

	echo '<h3>'.__('Try to make some nicer names:', 'amr-users').'</h3>';	
	/**** wp has changed - need to alllow for prefix now on fields.  Actually due to wpmu - keep the prefix, let the user remove it!  */
	foreach ($list as $i => $v) {
		if (empty( $nn[$v])) 	{ /* set a reasonable default nice name */
			if (!empty($oldnn[$v])) {
				$nn[$v] = $oldnn[$v];
				echo '<br />'. sprintf(__('Use existing name %s for %s', 'amr-users'),$nn[$v],$v);
			}
			else {  // take the last part of the field only - no not nice too unpredictable
				//$lastdash = strripos($v,'-');
				//$nn[$v] = substr($v, $lastdash);
				$nn[$v] = str_replace('s2member_custom_fields','s2m',$v); // if it is a s2member field - reduce length of name
				$nn[$v] = (str_replace('s2member', 's2m',$nn[$v]));	
				$nn[$v] = (str_replace('capabilities', 'Cap',$nn[$v]));	
				$nn[$v] = (str_replace('-', ' ',$nn[$v]));		
		//		if (isset ($wpdb->prefix)) {$nn[$v] = str_replace ($wpdb->prefix, '', $nn[$v]);} 
				/* Note prefix has underscore*/
				$nn[$v] = ucwords (str_replace('_', ' ',$nn[$v]));
				echo '<br />'. sprintf(__('Created name %s for %s', 'amr-users'),$nn[$v],$v);
			}
		}
	}
	unset($list);
	
	amr_check_for_table_prefixes($nn) ;
	ausers_update_option('amr-users-nicenames',$nn);
	$amr_nicenames = $nn;
	return($nn);
}
/* ----------------------------------------------------------------------------------- */	
function amr_check_for_table_prefixes ($nn) {
// use a field that is always there and has the table prefixes
	$prefixes_in_use = array();
	$checkfield = 'user-settings-time';
	foreach ($nn as $i=> $n) {
		if (stristr($i, $checkfield)) {
			$prefixes_in_use[] = str_replace($checkfield, '', $i);
		}
	}
	ausers_update_option('amr-users-prefixes-in-use', $prefixes_in_use);
}
/* ----------------------------------------------------------------------------------- */	
function amru_on_load_page() {
	global $pluginpage;
		//ensure, that the needed javascripts been loaded to allow drag/drop, expand/collapse and hide/show of boxes
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');

		//add several metaboxes now, all metaboxes registered during load page can be switched off/on at "Screen Options" automatically, nothing special to do therefore

	}
/* ---------------------------------------------------------------*/
function list_configurable_lists() {
global $amain,$ausersadminurl;
	$url = remove_query_arg(array('deletelist','copylist'));	
	echo '<form action="'.$url.'" method="get" style="width: 200px; display:inline;  ">'
	.'<input type="hidden" name="page" value="ameta-admin.php"/>' 
	.'<select  class="subsubsub" id="list" name="ulist" >';

	if (isset($_GET['ulist'])) $current= (int) $_GET['ulist'];
	else $current=1;
 	if (isset ($amain['names'])) {
		foreach ($amain['names'] as $i => $name) {
		//	for ($i = 1; $i <= $amain['no-lists']; $i++)	{	
					echo '<option value="'.$i.'"';
					if ($i === $current) echo ' selected="selected" ';
					echo '>'.$amain['names'][$i].'</option>';
			}
		};
	echo '</select>
	<input id="submit" style= "float:left;" class="button-secondary subsubsub" type="submit" value="';
	_e('Configure', 'amr-users'); 
	echo '" /></form>';	
	return;
}	
/* ----------------------------------------------------------------------------------- */	
function ausers_publiccheck() {
	global $ausersadminurl;
	echo '<div class="error fade">';
	_e('Please check the new user list public/private settings.', 'amr-users');
	_e('User list shortcodes will fail privacy check if the requested list is not public.', 'amr-users');
	echo ' <a href="'.$ausersadminurl.'"';
	_e('Do it','amr-users');
	echo '</a>&nbsp;';
	_e('Click update on user lists setting page to remove this message.','amr-users');
	echo '</div>';
}
/* ----------------------------------------------------------------------------------- */	
function amr_meta_menu() { /* parent, page title, menu title, access level, file, function */
	/* Note have to have different files, else wordpress runs all the functions together */
	global $amain,$amr_pluginpage;
	global $ausersadminurl;

/*	if (!current_user_can('edit_users')) return; */

	

	if (is_network_admin()) 
		$settings_page = 'settings.php';
	else {
		if (!is_plugin_active('amr-users/amr-users.php') ) return;
		$settings_page = 'options-general.php';
	}
	
	$amr_pluginpage = add_submenu_page($settings_page, 
			'Configure User Listings', 'User Lists Settings', 'manage_options',
			'ameta-admin.php', 'amrmeta_options_page');
		
		add_action('load-'.$amr_pluginpage, 'amru_on_load_page');
		add_action('admin_init-'.$amr_pluginpage, 'amr_load_scripts' );
		
	//	add_action('admin_print_styles-'.$pluginpage, 'add_ameta_stylesheet'); 
	//      They above caused the whole admin menu to disappear, so revert back to below.
		add_action( 'admin_head-'.$amr_pluginpage, 'ameta_admin_style' );
	 
		$amain = ameta_no_lists();  /*  Need to get this early so we can do menus */
		if (!isset($amain['checkedpublic'])) add_action('admin_notices','ausers_publiccheck');
		
		if (current_user_can('list_users') or current_user_can('edit_users'))  {
			if ((isset ($amain['no-lists'])) & (isset ($amain['names']))) { /* add a separate menu item for each list */
				//for ($i = 1; $i <= $amain['no-lists']; $i++)	{	
				foreach ($amain['names'] as $i => $name) {
					if (isset ($amain['names'][$i])) {
						$page = add_submenu_page(
						'users.php', // parent slug
						__('User lists', 'amr-users'), // title
						$amain['names'][$i], //menu title
						'list_users', // capability
						//add_query_arg('ulist',$i,'ameta-list.php'),//?ulist='.$i, //menu slug - must be ? why ??, priv problem if &
						'ameta-list.php?ulist='.$i, //menu slug - must be ? why ??, priv problem if &
						'amr_list_user_meta'); // function
						
					  /* Using registered $page handle to hook stylesheet loading */
						add_action( 'admin_print_styles-' . $page, 'add_ameta_stylesheet' );
					}
				}
			}
		}
	
	}
/* ----------------------------------------------------------------------------------- */
function amr_remove_footer_admin () {
	echo '';
	}	
/* ----------------------------------------------------------------------------------- */
function amr_handle_copy_delete () {	
	global $amain, $aopt;
	if (!current_user_can('administrator')) {
		echo 'Inadequate access';
		return;
	}
	if (isset($_GET['copylist'])) {  	
		$source = (int) $_REQUEST['copylist'];
		if (!isset($amain['names'][$source])) echo 'Error copying list '.$source; 
		$next = 1;  // get the current max index
		foreach($amain['names'] as $j=>$name) { 
			$next = max($next,$j);
		}
		$next = $next +1;
		//
		foreach($amain as $j=>$setting) {
			if (is_array($setting)) { echo '<br />copying '.$j.' from list '.$source;
				if (!empty($amain[$j][$source]) ) 
					$amain[$j][$next] = $amain[$j][$source];
			}
		}
		$amain['names'][$next] .= __(' - copy');
		$amain['no-lists'] = count($amain['names']);
		if (!empty($aopt['list'][$source]) ) {
					echo '<br />copying settings from list '.$source;
					$aopt['list'][$next] = $aopt['list'][$source];
		}
		
	}
	elseif (isset($_GET['deletelist'])) { 
		$source = (int) $_REQUEST['deletelist'];
		
		if (!isset($amain['names'][$source])) echo 'Error deleting list '.$source; 
		else {
			foreach($amain as $j=>$setting) {
				if (is_array($setting)) { echo '<br />deleteing '.$j.' from list '.$source;
					if (!empty($amain[$j][$source]) ) 
						unset ($amain[$j][$source]);
				}
			}
		}
		
		$amain['no-lists'] = count($amain['names']);
		if (!empty($aopt['list'][$source]) ) { 
			echo '<br />deleting list '.$source;
			unset($aopt['list'][$source]);
		}
		$acache = new adb_cache();
		$acache->clear_cache ($acache->reportid($source) );
		
	}
	if (!empty($_GET)) {
		ausers_update_option ('amr-users-no-lists', $amain);
		ausers_update_option ('amr-users', $aopt); 
	}
}	
/* ----------------------------------------------------------------------------------- */
function amrmeta_options_page() {
	global $aopt;
	global $amr_nicenames;
	global $pluginpage;
	global $amain;
	
	if (!empty($aopt)) amr_check_for_upgrades();  // so we only do if an upgrade

	if (isset($_REQUEST['ulist']) ) 	$ulist = (int) $_REQUEST['ulist'];	
	if (isset($_REQUEST['csv']) ) 		$ulist = (int) $_REQUEST['csv'];	
//	if (isset($_REQUEST['csvfiltered']) ) $ulist = (int) $_REQUEST['csvfiltered'];	
	
	echo '<div class="wrap">';
	if (!( current_user_can('manage_options') )) 
		wp_die(__('You do not have sufficient permissions to update list settings.'));
	if (isset($_REQUEST['news']))  { /*  */	
		amr_feed('http://wpusersplugin.com/feed/', 3, __('amr wpusersplugin news', 'amr-users'));
		amr_feed('http://webdesign.anmari.com/feed/', 3, __('other anmari news', 'amr-users'));
		return;	
		}
	elseif (isset($_POST['trashlog']) )  { /*  jobs havign a problem - allow try again option */
		check_admin_referer('amr-meta');
		$c = new adb_cache();
		$c->delete_all_logs();
		//return;	
		}	
	elseif (isset($_POST['trashcache']) )  { /*  jobs havign a problem - allow try again option */
		check_admin_referer('amr-meta');
		$c = new adb_cache();
		$c->clear_all_cache();
		//return;	
		}	
	elseif (isset($_POST['trashcachestatus']) )  { /*  jobs havign a problem - allow try again option */
		check_admin_referer('amr-meta');
		amr_trash_the_cache ();
		//return;	
		}
	elseif (isset ($_POST['reset'])){ 
		check_admin_referer('amr-meta');
		amr_meta_reset(); return;}	
	elseif (isset ($_REQUEST['rebuildback'])) { 
			check_admin_referer('amr-meta');
			if (isset($_REQUEST['rebuildreal'])) {
				amr_request_cache_with_feedback($_REQUEST['rebuildreal']);
				}
			else 
				amr_request_cache_with_feedback(); 
		}/* then we have a request to kick off run */
	elseif (isset ($_REQUEST['rebuildreal'])) { /* can only do one list at a time in realtime */
			check_admin_referer('amr-meta');
			$ulist = (int) $_REQUEST['rebuildreal'];
			amr_rebuild_in_realtime_with_info ($ulist);
			//echo amr_build_cache_for_one($_REQUEST['rebuildreal']); 
			//echo '<h2>'.sprintf(__('Cache rebuilt for %s ','amr-users'),$_REQUEST['rebuildreal']).'</h2>'; /* check that allowed */
			//echo au_view_link(__('View Report','amr-users'), $_REQUEST['rebuildreal'], __('View the recently cached report','amr-users'));
			return;
		}/* then we have a request to kick off cron */

	else {	
		echo '<div id="icon-users" class="icon32">
			<br/>
		</div>';	
		amrmeta_admin_header(); 
		$url = remove_query_arg(array('deletelist','copylist'));		
		echo '<form style="clear:both;" method="post" action="';
		esc_url($url); 
		echo'">';
		wp_nonce_field('amr-meta');
		ameta_options();
		if (isset ($_POST['action']) and  ($_POST['action'] == "save")) { /* Validate num of lists if we have etc and save.  Need to do this early */
				check_admin_referer('amr-meta');
				if ((isset($_REQUEST['am_page']))  and ($_REQUEST['am_page'] === 'overview'))
					amrmeta_validate_overview();
				else
					amrmeta_validate_mainoptions();
		}
			/* Now we know the number of lists, we can do the header */
		else amr_handle_copy_delete();
		
		if (isset($_REQUEST['am_page'])) {
				//check_admin_referer('amr-meta');
				if ($_REQUEST['am_page'] === 'overview') {
					//amr_mimic_meta_box('nicename_help', __('Nice Name Instructions').' '.__('(click to open)'), 'amrmeta_nicenameshelp');
					amr_meta_overview_page(); /* else do the main header page */				
					}
				elseif ($_REQUEST['am_page'] === 'nicenames') {
					//amr_mimic_meta_box('nicename_help', __('Nice Name Instructions').' '.__('(click to open)'), 'amrmeta_nicenameshelp');
					amrmeta_nicenames_page();					
					}
				elseif ($_REQUEST['am_page'] ==='cachelog')  { /*  */	
					$c = new adb_cache();
					echo $c->cache_log();				
					echo alist_trashlogs ();								
				}
				elseif ($_REQUEST['am_page'] ==='cachestatus')  { /*  */					
					$c = new adb_cache();
					$c->cache_status();										
					echo alist_rebuild();
					echo alist_trashcache_status();
					echo alist_trashcache ();
					echo alist_trashlogs ();					
				}
				elseif ($_REQUEST['am_page'] ==='rebuildcache')  { /*  */	
					check_admin_referer('amr-meta');
					amr_request_cache_with_feedback(); 				
				}
				elseif ($_REQUEST['am_page'] ==='rebuildwarning')  { /*  */	
					check_admin_referer('amr-meta');
					amr_rebuildwarning($_REQUEST['ulist']); 			
				}
				elseif ($_REQUEST['am_page'] ==='testyourdb')  { /*  */	
					check_admin_referer('amr-meta');
					amr_test_your_db(); 			
				}
				elseif ($_REQUEST['am_page'] === 'custom_navigation') { 
					amrmeta_custom_navigation_page($ulist);
					}
			}
			elseif (!empty($_GET['ulist']) ) {				
				//amr_mimic_meta_box('config_help', __('Configuration Instructions').' '.__('(click to open)'), 'amrmeta_confighelp');
				$ulist = (int) $_GET['ulist'];		
				if (amr_users_can_edit ('filtering')) {
					amrmeta_filtering_page($ulist);
				}

				else 	
					amrmeta_listfields_page($ulist);
					
				}
			elseif (isset($_GET['csv']) or isset($_GET['csvfiltered'])  ) {
				
				if (empty($amain['public'][$ulist])) { 
					check_admin_referer('amr-meta');
					$tofile = false;
					}
				else $tofile = true;	
				if (isset($_GET['csvfiltered'])) 
					$csv = amr_generate_csv($ulist, true, true, 'txt',"'",chr(9),chr(13).chr(10) ,$tofile);
				/* $strip_endings=false, $strip_html = false, $suffix='csv', $wrapper='"', $delimiter=',', $nextrow='\r\n' */
				else 
					$csv = amr_generate_csv($ulist, true, false,'csv','"',',',chr(13).chr(10), $tofile );
				echo $csv;		
				}	
				
			else {	

				//amr_mimic_meta_box('main_help', __('Main Instructions').' '.__('(click to open)'), 'amrmeta_mainhelp');
				amr_meta_general_page(); /* else do the main header page */
				}
				
		echo '<div style="clear:both; width: 100%;" class="clear"> </div>
		</form>
		<div class="clear"></div>
		<div class="clear"></div>'; 
		// force a clear as admin footer sneaks up . It appears we need a bunch of them - why -seems each entity?
		add_filter ( 'admin_footer_text', 'amr_remove_footer_admin'); // desparate measures		
		}

	echo '</div><!-- end of wrap 3 -->';	
		?>
<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function($) {
		// close postboxes that should be closed
		$('.if-js-closed').removeClass('if-js-closed').addClass('closed');

		// postboxes setup
		postboxes.add_postbox_toggles('<?php echo $pluginpage;; ?>');
	});
	//]]>
</script>
		<?php	
}	//end amrmetaoption_page

/* ---------------------------------------------------------------------*/
function amrmeta_acknowledgement () {
	?>
	<ul class="subsubsub" style="float:right;">

	<li><a href="http://wpusersplugin.com/"><?php _e('Plugin site','amr-users');?></a>|</li>
	<li><a href="http://wordpress.org/extend/plugins/amr-users/"><?php _e('wordpress','amr-users');?></a>|</li>
    <li>
	<a href="http://wpusersplugin.com/feed/"><?php _e('Rss feed','amr-users');?></a></li>

</ul>
	<?php
	}
/* ---------------------------------------------------------------------*/
	//styling options page
function ameta_admin_style() {

?>
<!-- Admin styles for amr-users settings screen - admin_print_styles trashed the admin menu-->
<style type="text/css" media="screen">

table th.show {
	width: 20px;
}

legend {
	  font-size: 1.1em;
	  font-weight: bold;
}  
label { cursor: auto;
}
.widefat li label {

	width: 500px;
}
form label.lists {
	display: block;  /* block float the labels to left column, set a width */
	clear: left;
	float: left;  
	text-align: right; 
	width:40%;
	margin-right:0.5em;
	padding-top:0.2em;
	padding-bottom:1em;
	padding-left:2em;
 }
.userlistfields th a { cursor: help;}

.if-js-closed .inside {
	display:none;
}
.subsubsub span.step {
	font-weight: bold;
	font-size: 1.5em;
	color: green;
}
.tooltip {
  cursor: help; text-decoration: none;
}


</style>
	
<?php
}
