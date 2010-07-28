var lang=[];
$j=jQuery;
_d=document;
function _a(i){ // shortcut version of _a(id)
	return _d.getElementById(i);
}
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
	if($type(p)=='string')p=_a(p);
	if(isArray(c))for(var i=0;i<c.length;++i)addEls(p,c[i]);
	else if(c)p.appendChild($type(c)=='string'||(+c)===c?newText(c):c);
	return p;
}
function addCell(a,b,c,d,e){
	var f=a.insertCell(b);
	f.colSpan=c;
	addEls(f,d);
	if(e)f.className=e;
	return f;
}
function addCells(r,c,a){
	for(var i=0;i<a.length;++i)addCell(r,c+parseInt(i),a[i].length>2?a[i][2]:1,a[i][0],(a[i].length>1?a[i][1]:0));
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
function event_target(e){ // taken from http://www.quirksmode.org/js/events_properties.html
	var targ;
	if (!e) var e = window.event;
	if (e.target) targ = e.target;
	else if (e.srcElement) targ = e.srcElement;
//	if (targ.nodeType == 3) // defeat Safari bug
//		targ = targ.parentNode;
	return targ;
}
function fixed(v,f){
	v=parseFloat(v);
	var l=Math.floor(f),a=v,c='',r=0;
	v=v.toFixed?v.toFixed((f-l)*10):v;
	while(a>=1){
		++r;
		a/=10;
	}
	for(;l>r;++r)c+='0';
	return c+v;
}
function getClassName(el){
	return el&&el.className?el.className:'';
}
function htmlspecialchars(str) {
	var div=_d.createElement('div');
	var text=_d.createTextNode(str);
	div.appendChild(text);
	return div.innerHTML;
}
function initialise(){
	alert('function initialise() no longer needs to be called from the HTML template. please remove it');
}
function initShowHide(vis,objName){
	if(!objName)objName='';
	var els=$('div.showhide'),i;
	for(var i=0;i<els.length;++i){
		var thisvis=vis?1:($(els[i]).hasClass('show')?1:0);
		var link=newLink('javascript:showhide('+(++showhideNum)+');',thisvis?'[hide'+objName+']':'[show'+objName+']','showhideLink'+showhideNum,'showhideLink');
		els[i].parentNode.insertBefore(link,els[i]);
		els[i].id='showhideDiv'+showhideNum;
		els[i].style.display=thisvis?'block':'none';
		$(els[i]).removeClass('showhide');
	}
	return els.length-1;
}
function isArray(o){
	return o instanceof Array||typeof o=='array';
}
function isLoaded(url){
	for(var i=0;i<loadedScripts.length;++i)if(loadedScripts[i]==url)return 1;
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
function loadAjaxMenu(){
	var el=$('.ajaxmenu')[0];
	if(!el)return;
	var id=el.id.replace(/ajaxmenu/,'');
	if(id && id=='am_top')return;
	loadScript('/ajax/menu.php?pageid='+pagedata.id);
}
function loadArray(k,v){
	var a=[],i;
	k.each(function(val,key){
		a[val]=c[key];
	});
	return a;
}
function loadFormValidation(skipload){
	if(skipload){
		if(!window.formvalidation_ids)window.formvalidation_ids=[];
		var els=$('.formvalidation');
		els.each(function(key,el){
			$(el).removeClass('formvalidation');
			if(!el.id)el.id='formvalidation'+window.formvalidation_ids.length;
			window.formvalidation_ids.push(el.id);
			$(el).validate();
		});
	}
	else loadJS('http://ajax.microsoft.com/ajax/jquery.validate/1.6/jquery.validate.min.js',0,0,'loadFormValidation(1)',1);
}
function loadJS(url,id,lang,onload,runanyway){
	var i=0;
	for(;i<loadedScripts.length;++i){
		if(loadedScripts[i]==url){
			if(onload && runanyway)return eval(onload);
			return 0;
		}
	}
	loadedScripts.push(url);
	var el=newScript(url);
	if(id){
		el.id=id;
	}
	if(lang){
		el.lang=lang;
	}
	if(onload){
		el.onload_triggered=0;
		el.onload=function(){
			if(!this.onload_triggered++){
				eval(onload);
			}
		};
		el.onreadystatechange=function(){
			if(this.readyState=='loaded'||this.readyState=='complete'){
				if(!this.onload_triggered++){
					eval(onload);
				}
			}
		};
	}
	_d.getElementsByTagName('head')[0].appendChild(el);
	return 1;
}
function loadScript(url){
	if(isLoaded(url))return 0;
	loadedScripts.push(url);
	if(kaejax_is_loaded&&/\.php/.test(url))url+=(/\?/.test(url)?'&':'?')+'kaejax_is_loaded';
	var el=newScript(url);
	_d.getElementsByTagName('head')[0].appendChild(el);
	return 1;
}
function loadUrl(url){
	_d.location=url;
}
function newEl(t,id,cn,els){
	var el=_d.createElement(t);
	if(id)X(el,{id:id,name:id});
	if(els){
		if($type(els)=='string')el.innerHTML=els;
		else addEls(el,els);
	}
	if(cn)el.className=cn;
	return el;
}
function newLink(h,t,id,c){
	return X(newEl('a',id,c,t),{href:h});
}
function newNumberRange(from,to,padding){
	var arr=[],i=0;
	for(;i<to-from+1;++i){
		arr[i]=''+(from+i);
		while(arr[i].length<padding)arr[i]='0'+arr[i];
	}
	return arr;
}
function newScript(url){
	var el;
	if(_d.ie)el=_d.createElement('<script type="text/javascript" src="'+url+'"></script>');
	else{
		el=newEl('script');
		X(el,{type:"text/javascript",src:url});
	}
	return el;
}
function newSelectbox(name,keys,vals,s,f){
	var el2=newEl('select',name),el3,s2=0,i;
	if(!s)s=0;
	if(!vals)vals=keys;
	for(var i=0;i<vals.length;++i){
		if(!vals[i])vals[i]='';
		var v1=vals[i].toString();
		var v2=v1.length>20?v1.substr(0,27)+'...':v1;
		el3=X(newEl('option',0,0,v2),{value:keys[i],title:v1});
		if(keys[i]==s)s2=i;
		addEls(el2,el3);
	}
	el2.selectedIndex=s2;
	if(f)el2.onchange=f;
	return el2;
}
function newText(a){
	return _d.createTextNode(a);
}
function replaceEl(f,t){
	if(f)f.parentNode.replaceChild(t,f);
}
function showhide(id){
	var el=_a('showhideDiv'+id),link=_a('showhideLink'+id);
	var objName=link.innerHTML.replace(/^\[(show|hide)(.*)\]/,'$2');
	var a=el.style.display=='block'?{d:'none',t:'[show'}:{d:'block',t:'[hide'};
	el.style.display=a.d;
	link.replaceChild(newText(a.t+objName+']'),link.childNodes[0]);
}
window.ww={
	CKEDITOR:'ckeditor'
};
function X(d,s){
	return $.extend(d,s);
}
// { variables
var browser=new Browser(),loadedScripts=[],kaejax_is_loaded=0,inCheckout=0;
var showhideDivs=[],showhideNum=0,months=['--','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
var function_urls=[];
var kaejax_timeouts=[],kaejax_xhrinstances=[],ms_select_defaults=[],ms_show_toplinks=true;
// }
// { browser-specific
if(window.ie)window.XMLHttpRequest=function(){var l=(ScriptEngineMajorVersion()>=5)?"Msxml2":"Microsoft";return new ActiveXObject(l+".XMLHTTP")};function DOMParser(){};DOMParser.prototype={toString:function(){return"[object DOMParser]"},parseFromString:function(s,c){var x=new ActiveXObject("Microsoft.XMLDOM");x.loadXML(s);return x},parseFromStream:new Function,baseURI:""};function XMLSerializer(){};XMLSerializer.prototype={toString:function(){return"[object XMLSerializer]"},serializeToString:function(r){return r.xml||r.outerHTML},serializeToStream:new Function};
// }
var Json = {
	toString: function(arr) {
		return $.toJSON(arr);
	}
};
function tabs_init(){
	var a=jQuery('div.tabs');
	if(!a||!a.length)return;
	for(var i=a.length-1;i>-1;--i){
		tabs_instances++;
		tabs_functions[tabs_instances]=[];
		var wrapper=a[i];
		wrapper.className='tabs_wrapper';
		var pages=jQuery('div.tabPage',wrapper),menu;
		menu=_d.createElement('div');
		menu.id='tabs_menu_'+tabs_instances;
		menu.className='tabs_menu';
		for(var j=0;j<pages.length;++j){
			var page=pages[j],text;
			page.className='tabs_page';
			var e=jQuery('h2',page)[0];
			if(e){
				text=e.innerHTML;
				e.parentNode.removeChild(e);
			}
			else text='Page '+(j+1);
			tabs_names[text]=[tabs_instances,j];
			link=$('<a href="javascript:tabs_show('+tabs_instances+','+j+')" id="tabs_menu_link_'+tabs_instances+'_'+j+'" class="tabs_menu_link'+(j?'':' active')+'"><span class="r"></span><span class="l"></span><span class="m">'+text+'</span></a>')
				.appendTo(menu);
			var ontabshow=page.getAttribute('ontabshow');
			if(ontabshow)tabs_functions[tabs_instances][j]=new Function(ontabshow);
			page.id='tabs_page_'+tabs_instances+'_'+j;
			page.style.display='none';
		}
		wrapper.insertBefore(menu,pages[0]);
		tabs_show(tabs_instances,0);
	}
	var url=_d.location.toString();
	var tabname=unescape(url.replace(/.*#.*tab=([^&]*)(&|$).*/,"$1"));
	tabs_open_by_name(tabname);
}
function tabs_open_by_name(name){
	var v=tabs_names[name];
	if(!v)return;
	tabs_show(v[0],v[1]);
}
function tabs_show(a,b){
	if(!_a('tabs_menu_link_'+a+'_'+b))return;
	var f=tabs_functions[a][b];
	if(f)f();
	for(var i=0;_a('tabs_menu_link_'+a+'_'+i);++i){
		var el=_a('tabs_menu_link_'+a+'_'+i);
		el.className=el.className.toString().replace(/active/,'');
		_a('tabs_page_'+a+'_'+i).style.display='none';
	}
	var el=_a('tabs_menu_link_'+a+'_'+b);
	el.blur();
	el.className+=' active';
	_a('tabs_page_'+a+'_'+b).style.display='block';
}
var tabs_instances=0,tabs_functions=[],tabs_names=[];
$(function(){
	var p=window.plugins_to_load;
	if(p.ajaxmenu)           loadAjaxMenu();
	if(p.carousel)           loadJS('/j/jcarousellite_1.0.1.js',0,0,"$('.carousel').jCarouselLite({btnNext:'.carousel-next',btnPrev:'.carousel-prev'});");
	if(p.formvalidation)     loadFormValidation();
	if(p.image_gallery)      loadScript('/ajax/image.gallery.php?pageid='+pagedata.id);
	if(p.tabs)               tabs_init();
	if(p.showhide)           initShowHide();
	if(p.fontsize_controls)  loadScript('/j/fonts.js');
	if(p.os_fader)           loadScript('/j/os_fader.js');

	// the following items have not yet been optimised at the PHP source
  if(p.eventsAdmin)      loadScript('/ww.admin/ajax/events.admin.php?pageid='+pagedata.id);
	if(p.newsAdmin)        loadScript('/ww.admin/ajax/news.admin.php?pageid='+pagedata.id);

	if(_a('webmeComments'))loadScript('/ajax/comments.php?pageid='+pagedata.id);
});
