function users_add_group(){
	$('<input name="new_groups[]" />').appendTo('.groups');
}
$(document).ready(function(){
	$('<a href="javascript:;" style="float:right;text-decoration:none" title="add a new group">[+]</a>')
		.click(users_add_group)
		.prependTo('.groups');
});
