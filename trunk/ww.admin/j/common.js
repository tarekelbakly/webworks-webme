var id='0',pagesContentsChildren=[],pagesContentsParents=[],pagesContentsRowById=[], CMSextra='',oFCKeditor=[],inAdmin=1;
function admin_editPermissions(type,id){
	window.permissions=[type,id];
	loadScript('/ww.admin/j/permissions.php');
}
function fckeditor_addOnComplete(lib,func){
	if(FCKeditor_OnCompleteFunctions_libs.contains(lib))return;
	FCKeditor_OnCompleteFunctions_libs.push(lib);
	FCKeditor_OnCompleteFunctions.push(func);
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
function initFckEditor(width,height){
	if(browser.isSafari)return 0;
	var sBasePath,els,begin,el,i;
	sBasePath='/j/'+FCKEDITOR+'/';
	els=$ES('textarea.fckeditor');
	begin=oFCKeditor.length;
	for(i=begin,n=begin+els.length;i<n;++i){
		el=els[i-begin];
		removeClassName(el,'fckeditor');
		oFCKeditor[i]=new FCKeditor(el.name,el.offsetWidth,el.offsetHeight);
		oFCKeditor[i].name=el.name;
		oFCKeditor[i].BasePath=sBasePath;
		oFCKeditor[i].Config.FullPage=el.hasClass('fullpage');
		oFCKeditor[i].ReplaceTextarea();
	}
	return i-1;
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
	if(classes['ajaxmenu_expandable'])loadScript('/ww.admin/ajax/ajaxmenu_expandable.php');
	if(classes['fckeditor'])initFckEditor();
	if(classes['sc_supplements'])loadScript('/ww.admin/j/sc_supplements.php');
	if(classes['draggable'])loadScript('/ww.admin/ajax/ajax_sortabletable.php');
	if(classes['accordion']){ // accordion
		accordionParams={active:'.current',clearStyle:true,autoHeight:false,header:'.accordion-header',fillSpace:false,navigation:true};
		$j('.accordion').accordion(accordionParams);
		$j('.accordion0').accordion(accordionParams);
	}
	var page=document.location.toString().replace(/.*admin\/(.*)\.php.*/,'$1');
	if(browser.isIE){
		var el=newEl('div','firefoxAd');
		el.innerHTML='<a href="http://www.spreadfirefox.com/?q=affiliates&id=0&t=203"><img border="0" alt="Upgrade to Firefox 1.5!" title="Upgrade to Firefox 1.5!" src="theme/getFirefox.gif"/><span>Download Firefox</span></a>';
		document.body.appendChild(el);
	}
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
function fckeditor_remove(name){
	var i;
	if(window.FCKeditorAPI == undefined)return;
	FCKeditorAPI.__Instances[name]=null;
	textarea=$M(name);
	if(!textarea)return;
	do{
		i=textarea.getPrevious(),f=1;
		if(i&&i.tagName=='IFRAME')i.remove();
		else f=0;
	}while(f);
}
function notice(v){
	new Notice(v);
}
function textarea_convertToFCKeditor(textarea){
	textarea.addClass('fckeditor fullpage');
	initFckEditor();
}
function textarea_getValue(name){
	var v1,v2;
	textarea_sync();
	v1=$F(name);
	v2=$F(name+'_cp');
	if(v2)v1=v2;
	return v1;
}
function textarea_set(textarea,value){
	if($M(textarea+'___Frame')){ // check for FCKeditor
/*		fckeditor_remove(textarea);
		textarea=$M(textarea);
		textarea.value=value;
		textarea_convertToFCKeditor(textarea); */
	}
	else if($M(textarea+'_cp')){ // check for CodePress
		$M(textarea+'_cp').value=value;
		$M(textarea+'_cp').innerHTML=value;
		eval(textarea).setCode(value);
		// updateCodePress();
	}
	else $M(textarea).value=value;
}
function textarea_sync(){
	updateCodePress();
	updateFckEditors();
}
function textarea_toggle(textarea,type){
	if($M(textarea+'___Frame')){ // check for FCKeditor
		fckeditor_remove(textarea);
		textarea=$M(textarea);
	}
	else if($M(textarea+'_cp')){ // check for CodePress
		var o=$M(textarea+'_cp');
		var i=o.getPrevious();
		o.id=textarea;
		textarea=o;
		i.remove();
	}
	else textarea=$M(textarea);
	textarea.style.display='block';
	switch(type){
		case 'html':{
			textarea_convertToFCKeditor(textarea);
			break;
		}
		case 'text':{
			textarea.addClass('codepress html autocomplete-off');
			CodePress.run();
			break;
		}
	}
}
function updateCodePress(){
	var els=$$M('textarea');
	for(var i=0;i<els.length;++i){
		var id=els[i].id.replace(/[^0-9a-zA-Z]/g,'_').replace(/_cp$/,'');
		try{
			var cp=window[id];
			els[i].value=cp.getCode();
		}
		catch(e){
		}
	}
}
function updateFckEditors(){
	var i,o;
	for(i=0;i<oFCKeditor.length;++i){
		o=oFCKeditor[i];
		if(!o)continue;
		var name=o.name;
		if(!$M(name+'___Frame'))return;
		var FCK=FCKeditorAPI.GetInstance(name);
		window.look_at_this=FCK;
		if($M(name) && FCK){
			FCK.UpdateLinkedField();
			FCK.ResetIsDirty();
		}
	}
}
FCKeditor_OnCompleteFunctions=[function(FCK){FCK.ResetIsDirty();}];
FCKeditor_OnCompleteFunctions_libs=[];
FCKeditor_OnComplete=function(FCK){
	for(var i=0;i<window.parent.FCKeditor_OnCompleteFunctions.length;++i){
		window.parent.FCKeditor_OnCompleteFunctions[i](FCK);
	}
};
window.addEvent('load',initialiseAdmin);
