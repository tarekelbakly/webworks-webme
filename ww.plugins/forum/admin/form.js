$(function() {
	window.postsForModeration = $('#forum-datatable-requires-moderation')
	.dataTable();
});
$('.approve').click(function() {
	var id = $(this).attr('id').replace('approve_', '');
	$.post(
		'/ww.plugins/forum/admin/approve-post.php',
		{
			'id':id
		},
		remove_row,
		'json'
	);
});
function remove_row(data) {
	if (!data.status) {
		return alert('There was an error in serving your request');
	}
	var postsTable = window.postsForModeration;
	var id = data.id;
	var action = data.action;
	$('<div>This post has been '+action+'</div>').dialog();
	var pos = postsTable.fnGetPosition(($('#post-for-moderation-'+id)[0]));
	postsTable.fnDeleteRow(pos);
}
$('.delete').click(function() {
	var id = $(this).attr('id').replace('delete_', '');
	if (confirm('Are you sure you want to delete this post')) {
		$.post(
			'/ww.plugins/forum/admin/delete-post.php',
			{
				"id":id
			},
			remove_row,
			'json'
		);
	}
});
$('.moderators').change(function() {
	var $this = $(this);
	var checked = $this.attr('checked');
	var forum = $this.attr('name').replace('moderators-', '');
	forum = forum.replace('[]', '');
	var autoApprove = false;
	if (!checked) {
		var allUnchecked = true;
		$('input[name="moderators-'+forum+'[]"]').each(function() {
			if ($(this).attr('checked')) {
				allUnchecked = false;
				return false;
			}
		});
		if (allUnchecked) {
			var confirmText = 'You have removed all moderator groups for this '
				+'forum\nDo you want to auto approve all posts';
			autoApprove = confirm(confirmText);
		}
	}
	var group = $this.val();
	$.post(
		'/ww.plugins/forum/admin/set-moderators.php',
			{
				"action": checked,
				"forum":forum,
				"group":group,
				"autoApprove":autoApprove
			},
			show_message,
			'json'
	);
});
function show_message(data) {
	alert('The moderater groups for this forum have been updated');
	if (data.posts) {
		var posts = data.posts;
		var table = window.postsForModeration;
		for (var i=0; i<posts.length; ++i) {
			var row = $('#posts-for-moderation-'+posts[i]);
			var pos = table.fnGetPosition(row[0]);
			table.fnDeleteRow(pos);
		}
	}
}
$('.add-group').click(function() {
	var $this = $(this);
	var id = $this.attr('id').replace('add-group-link-for-forum-', '');
	var html='<div>'
	html+= '<input class="new-group" id="new-moderator-group-for-forum-'+id
		+' />';
	html+= '</div>';
	$(html).insertBefore($this);
	$('.new-group').blur(function() {
		var $this = $(this);
		var forumID = $this.attr('id')
			.replace('new-moderator-group-for-forum-', '');
		var groupName = $this.val();
		$this.remove();
		$.post(
			'/ww.plugins/forum/admin/new-group.php',
			{
				name: groupName,
				forum: forumID
			},
			update_groups,
			'json'
		);
	});
});
function update_groups(data) {
	$('.add-group').each(function() {
		var $this = $(this);
		var forum = $this.attr('id')
			.replace('add-group-link-for-forum-', '');
		var html = '<div>'+data.name;
		html+= '<input type="checkbox" class="moderators" '
			+'name="moderators-'+forum+'[]"';
		if (forum==data.forum) {
			html+= ' checked="checked"';
		}
		html+= ' /></div>';
		$(html).insertBefore($this);
	});
}
$('.delete-forum-link').click(function() {
	if (confirm('Are you sure you want to delete this forum')) {
		var id = $(this).attr('id').replace('delete-forum-', '');
		$.post(
			'/ww.plugins/forum/admin/delete-forum.php',
			{
				'id':id
			},
			update_page,
			'json'
		);
	}
});
function update_page(data) {
	if (!data.status) {
		return alert(data.message);
	}
	$('#forum-'+data.id).remove();
	var postsTable = window.postsForModeration;
	var posts = data.posts;
	for (i=0; i<posts.length; ++i) {
		var row = $('#post-for-moderation-'+posts[i]);
		var pos = postsTable.fnGetPosition(($(row)[0]));
		if (pos!==null) {
			postsTable.fnDeleteRow(pos);
		}
	}
}
