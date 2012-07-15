<?php
/**
 * Template Name: Main Show Profile
 * Description: A Main Site Template with No Sidebars
 *
 */

include (TEMPLATEPATH . '/main-header.php'); ?>
<div id="widget-section">
	<?php
	$id = $_GET['show'];
	$ashows = $wpdb->get_results( 
		"
		SELECT * 
		FROM shows
		WHERE id = $id
		ORDER BY title ASC
		"
	);
	foreach ($ashows as $ashow){
		$id = $ashow->id;
		echo 	'<div id="show-box" class="gray-box id-'.$id.'">
					<div class="BIRN-post-title"><a target="_blank" href="/shows/?show='.$id.'">'.$ashow->title.'</a></div>
					<div id="show-box-content">
						<div class="show-genre-djs">	
							<div class="show-genre">Genres: <em>'.$ashow->genre.'</em></div>
							<div class="show-djs">DJs: <em></em></div>
						</div>
						<img class="show-image-thumbnail" src="/wp-content/uploads/show_images/'.$ashow->avatar_file_name.'">
						<div>Description: <em>'.$ashow->description.'</em></div>
						
					</div>
				</div>';
	}
	?>		
</div>
<?php get_footer(); ?>