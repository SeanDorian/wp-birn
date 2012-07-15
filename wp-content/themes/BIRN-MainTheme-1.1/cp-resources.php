<?php
/**
 * Template Name: CP Resources
 * Description: Resources Page on the CP
 */
get_header(); ?>
<div id="primary" style="padding-right:0; width:975px" >
	<div id="resources" class="gray-box">
			<?php include('functions/resources.php');?>			
	</div>
	<div id="tin-news">
		<div id="page-header">
			<?php
			if ($permission >= 2) {
					echo '<button type="button" class="big" onclick="resources(1)">Edit Resources</button>';
			}
			if ($permission >= 3) {	
					echo '<button type="button" class="big" onclick="tin(1)">New Post</button>';
			} 
			?>	
			<div id="page-title">TIN News Posts</div>	
		</div>
		<div id="resources-right">
			<?php 
			if ($permission >= 2) {?>
				<div id="resources-edit" class="gray-box">
					<textarea id="resources-textarea" rows="10"></textarea>
					<button type="button" class="big" onclick="resources('edit')">Finish Editing</button>
					<button type="button" class="big" onclick="resources('hide')">Cancel</button>
				</div><?
			}
			if ($permission >= 3) {?>
			 	<div id="tin-new" class="hide">
					<div class="BIRN-post-title"><textarea id="ntt" rows="1"></textarea></div>
					<textarea id="ntp" rows="10"></textarea>
					<button type="button" class="big" onclick="tin('new')">Add</button>
					<button type="button" class="big" onclick="tin('hide')">Cancel</button>
				</div><?	
			}?>
			<div id="tin-posts">
				<?php include('functions/tin.php');?>			
			</div>
		</div>
	</div>
</div>
<?php include (TEMPLATEPATH . '/cp-footer.php'); ?>