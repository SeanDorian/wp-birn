var filepath = '/wp-content/themes/BIRN-MainTheme-1.1/functions/'; //This is the filepath used in the AJAX functions
function resources(action) {
/*This function allows you to show and hide the edit resources window, as well as save your changes, with some animations.
The first action shows the window for editing, the second action hides the window, and the third action saves.*/
	if (action == 1) {//This action allows you to edit the resources, this should be changed to text.
		var resources = jQuery('#resources-content').html();
		jQuery("#resources-textarea").attr('value', resources);
		jQuery("#resources-edit").show(500).animate({opacity:1},500);
	} else if (action == 'hide') {
		jQuery('#resources-edit').animate({opacity:0},500).hide(500);
	} else if (action == 'edit') {
		var content = jQuery("#resources-textarea").attr('value')
		jQuery.ajax({
			type: 'POST',
			url: filepath + 'resources.php',
			data: {content: content, edit: true},
			success: function(data) {
				jQuery('#resources').animate({opacity:0},500,function() {
					jQuery('#resources').html(data)
				}).animate({opacity:1},500);
				jQuery('#resources-edit').animate({opacity:0},500).hide(500);
			}
		})
	}
}
function tin(action, id) {
	if (action == 1) {//This should be changed to text for easier reading
		jQuery("#tin-new").show(500).animate({opacity: 1}, 500)
	} else if (action == 'hide') {
		jQuery('#tin-new').animate({opacity: 0}, 500).hide(500);
	} else if (action == 'new') {
		var title = jQuery('#ntt').attr('value'); var post = jQuery("#ntp").attr('value');
		jQuery.ajax({
			type: 'POST',
			url: filepath + 'tin.php',
			data: {title: title, post: post, add: true},
			success: function(data) {
				jQuery('#ntt, #ntp').attr('value', '')
				jQuery('#tin-posts').animate({opacity:0},500,function() {
					jQuery('#tin-posts').html(data)
				}).animate({opacity:1},500);
				jQuery('#tin-new').animate({opacity:0},500).hide(500);
			}
		})
	} else if (action == 'edit') {
		var title = jQuery('#post-title-'+id).html(); var content = jQuery('#post-content-'+id).html();
		jQuery('#post-title-'+id).data('title', title); jQuery('#post-content-'+id).data('content', content);
		jQuery('.post-'+id).animate({opacity:0},500,function() {
			jQuery('#post-title-'+id).html('<textarea id="edit-title-'+id+'" rows="1">'+title+'</textarea>')
			jQuery('#post-content-'+id).html('<textarea id="edit-content-'+id+'" rows="10">'+content+'</textarea>')
			jQuery('#post-options-'+id+' button:eq(0), #post-options-'+id+' button:eq(1)').css('opacity','0').hide()
			jQuery('#post-options-'+id+' button:eq(2), #post-options-'+id+' button:eq(3)').show().css('opacity', '1')
		}).animate({opacity:1},500)
	} else if (action == 'cancel') {
		var title = jQuery('#post-title-'+id).data('title'); var content = jQuery('#post-content-'+id).data('content');
		jQuery('.post-'+id).animate({opacity:0},500,function() {
			jQuery('#post-title-'+id).html(title);
			jQuery('#post-content-'+id).html(content);
			jQuery('#post-options-'+id+' button:eq(2), #post-options-'+id+' button:eq(3)').css('opacity', '0').hide()
			jQuery('#post-options-'+id+' button:eq(0), #post-options-'+id+' button:eq(1)').show().css('opacity', '1')
		}).animate({opacity:1},500)
	} else if (action == 'save') {
		var title = jQuery('#edit-title-'+id).attr('value'); var content = jQuery('#edit-content-'+id).attr('value');
		jQuery.ajax({
			type: 'POST',
			url: filepath + 'tin.php',
			data: {title: title, content: content, id: id, save: true},
			success: function(data) {
				jQuery('.post-'+id).animate({
					opacity: 0
				}, 500, function() {
					jQuery('#post-title-'+id).html(title)
					jQuery('#post-content-'+id).html(content)
					jQuery('#post-options-'+id+' button:eq(2), #post-options-'+id+' button:eq(3)').css('opacity', '0').hide()
					jQuery('#post-options-'+id+' button:eq(0), #post-options-'+id+' button:eq(1)').show().css('opacity', '1')
				}).animate({opacity:1},500)
			}
		})
	} else if (action == 'delete') {
		if(confirm("Are you sure you want to delete this post?")) {
			jQuery.ajax({
				type: 'POST',
				url: filepath + 'tin.php',
				data: {id: id, delete: true},
				success: function(data) {
					jQuery('#tin-posts').animate({opacity: 0}, 500, function() {
						jQuery('#tin-posts').html(data)
					}).animate({opacity:1},500);
				}
			})
		}
	}
}
function toggleShows(x) {
//This function toggles between active and inactive shows on the shows page in the CP. 
	jQuery.ajax({
		type: 'POST',
		url: filepath + 'shows.php',
		data: {inactive: x},
		success: function(data) {
			jQuery('#view-shows').animate({opacity:0},250,function() {
				jQuery('#view-shows').html(data)
			}).animate({opacity:1},250)
		}
	});
}
function Inactivate(id) {
	if(confirm("Are you sure you want to inactivate one or more shows?")) {
		jQuery.ajax({
			type: 'POST',
			url: filepath + 'shows.php',
			data: {remove: id},
			success: function() {
				var element = id == 'all' ? '#view-shows' : '#id-'+id;
				jQuery(element).animate({opacity:0},250).hide(250);
			}
		});
	}
}
function Activate(id) {
	if(confirm("Are you sure you want to activate this show?")) {
		jQuery.ajax({
			type: 'POST',
			url: filepath + 'shows.php',
			data: {activate: id},
			success: function() {
				jQuery('#id-'+id).animate({opacity:0},250).hide(250)
			}
		});
	}
}
function newShow(action) {
	if (action == 'view') { jQuery('#new-show').show();	jQuery('#show-title').select() }
	if (action == 'cancel') { jQuery('#new-show').hide() }
}
function profile(action, id) {
	//This function is used to display the different sections in the profile page
	jQuery('.profile-options button.selected').removeClass('selected')
	jQuery('.profile-options button').eq(action).addClass('selected')
	var url = filepath; //We need to set this because we have to make changes to it and you don't want to change the filepath variable.
	switch(action) {
		case 0: url += 'profile-view.php';break;
		case 1: url += 'profile-rs.php';break;
		case 2: url += 'profile-strikes.php';break;
		case 3: url += 'profile-comments.php';break;
		case 4: url += 'profile-training.php';break;
		case 5: url += 'profile-edit.php';break;
	}
	jQuery('#user-view').animate({opacity:0},300,function() {
		jQuery.ajax({
			type: "GET",
			url: url,
			data: {user: id},
			success: function(data) { jQuery('#user-view').html(data).animate({opacity:1},300) }
		})
	})
}
function strikes(id,action,type) {
	//This function toggles between the different sections on the strikes section, as well as add/delete/resolve/unresolve strikes.
	//Eventually, I want to be able to edit individual strikes.
	var sType = jQuery('#strike-type option:selected').text();
	var sSever = jQuery('#strike-severity').attr('value');
	var sDate = jQuery('#strike-date').attr('value');
	var sComment = jQuery('#strike-comment').attr('value');
	jQuery.ajax({
		type: "POST",
		url: filepath + 'strikes-edit.php',
		data: {
			user: id,
			action: action,
			type: type,
			sType: sType,
			sSever: sSever,
			sDate: sDate,
			sComment: sComment
		},
		success: function(data) { jQuery('#page-content').html(data) }
	})
}
function editStrikes(id,action,post) {
	if(confirm('Are you sure you want to '+action+' this strike?')) {strikes(id,action,post);}
}
function editComments(id, action) {
	var comment = jQuery('#user-comment').attr('value');
	if(action == 'delete') {
		if(confirm('Are you sure you want to delete this post?')) {
			jQuery.ajax({
				type: "POST",
				url: filepath + 'profile-comments.php',
				data: {postID: id, action: action},
				success: function(data) {jQuery('#page-content').html(data)}
			})
		}
	} else {
		jQuery.ajax({
			type: "POST",
			url: filepath + 'profile-comments.php',
			data: {postID: id, action: action, comment: comment},
			success: function(data) {jQuery('#page-content').html(data)}
		})
	} 
}
function updateTraining(user, item) {//updateTraining and updateRoles could be one function.
	var color = $('input#'+item).attr('checked') ? 'yellow' : '#ddd';
	jQuery('li#item-'+item).animate({backgroundColor: color}, 1000)
	var numSteps = 10; //You need to change this if there are more than 10 steps!
	for (i=1;i<=numSteps;i++) {
		var action = $('input#'+i).attr('checked') ? 'complete' : 'remove';
		jQuery.ajax({
			type: "POST",
			url: filepath + 'profile-training.php',
			data: {user: user, step: i,	action: action}
		})
	}
}
function updateRoles(user, item) {//updateTraining and updateRoles could be one function.
	var color = $('input#'+item).attr('checked') ? 'yellow' : '#ddd';
	jQuery('li#item-'+item).animate({backgroundColor:color},1000)
	var numRoles = 10; //You need to change this if there are more than 10 roles!
	for (i=1;i<=numRoles;i++) {
		var action = $('input#'+i).attr('checked') ? 'complete' : 'remove';
		jQuery.ajax({
			type: "POST",
			url: filepath + 'profile-rs.php',
			data: {user: user,step: i,action: action}
		})
	}
}
function removeShow(id, user) {
	jQuery('#my-shows-'+id).animate({opacity:0},500).hide(500)
	jQuery.ajax({
		type: "POST",
		url: filepath + 'profile-rs.php',
		data: {user: user, id: id, action: 'remove-show'}
	})
}
function addShow(id, user, name) {
	jQuery('#show-list').append('<div class="my-shows" id="my-shows-'+id+'" style="opacity:0">'+name+'<span onclick="removeShow('+id+','+user+')">x</span></div>')
	jQuery('#my-shows-'+id).animate({opacity: 1}, 500, function() {
		jQuery('#show-input').select()
		jQuery.ajax({
			type: "POST",
			url: filepath + 'profile-rs.php',
			data: {user: user, id: id, action: 'add-show'}
		})	
	})
}
var time = '';
function checkTime() {
	var date = new Date(); var month = date.getMonth()+1;
	var day = date.getDate(); var year = date.getFullYear();
	time = month+'/'+day+'/'+year;
}
function post(action, showID, postID) {
	if (action == 'new') {
		jQuery("#post-new").show(500).animate({opacity:1},500);
	} else if (action == 'hide') {
		jQuery('#post-new').animate({opacity:0},500).hide(500);
	} else if (action == 'add') {
		var title = jQuery('#npt').attr('value');
		var post = jQuery("#npc").attr('value');
		jQuery.ajax({
			type: 'POST',
			url: filepath + 'show-posts.php',
			data: {title: title, post: post, add: true, showID: showID},
			success: function(data) {
				jQuery('#npt, #npc').attr('value', '')
				jQuery('#posts').animate({opacity: 0}, 500, function() {
					checkTime();
					jQuery('#posts').prepend('<div class="gray-box" id="post-'+data+'"><div class="BIRN-post-title">'+title+'</div><div class="BIRN-post-content">'+post+'</div><div id="post-date-'+data+'" class="BIRN-post-date">'+time+'</div><button type="button" class="big" onclick="post(&#39;edit&#39;, '+showID+', '+data+')">Edit</button><button type="button" class="big" onclick="post(&#39;delete&#39;, '+showID+', '+data+')">Delete</button><button type="button" class="big hide" onclick="post(&#39;save&#39;, '+showID+', '+data+')">Save</button><button type="button" class="big hide" onclick="post(&#39;cancel&#39;, '+showID+', '+data+')">Cancel</button></div>')
				}).animate({opacity: 1}, 1000)
				jQuery('#post-new').animate({opacity: 0}, 500).hide(500);
			}
		})
	} else if (action == 'edit') {
		var title = jQuery('#post-title-'+postID).html(); var content = jQuery('#post-content-'+postID).html();
		jQuery('#post-title-'+postID).data('title', title); jQuery('#post-content-'+postID).data('content', content);
		jQuery('#post-'+postID).animate({opacity: 0}, 500, function() {
			jQuery('#post-title-'+postID).html('<textarea id="edit-title-'+postID+'" rows="1">'+title+'</textarea>')
			jQuery('#post-content-'+postID).html('<textarea id="edit-content-'+postID+'" rows="10">'+content+'</textarea>')
			jQuery('#post-'+postID+' button:eq(0), #post-'+postID+' button:eq(1)').css('opacity', '0').hide()
			jQuery('#post-'+postID+' button:eq(2), #post-'+postID+' button:eq(3)').show().css('opacity', '1')
		}).animate({opacity: 1}, 500)
	} else if (action == 'cancel') {
		jQuery('#post-'+postID).animate({opacity: 0}, 500, function() {
			jQuery('#post-title-'+postID).html(jQuery('#post-title-'+postID).data('title'))
			jQuery('#post-content-'+postID).html(jQuery('#post-content-'+postID).data('content'))
			jQuery('#post-'+postID+' button:eq(2), #post-'+postID+' button:eq(3)').css('opacity', '0').hide()
			jQuery('#post-'+postID+' button:eq(0), #post-'+postID+' button:eq(1)').show().css('opacity', '1')
		}).animate({opacity: 1}, 500)
	} else if (action == 'save') {
		var title = jQuery('#edit-title-'+postID).attr('value'); var content = jQuery('#edit-content-'+postID).attr('value');
		var edit = jQuery.ajax({
			type: 'POST',
			url: filepath + 'show-posts.php',
			data: {title: title, content: content, id: postID, save: true},
			success: function(data) {
				jQuery('#post-'+postID).animate({opacity: 0}, 500, function() {
					jQuery('#post-title-'+postID).html(title)
					jQuery('#post-content-'+postID).html(content)
					jQuery('#post-'+postID+' button:eq(2), #post-'+postID+' button:eq(3)').css('opacity', '0').hide()
					jQuery('#post-'+postID+' button:eq(0), #post-'+postID+' button:eq(1)').show().css('opacity', '1')
				}).animate({opacity: 1}, 500)
			}
		})
	} else if (action == 'delete') {
		if(confirm("Are you sure you want to delete this post?")) {
			jQuery.ajax({
				type: 'POST',
				url: filepath + 'show-posts.php',
				data: {postID: postID, delete: true},
				success: function(data) { jQuery('#post-'+postID).animate({opacity:0},500).hide(500) }
			})
		}
	}
}
var eventType = 0; //This is a reference to the currently selected event type on the production page
var eventVenue = 'All'; //This is a reference to the currently selected event venue on the production page
function production(action, id, etc) {
	if (action == 'new') {
		jQuery('#new-production').show(500).animate({opacity: 1}, 500)
		jQuery('#event-name').select()
	} else if (action == 'cancel') {
		jQuery('#new-production').animate({	opacity: 0}, 500).hide(500)
	} else if (action == 'add') {
		var eName = jQuery('#event-name').attr('value');
		var eType = jQuery('#event-type').attr('value');
		var eDate = jQuery('#event-date').attr('value');
		var eDateF = jQuery('#event-date-f').attr('value');
		var eCall = jQuery('#event-call').attr('value');
		var eDesc = jQuery('#event-desc').attr('value');
		var ePositions = [
			jQuery('#event-producer').attr('checked'),
			jQuery('#event-engineer').attr('checked'),
			jQuery('#event-assistant').attr('checked'),
			jQuery('#event-photographer').attr('checked'),
			jQuery('#event-videographer').attr('checked'),
			jQuery('#event-dj').attr('checked'),
			jQuery('#event-interviewer').attr('checked'),
			jQuery('#event-reviewer').attr('checked'),
			jQuery('#event-observer').attr('checked')
		];
		if (eType != 'Unselected' && eDate != 'Date' && eName != 'Event Name') {
			jQuery.ajax({
				type: 'POST',
				url: filepath+'events.php',
				data: {eName: eName, type: eType, date: eDate, call: eCall, desc: eDesc, positions: ePositions, action: action, date_formatted: eDateF},
				success: function(data) {
					jQuery('#event-name, #event-desc').attr('value', '');
					jQuery('#event-producer,#event-engineer,#event-assistant,#event-photographer,#event-videographer,#event-dj,#event-interviewer,#event-reviewer,#event-observer').attr('checked', '');
					jQuery('#event-date').attr('value', 'Date');
					jQuery('#event-type').attr('value', 'Unselected');
					jQuery('#event-name').select();
					jQuery('#content').animate({opacity: 0}, 500, function() {
						jQuery('#content').html(data)
					}).animate({opacity: 1}, 500)
				}
			});
		} else { alert('Make sure you have filled out all of the fields!') };
	} else if (action == 'Subscribe') {
		jQuery.ajax({
			type: 'POST',
			url: filepath+'events.php',
			data: {user: id, action: action},
			success: function(data) {
				alert('You have been subscribed! You will now begin receiving emails about new upcoming events.')
				jQuery('#sub-button').animate({opacity: 0}, 500, function() {
					jQuery('#sub-button').text('Unsubscribe')
				}).animate({opacity: 1}, 500).attr('onClick', 'production("Unsubscribe", '+id+')')
			}
		});
	} else if (action == 'Unsubscribe') {
		jQuery.ajax({
			type: 'POST',
			url: filepath+'events.php',
			data: {user: id, action: action},
			success: function(data) {
				alert('You have been unsubscribed! You will no longer receive emails about new upcoming events.')
				jQuery('#sub-button').animate({opacity: 0}, 500, function() {
					jQuery('#sub-button').text('Subscribe')
				}).animate({opacity: 1}, 500).attr('onClick', 'production("Subscribe", '+id+')')
			}
		});
	} else if (action == 'A') {
		jQuery('.production-filter:eq(0) button').css('background', 'transparent')
		jQuery('.production-filter:eq(0) button:eq('+etc+')').css('background', '#ddd')
		eventVenue = id;
		jQuery('#event-list').animate({opacity: 0}, 500, function() {
			if (eventType == -1) {
				jQuery('.es-0, .es-1, .es-2').show()
				if(id != 'All') {
					jQuery('.el').each(function() {
						var text = jQuery(this).children('td').eq(2).text()
						if (text != id) { jQuery(this).hide() }
					})
				}
			} else {
				jQuery('.es-'+eventType).show()
				if (id != 'All') {
					jQuery('.es-'+eventType).each(function() {
						var text = jQuery(this).children('td').eq(2).text()
						if (text != id) { jQuery(this).hide() }
					})
				}
			}
		}).animate({opacity: 1}, 500)
	} else if (action == 'B') {
		eventType = id;
		jQuery('.production-filter:eq(1) button').css('background', 'transparent')
		jQuery('.production-filter:eq(1) button:eq('+(id+1)+')').css('background', '#ddd')
		jQuery('#event-list').animate({opacity: 0}, 500, function() {
			if(id == -1) {
				jQuery('.es-0, .es-1, .es-2').show()
				if(eventVenue != 'All') {
					jQuery('.el').each(function() {
						var text = jQuery(this).children('td').eq(2).text()
						if (text != eventVenue) { jQuery(this).hide() }
					})
				}
			} else {
				jQuery('.es-0, .es-1, .es-2').hide()
				jQuery('.es-'+id).show()
				if(eventVenue != 'All') {
					jQuery('.es-'+id).each(function() {
						var text = jQuery(this).children('td').eq(2).text()
						if (text != eventVenue) { jQuery(this).hide() }
					})
				}
			}
		}).animate({opacity: 1}, 500)
	} else if (action == 'show event') {
		jQuery('#content').animate({opacity: 0}, 500, function(){
			jQuery.ajax({
				type: 'POST',
				url: filepath + 'events-details.php',
				data: {action: action, eventID: id},
				success: function(data) { jQuery('#content').html(data).animate({opacity: 1}, 500) }
			})
		})
	}
}
function eventOptions(action, id, recruit) {
	if (action == "Go Back") {
		jQuery('#content').animate({opacity: 0}, 500, function(){
			jQuery.ajax({
				type: 'POST',
				url: filepath + 'events.php',
				success: function(data) { jQuery('#content').html(data).animate({opacity: 1}, 500) }
			})
		})
	} else if (action == "cancel" || action == "resume") {
		if(confirm('Are you sure you want to '+action+' this event?')) {
			jQuery('#content').animate({opacity: 0}, 500, function(){
				jQuery.ajax({
					type: 'POST',
					url: filepath + 'events-details.php',
					data: {action: action, eventID: id},
					success: function(data) { jQuery('#content').html(data).animate({opacity: 1}, 500) }
				})
			})
		}
	} else if (action == 'edit' || action == 'cancel edit' || action == 'recruit') {
		jQuery('#content').animate({opacity: 0}, 500, function(){
			jQuery.ajax({
				type: 'POST',
				url: filepath + 'events-details.php',
				data: {action: action, eventID: id},
				success: function(data) { jQuery('#content').html(data).animate({opacity: 1}, 500) }
			})
		})
	} else if (action == 'save edit') {
		var bigchange = jQuery('#big-change').attr('value');
		var eName = jQuery('#event-name-edit').attr('value');
		var eType = jQuery('#event-type-edit').attr('value');
		var eDate = jQuery('#event-date-edit').attr('value');
		var eDateF = jQuery('#event-date-f-edit').attr('value');
		var eCall = jQuery('#event-call-edit').attr('value');
		var eDesc = jQuery('#event-desc-edit').attr('value');
		var ePositions = [
			jQuery('#event-producer-edit').attr('checked'),
			jQuery('#event-engineer-edit').attr('checked'),
			jQuery('#event-assistant-edit').attr('checked'),
			jQuery('#event-photographer-edit').attr('checked'),
			jQuery('#event-videographer-edit').attr('checked'),
			jQuery('#event-dj-edit').attr('checked'),
			jQuery('#event-interviewer-edit').attr('checked'),
			jQuery('#event-reviewer-edit').attr('checked'),
			jQuery('#event-observer-edit').attr('checked')
		];
		jQuery.ajax({
			type: 'POST',
			url: filepath+'events-details.php',
			data: {eName: eName, type: eType, date: eDate, call: eCall, desc: eDesc, positions: ePositions, action: action, date_formatted: eDateF, eventID: id, bigchange: bigchange},
			success: function(data) {
				jQuery('#content').animate({opacity: 0}, 500, function() {
					jQuery('#content').html(data).animate({opacity: 1}, 500)
				})
			}
		});		
	} else if (action == 'apply') {
		var ePositions = [
			jQuery('#producer').attr('checked'),
			jQuery('#engineer').attr('checked'),
			jQuery('#assistant').attr('checked'),
			jQuery('#photographer').attr('checked'),
			jQuery('#videographer').attr('checked'),
			jQuery('#dj-top').attr('checked'),
			jQuery('#dj-interview').attr('checked'),
			jQuery('#dj-review').attr('checked'),
			jQuery('#observers').attr('checked')
		];
		jQuery.ajax({
			type: 'POST',
			url: filepath+'events-details.php',
			data: {positions: ePositions, action: action, eventID: id},
			success: function(data) {
				jQuery('#content').animate({opacity: 0}, 500, function() {
					jQuery('#content').html(data).animate({opacity: 1}, 500)
				})
			}
		});
	} else if (action == 'start recruit') {
		var recruitList = [];
		var notRecruitList = [];
		jQuery('#events-recruit input').each(function(){
			if(jQuery(this).attr('checked')) {
				var thisPos = jQuery(this).attr('class');
				var thisID = jQuery(this).attr('title')
				recruitList.push({label: thisPos, id: thisID});
			} else {
				var thisPos = jQuery(this).attr('class');
				var thisID = jQuery(this).attr('title')
				notRecruitList.push({label: thisPos, id: thisID});
			}
		})
		jQuery.ajax({
			type: 'POST',
			url: filepath+'events-details.php',
			data: {action: action, eventID: id, recruit: recruitList, notRecruit: notRecruitList},
			success: function(data) {
				alert('An email has been sent to those who have been recruited.')
				jQuery('#content').animate({opacity: 0}, 500, function() {
					jQuery('#content').html(data).animate({opacity: 1}, 500)
				})
			}
		});
	}
}
function bigchange() {$('#big-change').attr('value','true')}
jQuery(document).ready(function() {
	if(jQuery("#featured")) {
		jQuery("#featured").tabs({fx:{opacity: "toggle"}}).tabs("rotate", 5000, true).hover(  
			function() { jQuery("#featured").tabs("rotate",0,true) },  
			function() { jQuery("#featured").tabs("rotate",5000,true) }  	
		);
	};//This is broken
	jQuery("#tabs").tabs();
	jQuery("#tabs > #secondary-menu > ul, #tabs > #secondary-menu > ul > li").removeClass();
	jQuery('#new-show, #new-production').find('input[type="text"]').click(function() { jQuery(this).select() })
	jQuery('#show-title').blur(function() {	jQuery('#show-djs').text('') })
	jQuery('#show-tabs, #edit-show-tabs').tabs()
	jQuery('#event-date').datepicker({
		dateFormat: 'DD, MM dd, yy', //Makes the date look nice
		altField: '#event-date-f', 
		altFormat: "yy/mm/dd (DD)" //Formats the date so that it sorts properly
	})
});