<?php
include("../../../../wp-blog-header.php");
$id = $_GET['user'];
//Only Permission 3 can add and edit strikes, everyone else can read them
?>
<div id="page-header">
	<button type="button" class="big left" onclick="strikes('<?php echo $id; ?>','view','unresolved')">Unresolved</button>
	<button type="button" class="big right" onclick="strikes('<?php echo $id; ?>','view','resolved')">Resolved</button>
	<button type="button" class="big" style="float:right" onclick="strikes('<? echo $id; ?>','new')">Add Strike</button>
</div>
<div id="page-content">
	<?php include ('strikes-edit.php') ?>
</div>
<script>
jQuery('#page-header button:first').css('background-color', '#eee')
jQuery('#page-header button').click(function() {
	jQuery('#page-header button').css('background-color', 'transparent')
	jQuery(this).css('background-color', '#eee')
})
</script>