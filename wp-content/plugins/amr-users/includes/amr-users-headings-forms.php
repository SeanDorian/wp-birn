<?php

/* -------------------------------------------------------------------------------------------------------------*/
function amr_list_user_admin_headings($l){

global $amain;
global $ausersadminurl;

if ( !is_admin() ) return;
echo '<div class="wrap"><div id="icon-users" class="icon32"><br /></div><h2>';
echo $amain['names'][$l];
echo '</h2><div class="filter" >'.
	'<ul class="subsubsub" style="float:left; white-space:normal;">';

		$t = __('CSV Export','amr-users');
		$n = $amain['names'][$l];
		if (current_user_can('list_users') or current_user_can('edit_users')) {
			echo '<li style="display:block; float:left;">'
				.au_csv_link($t, $l, $n.__(' - Standard CSV.','amr-users')).'</li>';
			echo '<li style="display:block; float:left;"> |'.au_csv_link(__('Txt Export','amr-users'),
						$l.'&amp;csvfiltered',
						$n.__('- a .txt file, with CR/LF filtered out, html stripped, tab delimiters, no quotes ','amr-users')).'</li>';
			}
		if (current_user_can('manage_options')) {
			echo '<li style="display:block; float:left;"> | '
			.'<a style="color:#D54E21;" href="'.$ausersadminurl.'">'.__('Main Settings','amr-users').'</a></li>';
			echo '<li style="display:block; float:left;"> | '
			.'<a '.a_currentclass('nicenames').' href="'
			.wp_nonce_url(add_query_arg('am_page','nicenames',$ausersadminurl),'amr-meta')
			.'" title="'.__('Find fields and update nice names','amr-users').'" >'
			.__('Find Fields','amr-users').'</a></li>';
			echo '<li style="display:block; float:left;"> | '
			.au_configure_link(__('Configure this list','amr-users'), $l,$n).'</li>';
			echo '<li style="display:block; float:left;"> | '
			.au_headings_link($l,$n).'</li>';
			echo '<li style="display:block; float:left;"> | '
			.au_filter_link($l,$n).'</li>';
			echo '<li style="display:block; float:left;"> | '
			.au_custom_nav_link($l,$n).'</li>';

		}

		echo '<li style="display:block; float:left;"> | '
			.au_buildcache_link(__('Rebuild cache now','amr-users'),$l,$n)
			.'</li>';
		echo '</ul>
</div> <!-- end of filter-->
<div class="clear"></div>
</div>';

}
/* -------------------------------------------------------------------------------------------------------------*/
function alist_searchform ($i) {
global $amain;
	if (!is_rtl()) $style= ' style="float:right;" ';
	else $style= '';

	if (isset($_REQUEST['su']))
		$searchtext = esc_attr($_REQUEST['su']);
	else
		$searchtext = '';
	$text = '';
	$text .= '<div class="search-box" '.$style.'>'
//	.'<input type="hidden"  name="page" value="ameta-list.php"/>'
	.'<input type="hidden"  name="ulist" value="'.$i.'"/>';
//	echo '<label class="screen-reader-text" for="post-search-input">'.__('Search Users').'</label>';
	$text .= '<input type="text" id="search-input" name="su" value="'.$searchtext.'"/>
	<input type="submit" name="search" id="search-submit" class="button" value="'.__('Search Users').'"/>';
	$text .= '</div><div style="clear:both;"><br /></div>';
//	$text .= '</form>';
	return ($text);
}
/* -------------------------------------------------------------------------------------------------------------*/
function alist_per_pageform ($i) {
global $amain;

	$rowsperpage = amr_rows_per_page($amain['rows_per_page']);  // will check for request

	$text = '';
	$text .= '<p class="perpage-box" style="text-align: center;">'
	.'<input type="hidden"  name="ulist" value="'.$i.'"/>';
	$text .= '<label for="rows_per_page">'.__('Per page');
	$text .= '<input type="text" name="rows_per_page" id="rows_per_page" size="3" value="'.$rowsperpage.'">';
	$text .= '</label>';
	$text .= '<input type="submit" name="refresh" id="perpage-submit" class="button" value="'.__('Apply').'"/>';
	$text .= '</p>';
//	$text .= '</form>';
	return ($text);
}
/* --------------------------------------------------------------------------------------------*/
function amr_table_headings ($cols,$icols,$ulist, $sortable) {

	$html = '';
	$cols = amr_users_get_column_headings ($ulist, $cols, $icols ); // should be added to cache rather
	$cols = apply_filters('amr-users-headings', $cols,$icols,$ulist);  //**** test this

	foreach ($icols as $ic => $cv) { /* use the icols as our controlling array, so that we have the internal field names */

		if (($cv == 'checkbox')) {
			$html 	.= '<th class="manage-column column-cb check-column" >'.htmlspecialchars_decode($cols[$ic]).'</th>';
		}
		else {

			if ($sortable and (!($cv == 'checkbox')) ) 
				$v = amr_make_sortable($cv,htmlspecialchars_decode($cols[$ic]));
			else 
				$v = htmlspecialchars_decode($cols[$ic]);
			if ($cv === 'comment_count')
				$v 	.= '<a title="'.__('Explanation of comment total functionality','amr-users')
								.'"href="http://wpusersplugin.com/1822/comment-totals-by-authors/">**</a>';
						//$v .= amr_indicate_sort_priority ($cv,
						//	(empty($l['sortby'][$cv])? null : $l['sortby'][$cv]));
			$html 	.= '<th>'.$v.'</th>';
			}
		}
		$hhtml = '<tr>'.$html.'</tr>'; /* setup the html for the table headings */

	return ($hhtml);
}
/* --------------------------------------------------------------------------------------------*/
function amr_indicate_sort_priority ($colname, $orig_sort) {
	if ((!empty($_REQUEST['sort'])) and ($_REQUEST['sort'] === $colname)) {
		return (' <a style="color: green;" href="" title="'
		.sprintf(
			_x('Sorted 1%s','Indicates sort priority',  'amr-users' )
			,'1')
		.'">&uarr&darr</a>' )	;

	}

	if (!empty($orig_sort)) {
		return(' <a style="color: green;" href="" title="'
		.sprintf(
			_x('Sorted %s','Indicates sort priority',  'amr-users' )
			,$orig_sort)
		.'">&uarr;&darr;</a>' )	;

	}
	return '';
}
/* --------------------------------------------------------------------------------------------*/
function amr_make_sortable($colname, $colhead) { /* adds a link to the column headings so that one can resort against the cache */
	$dir = 'SORT_ASC';

	if ((!empty($_REQUEST['sort'])) and ($_REQUEST['sort'] === $colname)) {
		if (!empty($_REQUEST['dir'])) {
			if ($_REQUEST['dir'] === 'SORT_ASC' )
				$dir = 'SORT_DESC';
			else
				$dir = 'SORT_ASC';

		}
	}
	$link = remove_query_arg(array('refresh'));
	$link = add_query_arg('sort', $colname, $link);
	$link = add_query_arg('dir',$dir,$link);
	if (!empty($_REQUEST['rows_per_page']))
	$link = add_query_arg('rows_per_page',(int) $_REQUEST['rows_per_page'],$link);
	return('<a title="'.
	__('Click to sort.  Click again to change direction.','amr-users')
	.'" href="'.htmlentities($link).'">'.$colhead.'</a>');
}
/* --------------------------------------------------------------------------------------------*/
?>