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
	$('#pages_form select[name=type]').remoteselectoptions({url:'/ww.admin/pages/get_types.php'});
});
