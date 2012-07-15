<?php
/*
The csv file functions for the plugin
*/
/* ---------------------------------------------------------------------- */
function amr_users_show_csv_link($ulist) {	//  * Return the full path to the  file 
global $amain;

	$text = (empty ($amain['csv_text'] ) ? '' : $amain['csv_text']);
	
	$csvfile = amr_users_setup_csv_filename($ulist, 'csv');
	$url = amr_users_get_csv_url($csvfile);
	if (file_exists($csvfile))	return (
	'<div class="csvlink" style="float:left;">
	<p><a class="csvlink" title="'.__('Csv Export','amr-users').'" href="'.$url.'">'
	.$text
	.'</a></p>
	</div>'
	) ;
	else return '';
}	
/* ---------------------------------------------------------------------- */
function amr_users_show_refresh_link($ulist) {	//  * Return the full path to the  file 
global $amain;

	$text = (empty ($amain['refresh_text'] ) ? '' : $amain['refresh_text']);

	$url = remove_query_arg(array('sort','dir','listpage'));
	$url = add_query_arg(array('refresh'=>'1'),$url);
	return (
	'<div class="refreshlink" style="float:left;">
	<p><a class="refreshlink" title="'.__('Refresh Cache','amr-users').'" href="'.$url.'">'
	.$text
	.'</a></p>
	</div>'
	) ;

}
/* ---------------------------------------------------------------------- */
function amr_users_to_csv($ulist, $text, $suffix) {  // get the file name and write the csv text
	$csvfile = amr_users_setup_csv_filename($ulist, $suffix);
	amr_users_delete_old_csv_files (20); // delete any old csv file;
	$success = file_put_contents($csvfile, $text.chr(13), LOCK_EX);
	if ($success) return ($csvfile );
	else return (false);
}
/* ---------------------------------------------------------------------------------- */
function amr_users_get_csv_path() { //	 * Attempt to create the log directory if it doesn't exist.
	$upload_dir = wp_upload_dir();
	$remove = stristr($upload_dir['basedir'],'wp-content');
	$remove = str_replace('wp-content','',$remove);
	$csv_path = str_replace($remove, '', $upload_dir['basedir']). '/uploads/users_csv';		

	if (!file_exists($csv_path)) { /* if there is no folder */
		if (wp_mkdir_p($csv_path, 0705)) {
			printf('<br/>'
				.__('Your csv directory %s has been created','amr-users'),'<code>'.$csv_path.'</code>');
			file_put_contents($csv_path.'/index.php', 'Silence is golden', LOCK_EX);
			return $csv_path;
		}
		else {
				echo ( '<br/>'.sprintf(__('Error creating csv directory %s. Please check permissions','amr-users'),$csv_path)); 
				return $upload_dir;
			}
	}		
	return $csv_path;
}
/* ---------------------------------------------------------------------------------- */
function amr_users_get_csv_url($csvfile) { //	 * Attempt to create the log directory if it doesn't exist.
	$upload_dir = wp_upload_dir();
	$upload_url = $upload_dir['baseurl'];
	$csvurl = str_replace($upload_dir,$upload_url, $csvfile); // get the part after theupload dir
	return $csvurl;
}

/* ---------------------------------------------------------------------- */
function amr_users_setup_csv_filename($ulist, $suffix) {	//  * Return the full path to the  file 
	$today 		= date('Ymd');
	$csvfile 	= amr_users_get_csv_path() .'/'
//	.$today
	.'user_list_'.$ulist
	.'.'.$suffix;
	return $csvfile ;
}
/* ---------------------------------------------------------------------------------- */
function amr_list_csv_files () { // may not need
	// Define the folder to clean
	// (keep trailing slashes)
	$Folder  = amr_users_get_csv_path();
	$url = amr_users_get_log_url();
	$files = scandir($Folder); 
	
	// Find all files of the given file type
	foreach ( $files as $i=>$Filename) {
		if (!stristr($Filename,'.csv'))   // if not  atxt file, then skip  ?? OR .txt - NEEDS MOD
			unset($files[$i]);
	}
	
	echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br/></div><h2>';
	 _e('List user log files','amr-users'); 
	echo '</h2><div class="postbox" style="padding: 1em;">';
	
	if (count($files) > 0) {
	echo '<form method="post" action="'. esc_url($_SERVER['PHP_SELF']).'?page=amr_user_templates_settings_page">';
	wp_nonce_field( 'amr-users','amr-users' ); 
	echo '<input style="clear:both; margin: 1em; float:right;" class="button-primary" type="submit" value="'
	.__('Clear csvfiles','amr-users').
	'" name="deletecsvfiles"/></form>';
	}	
	echo '<a href="'.remove_query_arg('viewcsvfiles').'">'.__('back').'</a><br/>';

	// Find all files of the given file type
	foreach ( $files as $Filename) {
		echo '<br/><a target="_blank" href="'.$url.'/'.$Filename.'">'.$Filename.'</a><br/>';
	}
	
	echo '</div></div>';
}
/* ---------------------------------------------------------------------------------- */
function amr_users_delete_old_csv_files ($expire_days=31) { // do we really need - could just overwrite one per report
	// Define the folder to clean
	// (keep trailing slashes)
	$Folder  = amr_users_get_csv_path();
	// Filetypes to check (you can also use *.*)
	$fileTypes      = '*.csv';	 
	// Here you can define after how many
	// days the files should get deleted
	$expire_time    = $expire_days * 60*60*24; // 24 hrs * 60 mins *60 sec
	
//	$files = glob($Folder . $fileTypes);
	$files = scandir($Folder);
	 
	// Find all files of the given file type
	foreach ( $files as $Filename) {
		if (stristr($Filename,'.csv') or stristr($Filename,'.txt'))  { // if not  atxt file, then skip
			if (!stristr($Filename,'user_list'))
		    // Read file creation time
		    $FileTime = filectime($Folder.'/'.$Filename);  // need the full file name		 
		    // Calculate file age in seconds
		    if (!empty($FileTime)) 
				$FileAge = time() - $FileTime; 		
			else continue;		
		    // Is the file older than the given time span?
		    if ($FileAge > ($expire_time)){  
		        unlink($Folder.'/'.$Filename);
		    } 
		}
	}
}
/* ---------------------------------------------------------------------------------- */ 

