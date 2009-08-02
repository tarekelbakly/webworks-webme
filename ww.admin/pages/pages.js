function pages_delete(){
	if(!confirm('Are you sure you want to delete this?'))return false;
	var p,link=$(this);
	pel=link.closest('tr');
	p=pel[0].id.replace(/[a-z_]*/,'');
	if(p==ajaxmenu_expandable_currentPage || pel.hasClass('ajaxmenu_hasChildren'))document.location='/ww.admin/pages.php?action=delete&id='+p;
	else $.get('/ww.admin/pages/delete.php?id='+p,function(res){
		if(res)$(res).dialog();
		else link.closest('tr').hide(500);
	});
	return false;
}
function pages_new(){
	var p=0,link=$(this);
	if(this.className=='newsubpage')p=link.closest('tr')[0].id.replace(/[a-z_]*/,'');
	$('<form id="newpage_dialog" action="/ww.admin/index.php" method="post"><input type="hidden" name="action" value="Insert Page Details" /><input type="hidden" name="special[1]" value="1" /><input type="hidden" name="newpage_dialog" value="1" /><input type="hidden" name="parent" value="'+p+'" /><table><tr><th>Name</th><td><input name="name" /></td></tr><tr><th>Page Type</th><td><select name="type"><option value="0">normal</option></select></td></tr></table></form>').dialog({
		modal:true,
		buttons:{
			'Create Page': function() {
				document.getElementById('newpage_dialog').submit();
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});
	$('#newpage_dialog select[name=type]').remoteselectoptions({url:'/ww.admin/pages/get_types.php'});
	return false;
}
$(document).ready(function(){
	$('.newtoppage,.newsubpage').click(pages_new);
	$('.deletepage').click(pages_delete);
	$('#pages_form select[name=type]').remoteselectoptions({url:'/ww.admin/pages/get_types.php'});
	$('#pages_form select[name=parent]').remoteselectoptions({
		url:'/ww.admin/pages/get_parents.php',
		other_GET_params:ajaxmenu_expandable_currentPage
	});
});
