function admin_edit_page(){
	$('<div id="admin-overlay"></div>').appendTo(document.body);
	$('<div id="admin-editor"><a href="javascript:admin_edit_page_close()">[x]</a><div id="admin-iframe-holder"><iframe src="/ww.admin/pages.php?frontend-admin=1&action=edit&id='+pagedata.id+'"></iframe></div></div>').appendTo(document.body);
}
function admin_edit_page_close(){
	$('#admin-overlay,#admin-editor').remove();
}
function admin_menubar_toggle(){
	if(window.admin_menubar_closed){
		$('#admin-menubar').css('display','block');
		window.admin_menubar_closed=false;
		$('#admin-menubar-hider').css('background-position','center right');
	}
	else{
		$('#admin-menubar').css('display','none');
		window.admin_menubar_closed=true;
		$('#admin-menubar-hider').css('background-position','center left');
	}
	$('#admin-menubar-hider')[0].blur();
}
$(function(){
	$('<div id="admin-menubar"><a href="javascript:admin_edit_page();">edit page content</a></div><a href="javascript:admin_menubar_toggle()" id="admin-menubar-hider"></a>').appendTo(document.body);
})
