<?php
/*
 Template Name: CP Manage Shows
*/
$uid = get_current_user_id();
$yourshows = $wpdb->get_results( 
	"
	SELECT * 
	FROM shows_users
	WHERE user_id = $uid
	"
);
get_header(); ?>

<div id="primary" >
	<div id="show-tabs" style="margin-bottom: 10px">
		<ul>
			<?php foreach ($yourshows as $ys) {
				$show = $ys->show_id;
				$listshows = $wpdb->get_results( 
					"
					SELECT * 
					FROM shows
					WHERE id = $show AND active = 1
					"
				);
				foreach ($listshows as $ls) {
					echo '<li><a href="#'.$ls->id.'">'.$ls->title.'</a></li>';
				}
			}
			?>
		</ul>
		<?php foreach ($yourshows as $ys) {
			$show = $ys->show_id;
			$listshows = $wpdb->get_results( 
				"
				SELECT * 
				FROM shows
				WHERE id = $show AND active = 1
				"
			);
			foreach ($listshows as $ls) {
				$showID = $ls->id ?>
				<div id="<?php echo $ls->id; ?>">
					<div id="edit-showtabs-<?php echo $ls->id ?>">
						<ul>
							<li><a href="#show-profile">Profile</a></li>
							<li><a href="#show-posts">Posts</a></li>
							<li><a href="#show-playlist">Playlist</a></li>
						</ul>
						<div id="show-profile">
							profile
						</div>
						<div id="show-posts">
							<div id="page-header">
								<button type="button" class="big" onclick="post('new',<?php echo $ls->id ?>)">New Post</button>
							</div>
							<div id="post-new" class="hide">
								<div class="gray-box">
									<div class="BIRN-post-title"><textarea id="npt" rows="1"></textarea></div>
									<textarea id="npc" rows="10"></textarea>
									<button type="button" class="big" onclick="post('add', <?php echo $ls->id ?>)">Add</button>
									<button type="button" class="big" onclick="post('hide')">Cancel</button>
								</div>
							</div>
							<div id="posts">
								<?php
								$showPosts = $wpdb->get_results(
									"
									SELECT *
									FROM show_posts
									WHERE Show_ID = $showID
									ORDER BY Date DESC
									"
								);
								foreach ($showPosts as $sp) { ?>
									<div class='gray-box' id="post-<?php echo $sp->ID; ?>">
										<div id="post-title-<?php echo $sp->ID; ?>" class="BIRN-post-title"><?php echo $sp->Title ?></div>
										<div id="post-content-<?php echo $sp->ID; ?>" class="BIRN-post-content"><?php echo $sp->Content ?></div>
										<div id="post-date-<?php echo $sp->ID; ?>" class="BIRN-post-date"><?php echo $sp->Time_Formatted; ?></div>
										<button type="button" class="big" onclick="post('edit', <?php echo $ls->id ?>, <?php echo $sp->ID; ?>)">Edit</button>
										<button type="button" class="big" onclick="post('delete', <?php echo $ls->id ?>, <?php echo $sp->ID; ?>)">Delete</button>
										<button type="button" class="big hide" onclick="post('save', <?php echo $ls->id ?>, <?php echo $sp->ID; ?>)">Save</button>											
										<button type="button" class="big hide" onclick="post('cancel', <?php echo $ls->id ?>, <?php echo $sp->ID; ?>)">Cancel</button>
									</div><?php
								}	
								?>
							</div>
						</div>
						<div id="show-playlist">
							The playlist feature is currently under construction.
						</div>
					</div>
				</div>
				<script>
					jQuery('#edit-showtabs-<?php echo $ls->id; ?>').tabs();
				</script><?php
			}
		}
		?>
	</div>
</div>

<?php 
include (TEMPLATEPATH . '/cp-footer.php'); ?>
