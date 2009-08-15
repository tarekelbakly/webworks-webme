jQuery.event.add(document.body,'contextmenu',function(e){
	e.stopPropagation();
	e.preventDefault();
	clear_contextmenu();
	var menu=document.createElement('contextmenu');
	menu.id='wwContextMenu';
	menu.style.left=e.pageX+'px';
	menu.style.top=e.pageY+'px';
	links=['<a href="javascript:document.location=\'/ww.admin/pages.php?action=edit&id='+pagedata.id+'\';">edit in admin-area</a>'];
	if(!window.fckeditor_is_open)links.push('<a href="javascript:frontend_admin_editPageContent();">edit page</a>');
	else{
		var oEditor = FCKeditorAPI.GetInstance('__webmePageContent_fckeditor') ;
		links.push('<a href="javascript:frontend_admin_save()">save changes and reload page</a>');
		links.push('<a href="javascript:document.location=document.location">close editor without saving</a>');
	}
	menu.innerHTML=links.join('');
	document.body.appendChild(menu);
	jQuery.event.add(document.body,'click',function(){setTimeout(clear_contextmenu,100);});
});
function clear_contextmenu(){
	var menu=document.getElementById('wwContextMenu');
	if(!menu)return;
	document.body.removeChild(menu);
}
function frontend_admin_addFckeditor(res){
	var html=res.html,css=res.css;
	var our_div=document.getElementById('__webmePageContent');
	var x=our_div.offsetWidth,y=our_div.offsetHeight;
	our_div.innerHTML='';
	var textarea = document.createElement('textarea');
	textarea.setAttribute("id", '__webmePageContent_fckeditor');
	textarea.setAttribute("name", '__webmePageContent_fckeditor');
	textarea.style.width=x+'px';
	textarea.style.height=y+'px';
	textarea.appendChild(document.createTextNode(html));
	our_div.appendChild(textarea);
	var oFCKeditor = new FCKeditor('__webmePageContent_fckeditor',x+20,y+100);
	oFCKeditor.name='__webmePageContent_fckeditor';
	oFCKeditor.BasePath	= '/j/'+ww.FCKEDITOR+'/' ;
	oFCKeditor.Config.ToolbarStartExpanded=false;
	oFCKeditor.Config.EditorAreaCSS=css;
	oFCKeditor.ReplaceTextarea();
	window.fckeditor_is_open=true;
}
function frontend_admin_editPageContent(){
	x_frontend_admin_getPageContent(pagedata.id,frontend_admin_addFckeditor);
}
function frontend_admin_save(){
	var oEditor = FCKeditorAPI.GetInstance('__webmePageContent_fckeditor') ;
	var html = oEditor.GetXHTML();
	x_frontend_admin_save(pagedata.id,html,function(){document.location=document.location});
}
