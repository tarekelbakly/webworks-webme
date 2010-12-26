$j=jQuery;
jQuery.fn.outerHTML = function() {
	return $('<div>').append( this.eq(0).clone() ).html();
};
function $type(obj){
	if (obj==undefined) return false;
	if (obj.htmlElement) return 'element';
	var type = typeof obj;
	if (type == 'object' && obj.nodeName){
		switch(obj.nodeType){
			case 1: return 'element';
			case 3: return (/\S/).test(obj.nodeValue) ? 'textnode' : 'whitespace';
		}
	}
	if (type == 'object' || type == 'function'){
		switch(obj.constructor){
			case Array: return 'array';
			case RegExp: return 'regexp';
		}
		if (typeof obj.length == 'number'){
			if (obj.item) return 'collection';
			if (obj.callee) return 'arguments';
		}
	}
	return type;
};
function addEls(p,c){
	if(!p)return;
	if($type(p)=='string')p=document.getElementById(p);
	if($.isArray(c))for(var i=0;i<c.length;++i)addEls(p,c[i]);
	else if(c)p.appendChild($type(c)=='string'||(+c)===c?newText(c):c);
	return p;
}
function Browser(){
	var ua=navigator.userAgent;
	this.isFirefox=ua.indexOf('Firefox')>=0;
	this.isOpera=ua.indexOf('Opera')>=0;
	this.isIE=ua.indexOf('MSIE')>=0&&!this.isOpera;
	this.isSafari=ua.indexOf('Safari')>=0;
	this.isKonqueror=ua.indexOf('KHTML')>=0&&!this.isSafari;
	this.versionMinor=parseFloat(navigator.appVersion);
	if(this.isIE)this.versionMinor=parseFloat(ua.substring(ua.indexOf('MSIE')+5));
	this.versionMajor=parseInt(this.versionMinor);
}
function date_m2h(d,type){
	if(d=='' || d=='0000-00-00')return '-';
	if(!type)type='date';
	date=d.replace(/([0-9]+)-([0-9]+)-([0-9]+).*/,'$3-$2-$1',d).replace(/-0/g,'-');
	var m=months[date.replace(/.*-([0-9]+)-.*/,'$1')];
	date=date.replace(/-[0-9]+-/,'-'+m+'-');
	if(type=='date')return date;
	var time=d.replace(/.* /,'');
	if(type=='time')return time;
	return time+', '+date;
}
function htmlspecialchars(str) {
	var div=document.createElement('div');
	var text=document.createTextNode(str);
	div.appendChild(text);
	return div.innerHTML;
}
// { kaejax
function kaejax_create_functions(url,f){
	kaejax_is_loaded=1;
	for(var i=0;i<f.length;++i){
		eval('window.x_'+f[i]+'=function(){kaejax_do_call("'+f[i]+'",arguments)}');
		function_urls[f[i]]=url;
	}
}
function kaejax_do_call(func_name,args){
	var uri=function_urls[func_name];
	if(!window.kaejax_timeouts[uri]){
		window.kaejax_timeouts[uri]={t:setTimeout('kaejax_sendRequests("'+uri+'")',1),c:[],callbacks:[]};
	}
	var l=window.kaejax_timeouts[uri].c.length,v2=[];
	for(var i=0;i<args.length-1;++i){
		v2[v2.length]=args[i];
	}
	window.kaejax_timeouts[uri].c[l]={f:func_name,v:v2};
	window.kaejax_timeouts[uri].callbacks[l]=args[args.length-1];
}
function kaejax_sendRequests(uri){
	var t=window.kaejax_timeouts[uri],callbacks=window.kaejax_timeouts[uri].callbacks;
	t.callbacks=null;
	window.kaejax_timeouts[uri]=null;
	var x=new XMLHttpRequest(),post_data="kaejax="+escape(Json.toString(t)).replace(/%([89A-F][A-Z0-9])/g,'%u00$1').replace('+','%2B');
	x.open('POST',uri,true);
	x.setRequestHeader("Method","POST "+uri+" HTTP/1.1");
	x.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	x.onreadystatechange=function(){
		if(x.readyState!=4)return;
		var v=eval('('+unescape(x.responseText.replace(/%/g,'%25'))+')');
		var f,p;
		for(var i=0;i<t.c.length;++i){
			var f=callbacks[i],p=[];
			p=[];
			if($type(f)=='array'){
				p=f;
				f=f[0];
			}
			if(f)f(v[i],p);
		}
	}
	x.send(post_data);
}
// }
function loadScript(url){
	if($.inArray(url,loadedScripts)>-1)return 0;
	loadedScripts.push(url);
	if(kaejax_is_loaded&&/\.php/.test(url))url+=(/\?/.test(url)?'&':'?')+'kaejax_is_loaded';
	$.getScript(url);
	return 1;
}
function newEl(t,id,cn,els){
	var el=document.createElement(t);
	if(id)$.extend(el,{id:id,name:id});
	if(els){
		if($type(els)=='string')el.innerHTML=els;
		else addEls(el,els);
	}
	if(cn)el.className=cn;
	return el;
}
function newText(a){
	return document.createTextNode(a);
}
window.ww={
	CKEDITOR:'ckeditor'
};
// { variables
var browser=new Browser(),loadedScripts=[],kaejax_is_loaded=0,months=['--','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
var function_urls=[];
var kaejax_timeouts=[],ms_select_defaults=[],ms_show_toplinks=true;
// }
var Json = {
	toString: function(arr) {
		return $.toJSON(arr);
	}
};
$(function(){
	var el=$('.ajaxmenu')[0];
	if(!el)return;
	var id=el.id.replace(/ajaxmenu/,'');
	if(id && id=='am_top')return;
	loadScript('/j/menu.php?pageid='+pagedata.id);
});
