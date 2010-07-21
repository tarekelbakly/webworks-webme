var id='0',pagesContentsChildren=[],pagesContentsParents=[],pagesContentsRowById=[], CMSextra='',inAdmin=1;
function admin_editPermissions(type,id){
	window.permissions=[type,id];
	loadScript('/ww.admin/j/permissions.php');
}
function imageCheckName(newName,id,doalert){
	for(i=0;i<imageName.length;i++){
		if(imageNames[i][1]==newName&&imageNames[i][0]!=id){
			if(doalert)alert('The name "'+newName+'" is already in use.\nPlease choose a different name.');
			return false;
		}
	}
	return true;
}
function initialiseAdmin(){
	var classes=[];
	els=getEls('*');
	for(i=0;els[i];i++){
		if(els[i].className!=''){
			cn=els[i].className;
			if(cn.indexOf(' ')>-1){
				cn=cn.split(" ");
				for(var j=0;j<cn.length;++j)classes[cn[j]]=1;
			}else{
				classes[cn]=1;
			}
		}
	}
	if(classes['addressbook'])loadScript('/ww.admin/j/addressbook.php');
	if(classes['adminmenu'])loadScript('/ww.admin/ajax/menu.php');
	if(classes['sc_supplements'])loadScript('/ww.admin/j/sc_supplements.php');
	if(classes['draggable'])loadScript('/ww.admin/ajax/ajax_sortabletable.php');
	if(classes['accordion']){ // accordion
		accordionParams={active:'.current',clearStyle:true,autoHeight:false,header:'.accordion-header',fillSpace:false,navigation:true};
		$('.accordion').accordion(accordionParams);
		$('.accordion0').accordion(accordionParams);
	}
	var page=document.location.toString().replace(/.*admin\/(.*)\.php.*/,'$1');
}
function setSelection(el,val){
	if(el.setSelectionRange){
		el.value=el.value.substring(0,el.selectionStart)+val+el.value.substring(el.selectionEnd,el.value.length);
		el.value=el.value.toString().replace(/__(.*)__/,"$1");
		el.value=el.value.toString().replace(/\*\*(.*)\*\*/,"$1");
		el.value=el.value.toString().replace(/\/\/(.*)\/\//,"$1");
		el.value=el.value.toString().replace(/_ _/,' ');
		el.value=el.value.toString().replace(/\* \*/,' ');
		el.value=el.value.toString().replace(/\/ \//,' ');
	}else{
		document.selection.createRange().text=val;
	}
	el.focus();
}
function setSize(el,x,y){
	if(x)el.style.width=parseInt(x)+(/\%/.test(x)?'%':'px');
	if(y)el.style.height=parseInt(y)+(/\%/.test(y)?'%':'px');
}
function windowOpen(l,n,w,h){
	window.open(l,n,"width="+w+",height="+h+",left="+(screen.availWidth/2-w/2)+",top="+(screen.availHeight/2-h/2));
}
function notice(v){
	new Notice(v);
}
window.addEvent('load',initialiseAdmin);
