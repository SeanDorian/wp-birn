<?php
/*
 Template Name: CP Shows
*/
if($_POST['add-show']) {
	$addshow = $wpdb->insert( 
		'shows', 
		array( 
			'title' => $_POST['show-title'], 
			'genre' => $_POST['show-genre'],
			'description' => $_POST['show-description'],
			'created_at' => date("m.d.y"),
			'avatar_file_name' => 'default.jpg',
			'avatar_content_type' => 'image/jpeg',
			'active' => 1
		)
	);	
}
get_header(); ?>
<div id="primary" >
	<div id="page-header">
		<button type="button" id="show-active" class="big left" onclick="toggleShows(0)">Active</button>
		<button type="button" id="show-inactive" class="big right" onclick="toggleShows(1)">Inactive</button>
		<?php if ($permission >= 3) {?>
			<button type="button" class="big" onclick="Inactivate('all')">Inactivate All</button>
			<button type="button" class="big" onclick="newShow('view')">New Show</button><?php
		}; ?>
		<div id="page-title">Shows</div>	
	</div>
	<?php if($permission >= 3) {?>
		<div id="new-show" class="gray-box">
			<input type="text" id="show-title" value="Name">
			<input type="text" id="show-djs" value="Enter DJ Names...">
			<div id="dj-list"></div>
			<button type="button" class="big" onclick="addShow()">Add Show</button>
			<button type="button" class="big" onclick="newShow('cancel')">Cancel</button>
		</div><?php
	}
	?>
	<div id="view-shows">
		<?php include ('functions/shows.php');?>
	</div>
</div>
<script>
var djs = [];
var djList = [];
</script>
<?php 
include (TEMPLATEPATH . '/cp-footer.php');
$numDJs = count_users();
for ($i = 1; $i <= $numDJs['total_users']; $i++) {
   if (get_user_by('id', $i)) {
	 $first = get_user_meta( $i, 'first_name', true); 
	  	$last = get_user_meta( $i, 'last_name', true);?>
		<script>
			djs.push({label: "<?php echo $first.' '.$last; ?>", id: "<?php echo $i; ?>"})
		</script><?php
	}
}
?>
<script>
jQuery('#show-djs').autocomplete({
	source: djs,
	focus: function (event, ui) {
		$( "#show-djs" ).val( ui.item.label );
		return false;
	},
	select: function (event, ui) {
		addDJ(ui.item.label, ui.item.id)		
	}
})
function addDJ(dj, id) {
	djList.push({label: dj, id: id});
	jQuery('#dj-list').append('<div id="dj-name-'+id+'" class="dj">'+dj+'<span id="remove" onclick="removeDJ('+id+')">X</span></div>')
	jQuery('#dj-name-'+id).show()
	jQuery('#dj-name-'+id).animate({
		opacity: 1
	}, 200, function() {
		jQuery('#show-djs').attr('value', 'Enter DJ Names...').select()
	})
}
function removeDJ(id) {
	for (i=0; i<djList.length;i++) {
		if (djList[i].id == id) {
			djList.splice(i,1);
			/*var output = '';
			for (property in djList) {
				output += djList[property].id;
			}
			alert(output)*/
			break;
		}
	}
	jQuery('#dj-name-'+id).animate({
		opacity: 0
	}, 200, function() {
		jQuery('#dj-name-'+id).remove()
	})
}
function addShow() {
	var userIDs = [];
	for (property in djList) {
		userIDs.push(djList[property].id);
	};
	var showName = jQuery('#show-title').attr('value');
	jQuery.ajax({
		type: "POST",
		url: filepath + 'shows.php',
		data: {
			userID: userIDs,
			showName: showName,
			action: 'addNewShow'
		},
		success: function(data) {
			for (i=0;i<userIDs.length;i++) {
				jQuery('#dj-name-'+userIDs[i]).remove()
			}
			djList = [];
			jQuery('#show-title').attr('value', 'Show Title')
			jQuery('#show-title').select();
			jQuery('#view-shows').html(data)
		},
		fail: function() {
			alert('fieoajfoeiajo')
		}
	})
}
</script>
