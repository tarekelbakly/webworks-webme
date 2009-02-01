var lang=[];
$j=jQuery;
function __(str){
	if(lang[str])str=lang[str];
	return str;
}
function $F(n,v){
	var o=$M(n),i=0;
	if(!o)return v?v:'';
	if(o.type=='checkbox')return o.checked;
	if(o.type!='radio')return o.value;
	var os=getEls('input',o.parentNode.parentNode.parentNode),a=[];
	for(;i<os.length;++i)if(os[i].type=='radio' && os[i].name==n)a.push(os[i]);
	for(i=0;i<a.length;++i)if(a[i].checked)return a[i].value;
	return '';
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
function addClass(o,c){
	setClass(o,getClassName(o)+' '+c);
}
function addEls(p,c){
	if(!p)return;
	if($type(p)=='string')p=document.getElementById(p);
	if(isArray(c))for(var i=0;i<c.length;++i)addEls(p,c[i]);
	else if(c)p.appendChild($type(c)=='string'||(+c)===c?newText(c):c);
	return p;
}
function addCell(a,b,c,d,e){
	var f=a.insertCell(b);
	f.colSpan=c;
	addEls(f,d);
	return setClass(f,e);
}
function addCells(r,c,a){
	for(var i=0;i<a.length;++i)addCell(r,c+parseInt(i),a[i].length>2?a[i][2]:1,a[i][0],(a[i].length>1?a[i][1]:0));
}
function boxdropTracer(f,t){
	var ef=$M(f),et=$M(t);
	if(!ef||!et)return;
	var wf=ef.offsetWidth,hf=ef.offsetHeight;
	var xf=getOffset(ef,'Left')+(wf/2),yf=getOffset(ef,'Top')+(hf/2),xt=getOffset(et,'Left')+(et.offsetWidth/2),yt=getOffset(et,'Top')+(et.offsetHeight);
	var d=Math.sqrt((xf-xt)*(xf-xt)+(yf-yt)*(yf-yt));
	if(d<5)return;
	var i=boxdropTracers.length;
	boxdropTracers[i]={dx:xt,dy:yt,x:xf,y:yf,width:wf,height:hf,opacity:.8};
	setTimeout('boxdropTracerStep('+i+')',100);
}
function boxdropTracerStep(id){
	var el=$M('boxdropTracer'+id);
	if(!el){
		el=newEl('div','boxdropTracer'+id,'boxdroptracer');
		el.setStyles({
			'border':'1px solid red',
			'background':'#ff0',
			'height':0,
			'width':0,
			'position':'absolute',
			'left':0,
			'top':0
		});
		addEls(document.body,el);
	}
	with(boxdropTracers[id]){
		x=dx+(x-dx)*.9;
		y=dy+(y-dy)*.9;
		width*=.9;
		height*=.9;
		opacity*=.9;
		if(width<1&&height<1){
			delEl(el);
			boxdropTracers[id]=null;
			return;
		}
		el.setStyles({
			'opacity':opacity,
			'width':width,
			'height':height,
			'left':parseInt(x-width/2),
			'top':parseInt(y-height/2)
		
		});
	}
	setTimeout("boxdropTracerStep("+id+")",100);
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
function delEl(o){
	if($type(o)=='array')for(var i=0;i<o.length;++i)delEl(o[i]);
	else{
		if($type(o)=='string')o=document.getElementById(o);
		if(o&&o.parentNode)o.parentNode.removeChild(o);
	}
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
function getEvent(e){
	return e?e:(window.event?window.event:"");
}
function getParentWithClass(e,t,c) {
	while(e!=null){
		if(t!=null&&e.tagName==t&&hasClass(e,c))return e;
		e=e.parentNode;
	}
}
function hasClass(o,c){
	return eval('/(^| )'+c+'( |$)/').test(getClassName($M(o)));
}
function initialise(){
	alert('function initialise() no longer needs to be called from the HTML template. please remove it');
}
function initShowHide(vis,objName){
	if(!objName)objName='';
	var els=$ES('div.showhide'),i;
	for(var i=0;i<els.length;++i){
		var thisvis=vis?1:(hasClass(els[i],'show')?1:0);
		var link=newLink('javascript:showhide('+(++showhideNum)+');',thisvis?'[hide'+objName+']':'[show'+objName+']','showhideLink'+showhideNum,'showhideLink');
		els[i].parentNode.insertBefore(link,els[i]);
		els[i].id='showhideDiv'+showhideNum;
		els[i].style.display=thisvis?'block':'none';
		removeClassName(els[i],'showhide');
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
	var throbber=document.getElementById('throbber');
	if(!throbber){
		var throbber=newEl('div','throbber');
		document.body.appendChild(throbber);
	}
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
		throbber.style.display='none';
	}
	var w=getWindowSize(),s=getWindowScrollAt();
	throbber.style.display='block';
	throbber.style.position='absolute';
	throbber.style.right=0;
	throbber.style.top=0;
	throbber.style.width='16px';
	throbber.style.height='16px';
	throbber.style.background='url(/i/throbber.gif)';
	throbber.style.zIndex=30;
	x.send(post_data);
}
// }
function lightbox_createFrame(){
	if($M('shader'))return $M('shader').imgSize;
	{ // shader
		var shader=new Element('div',{
			'id':'shader',
			'styles':{
				'position':'fixed',
				'left':0,
				'top':0,
				'width':'100%',
				'height':'100%',
				'background':'#000 url(/i/ajax-loader.gif) no-repeat center center',
				'opacity':.7,
				'z-index':1
			},
			'events':{
				'keyup':lightbox_remove
			}
		});
		$(shader).click(lightbox_remove);
		document.body.appendChild(shader);
	}
	{ // sizes
		var s=window.getSize().size;
		var frameSize={x:parseInt(s.x*.65),y:parseInt(s.y*.95)};
		var imgSize={x:frameSize.x-40,y:frameSize.y-40};
		shader.imgSize=imgSize;
	}
	return imgSize;
}
function lightbox_show(src,caption){
	if(!caption)caption='';
	imgSize=lightbox_createFrame();
	if(/kfmget/.test(src))src+=',width='+imgSize.x+',height='+imgSize.y;
	var lightboxdiv=new Element('div',{
		'id':'lightboxdiv',
		'styles':{
			'position':'fixed',
			'left':0,
			'top':0,
			'width':'100%',
			'height':'100%',
			'z-index':2,
			'background':'url('+src+') no-repeat center center'
		},
		'events':{
			'keyup':lightbox_remove
		}
	});
	$(lightboxdiv).click(lightbox_remove);
	var lightboxcaption=new Element('div',{
		'id':'lightboxcaption',
		'styles':{
			'position':'fixed',
			'left':0,
			'bottom':0,
			'width':'100%',
			'z-index':3,
			'text-align':'center'
		}
	});
	lightboxcaption.appendText(caption);
	document.body.appendChild(lightboxdiv);
	document.body.appendChild(lightboxcaption);
	return false;
}
function lightbox_remove(){
	$M('shader').remove();
	$M('lightboxdiv').remove();
	$M('lightboxcaption').remove();
}
function loadAjaxMenu(){
	var el=$('.ajaxmenu')[0];
	if(!el)return;
	var id=el.id.replace(/ajaxmenu/,'');
	if(id && id=='am_top')return;
	loadScript('/ajax/menu.php?pageid='+pagedata.id+'&search_options='+($(el).hasClass('products')?1:0));
}
function loadArray(k,v){
	var a=[],i;
	k.each(function(val,key){
		a[val]=c[key];
	});
	return a;
}
function loadDateClass(skipload){
	if(skipload){
		if(!window.inputdate_ids)window.inputdate_ids=[];
		var els=$('.inputdate');
		els.each(function(key,el){
			$(el).removeClass('inputdate');
			if(!el.id)el.id='inputdate'+window.inputdate_ids.length;
			window.inputdate_ids.push(el.id);
			$(el).datepicker({dateFormat:'dd/mm/yy'});
		});
	}
	else loadJS('/j/ui.datepicker.js',0,0,'loadDateClass(1)',1);
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
	else loadJS('/j/jquery.validate.min.js',0,0,'loadFormValidation(1)',1);
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
	document.getElementsByTagName('head')[0].appendChild(el);
	return 1;
}
function loadScript(url){
	if(isLoaded(url))return 0;
	loadedScripts.push(url);
	if(kaejax_is_loaded&&/\.php/.test(url))url+=(/\?/.test(url)?'&':'?')+'kaejax_is_loaded';
	var el=newScript(url);
	document.getElementsByTagName('head')[0].appendChild(el);
	return 1;
}
function newScript(url){
	var el;
	if(document.ie)el=document.createElement('<script type="text/javascript" src="'+url+'"></script>');
	else{
		el=newEl('script');
		X(el,{type:"text/javascript",src:url});
	}
	return el;
}
function loadUrl(url){
	document.location=url;
}
function formShow(containedForm,title,actions){
	var form=newEl('div');
	form.className='modal_centre';
	form.appendChild(containedForm);
	var body=document.body,shader=newEl('div','shader'),scrollAt=browser.isIE?getWindowScrollAt():{x:0,y:0},a=getWindowSize(),wx=0,wy=0,pos=browser.isIE?'absolute':'fixed',i;
	{ // shader
		shader.setStyles({
			'background':'#fff',
			'opacity':.5,
			'position':pos,
			'top':scrollAt.y,
			'left':scrollAt.x,
			'z-index':2,
			'width':a.x,
			'height':a.y
		});
		addEls(body,shader);
	}
	{ // wrapper
		var wrapper=newEl('div','formWrapper');
		wrapper.setStyle('visibility','hidden');
		var h2=newEl('h2',0,0,title);
		h2.className='modal_header';
		h2.setStyle('float','left');
		form.setStyles({
			'position':'relative',
			'margin':0,
			'text-align':'left',
			'clear':'left'
		});
		addEls(wrapper,[h2,form]);
		{ // link row
			var row=newEl('div'),buttonStyle={
				'float':'right',
				'border':'1px solid',
				'border-color':'#ccc #666 #666 #ccc',
				'display':'block',
				'background':'#ddd',
				'color':'#000',
				'text-decoration':'none',
				'margin':2
			};
			row.className='modal_links';
			var link=newLink('javascript:formHide()','Close',0,'button');
			link.setStyles(buttonStyle);
			addEls(row,link);
			if(actions&&actions.length)for(var i=0;i<actions.length;++i){
				var v=actions[i];
				if(v[1].toString()===v[1])link=newLink('javascript:'+v[1]+'()',v[0],0,'button');
				else{
					link=newLink('#',v[0],0,'button');
					$(link).click(function(){
						v[1][0][v[1][1]]();
						return false;
					});
				}
				link.setStyles(buttonStyle);
				addEls(row,link);
			}
			addEls(wrapper,row);
		}
		row.setStyles({
			'background':'#eee',
			'border-top':'1px solid #ddd',
			'text-align':'right',
			'padding':2,
			'z-index':3
		})
		addEls(body,wrapper);
		form.setStyle('width',containedForm.offsetWidth+'px');
		wrapper.setStyles({
			'position':'absolute',
			'width':form.offsetWidth
		});
		var w=wrapper.offsetWidth;
		if(w<200||w>a.x*.9){
			w=w<200?200:parseInt(a.x*.9);
			wrapper.setStyle('width',w);
		}
		var h=browser.isIE?wrapper.offsetHeight:h2.offsetHeight+form.offsetHeight+row.offsetHeight,q=browser.isIE?1:0,r=browser.isIE?0:4;
		if(parseFloat(h)>parseFloat(a.y*.9)){
			h=parseInt(a.y*.8);
			var h3=h-row.offsetHeight-h2.offsetHeight-q;
			form.setStyles({
				'margin':'0 auto',
				'overflow':'auto',
				'height':h3,
				'max-height':h3
			});
		}
		else{
			var h3=h-row.offsetHeight-h2.offsetHeight-q;
			form.setStyles({
				'overflow':'auto',
				'width':'100%',
				'max-height':h3
			});
		}
		wrapper.setStyles({
			'position':pos,
			'left':scrollAt.x+a.x/2-w/2,
			'top':scrollAt.y+a.y/2-h/2,
			'background':'#fff',
			'z-index':3,
			'border':'1px solid #000',
			'visibility':'visible'
		});
	}
	if(inAdmin)initFckEditor();
}
// { multiselects
function ms_convert(){
	do{
		var found=0,a=getEls('select');
		for(var b=0;b<a.length,!found;++b){
			var ms=a[b];
			if(ms==null)break;
			var name=ms.name.replace(/\[\]$/,'');
			if(ms&&ms.multiple){
				{ // common variables
					ms_select_defaults[name]=[];
					var found=1,disabled=ms.disabled?1:0,msw=ms.offsetWidth,msh=ms.offsetHeight;
					if(msw<120)msw=120;
					if(msh<60)msh=60;
				}
				{ // set up wrapper
					var wrapper=newEl('div'),k={width:msw+'px',height:msh+'px',position:'relative',border:'2px solid #000',borderColor:'#333 #ccc #ccc #333',font:'10px sans-serif'};
					if(disabled)k.background='#ddd';
					X(wrapper.style,k);
				}
				if(ms_show_toplinks){ /* reset, all, none */
					var c="alert('selection disabled')",d="ms_selectall('"+name+"','",e="javascript:";
					var f=disabled?{a:c,b:c,c:c}:{a:d+"checked');",b:d+"');",c:d+"reset');"};
					addEls(wrapper,[newLink(e+f.a,'all'),', ',newLink(e+f.b,'none'),', ',newLink(e+f.c,'reset')]);
				}
				{ // setup ms
					var newms=newEl('div'),g=browser.isIE?{w:msw-4,h:19}:{w:msw,h:15},h=ms_show_toplinks?{t:'15px',h:msh-g.h}:{t:0,h:msh};
					X(newms.style,{position:'absolute',top:h.t,left:0,overflow:'auto',width:g.w+'px',height:h.h+'px'});
				}
				c=ms.getElementsByTagName('option');
				for(d=0;d<c.length;d++){
					var label=newEl('label'),k={display:'block',border:'1px solid #eee',borderWidth:'1px 0',font:'10px arial',lineHeight:'10px',paddingLeft:'20px'};
					if(browser.isIE){
						checkbox=document.createElement('<input type="checkbox" name="'+ms.name+'" value="'+c[d].innerHTML+'" />');
					}
						else{
						checkbox=newEl('input');
						X(checkbox.style,{marginLeft:'-16px',marginTop:'-2px'});
						X(checkbox,{type:'checkbox',name:ms.name,value:c[d].value});
					}
					if(c[d].selected){
						X(checkbox,{checked:'checked',defaultChecked:true});
						X(k,{background:'blue',color:'#fff'});
					}
					if(c[d].disabled){
						checkbox.disabled='disabled';
						X(k,{background:'#fff',color:'#666'});
					}
					X(label.style,k);
					checkbox.onchange=checkbox.onclick=new Function('ms_updateBackground(this)');
					ms_select_defaults[name][d]=c[d].selected?'checked':'';
					if(disabled)checkbox.disabled="disabled";
					addEls(label,[checkbox,c[d].innerHTML.replace(/\&nbsp;?/g,' ').replace(/\&lt;?/g,'<').replace(/\&gt;?/g,'>')]);
					newms.appendChild(label);
				}
				wrapper.appendChild(newms);
				ms.parentNode.insertBefore(wrapper,ms);
				ms.parentNode.removeChild(ms);
			}
		}
	}while(found);
}
function ms_hasSelected(name){
	var els=document.getElementsByTagName('input');
	for(var i=0;i<els.length;++i)if((els[i].name==name+'[]'||els[i].name==name)&&els[i].checked)return true;
	return false;
}
function ms_selectall(name,val){
	var els=document.getElementsByTagName('input'),found=0;
	for(var i=0;i<els.length;++i)if((els[i].name==name+'[]'||els[i].name==name)&&!els[i].disabled){
		els[i].checked=val=='reset'?ms_select_defaults[name][found++]:val;
		ms_updateBackground(els[i]);
	}
}
function ms_updateBackground(el){
	var p=el.parentNode,c=el.checked?true:false,s=c?{b:'blue',c:'#fff'}:{b:'#fff',c:'#000'};
	X(p.style,{backgroundColor:s.b,color:s.c});
}
// }
function newImg(a,id,title){
	return X(newEl('img'),{'src':a,'id':id,'title':title});
}
function newInput(n,t,v,cl){
	var b;
	if(!t)t='text';
	switch(t){
		case 'checkbox':{
			b=X(newEl('input',n),{type:'checkbox'});
			if(v)b.checked=true;
			b.style.width='auto';
			break;
		}
		case 'date':case 'datetime':{
			{ // break the value into components
				if(v){
					var p=v.split(/[- :]/);
					var year=p[0],month=p[1],day=p[2],hour=p[3],minute=p[4];
				}else{
					var today=new Date();
					var year=today.getFullYear(),month=today.getMonth()+1,day=today.getDate(),hour=today.getHours(),minute=today.getMinutes();
					v=year+'-'+fixed(month,2)+'-'+fixed(day,2)+' '+hour+':'+minute;
				}
			}
			{ // draw the table
				var b=X(newEl('table'),{className:'borderedTable'}),row;
				b.style.width='auto';
				row=addRow(b,0);
				var y=newNumberRange(2000,2030);
				y.unshift('----');
				var d=newNumberRange(1,31,2);
				d.unshift('--');
				addEls(row.insertCell(0),[newSelectbox(n+'_month',newNumberRange(0,12,2),months,month,updateInputVal),newInput(n,'hidden',v)]);
				addCells(row,1,[[newSelectbox(n+'_day',d,0,day,updateInputVal)],[newSelectbox(n+'_year',y,0,year,updateInputVal)]]);
				if(t=='datetime'){ // time
					addCells(row,3,[[newText('-')],[newSelectbox(n+'_hour',newNumberRange(0,23,2),0,hour,updateInputVal)],[newSelectbox(n+'_minute',newNumberRange(0,59,2),0,minute,updateInputVal)]]);
				}
			}
			break;
		}
		case 'radio':{
			b=X(newEl('input',n),{type:'radio',value:v,checked:cl});
			break;
		}
		case 'textarea':{
			b=newEl('textarea',n);
			break;
		}
		default:{
			b=X(newEl('input',n),{type:t});
		}
	}
	if(v){
		if(t=='checkbox')b.checked=b.defaultChecked='checked';
		else if(t!='datetime')b.value=v;
	}
	return setClass(b,cl);
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
	return document.createTextNode(a);
}
function removeChildren(a){
	switch($type(a)){
		case 'array':
			for(var i=0;i<a.length;++i)removeChildren(a[i]);
			return;
		case 'string':
			a=document.getElementById(a);
	}
	if(!a)return;
	a.innerHTML='';
	return a;
}
function removeClassName(el,name){
	return $M(el).removeClass(name);
}
function replaceEl(f,t){
	if(f)f.parentNode.replaceChild(t,f);
}
function removeRowIfEmpty(el){
	if(!el){
		el=$ES('.removeRowIfEmpty');
		el.each(removeRowIfEmpty);
		return;
	}
	if(el.innerHTML!='')return;
	while(el&&el.tagName!='TR')el=$M(el.parentNode);
	if(el)el.remove();
}
function setAttribute(o,n,v){
	if(!n||!o)return;
	if(n=='width'||n=='height')v=v+'px';
	if(n=='class')setClass(o,v);
	else if(n=='style'&&browser.isIE)$M(o).setStyles(v);
	else if(n=='onchange'&&browser.isIE){
		var t=n.replace(/^on/,''),f=new Function(v);
		o.addEvent(t,f);
	}
	else o.setAttribute(n,v);
}
function setClass(o,c){
	if(o&&(c||getClassName(o)))o.className=c?c:'';
	return o;
}
function setPos(a,b,c){
	$M(a).setStyles({
		'left':b,
		'top':c
	});
}
function showhide(id){
	var el=$M('showhideDiv'+id),link=$M('showhideLink'+id);
	var objName=link.innerHTML.replace(/^\[(show|hide)(.*)\]/,'$2');
	var a=el.style.display=='block'?{d:'none',t:'[show'}:{d:'block',t:'[hide'};
	el.style.display=a.d;
	link.replaceChild(newText(a.t+objName+']'),link.childNodes[0]);
}
function text2html(text,nobr){
	if(!window.xhtmlentities){
		var a,b='';
		window.xhtmlentities=$H({'&':'amp',' ':'nbsp','¡':'iexcl','¢':'cent','£':'pound','¤':'curren','¥':'yen','¦':'brvbar','§':'sect','¨':'uml','©':'copy','ª':'ordf','«':'laquo','¬':'not','­':'shy','®':'reg','¯':'macr','°':'deg','±':'plusmn','²':'sup2','³':'sup3','´':'acute','µ':'micro','¶':'para','·':'middot','¸':'cedil','¹':'sup1','º':'ordm','»':'raquo','¼':'frac14','½':'frac12','¾':'frac34','¿':'iquest','×':'times','÷':'divide','"':'quot','<':'lt','>':'gt','ˆ':'circ','˜':'tilde',' ':'ensp',' ':'emsp',' ':'thinsp','–':'ndash','—':'mdash','‘':'lsquo','’':'rsquo','‚':'sbquo','“':'ldquo','”':'rdquo','„':'bdquo','†':'dagger','‡':'Dagger','‰':'permil','‹':'lsaquo','›':'rsaquo','€':'euro'});
		xhtmlentities.each(function(val,key){
			b+=key;
		});
		window.xhtmlentitiesreg=new RegExp('['+b+']');
	}
	text=unescape(text);
	if(xhtmlentitiesreg.test(text))a=1;
	if(text.indexOf('&')!=-1 && /\&amp;([a-z]*);/.test(text))text=text.replace(/\&amp;([a-z]*);/g,'&$1;');
	if(!nobr)text=text.replace(/\n/g,'<br />');
	if(browser.isSafari)text=text.replace(/\r/g,'');
	return text;
}
function toFixed(a,b){
	a=parseFloat(a);
	return a.toFixed?a.toFixed(b):a;
}
function updateInputVal(e){
	var id=(new Event(e)).target.id.replace(/_[^_]*$/,'');
	var y=$F(id+'_year'),m=$F(id+'_month'),d=$F(id+'_day');
	if(y=='----')y='0000';
	if(d=='--')d='00';
	$M(id).value=y+'-'+m+'-'+d+' '+$F(id+'_hour','00')+':'+$F(id+'_minute','00')+':00';
}
function webme_start(){
//	$("img").lazyload({ threshold : 20, effect : "fadeIn" });
	if(getEls('select').length)ms_convert();
	var p=window.plugins_to_load;
	if(p.adblock)            loadScript('/ajax/ads.php?pageid='+pagedata.id);
	if(p.ajaxmenu)           loadAjaxMenu();
	if(p.dbase)              loadScript('/ajax/table.php?pageid='+pagedata.id);
	if(p.eventcalendar)      loadScript('/ajax/events.php?pageid='+pagedata.id);
	if(p.formvalidation)     loadFormValidation();
	if(p.frontend_admin)     loadScript('/ajax/frontend_admin.php?pageid='+pagedata.id);
	if(p.image_gallery)      loadScript('/ajax/image.gallery.php?pageid='+pagedata.id);
	if(p.tabs)               tabs_init();
	if(p.showhide)           initShowHide();
	if(p.fontsize_controls)  loadScript('/j/fonts.js');
	if(p.imagefader)         loadScript('/j/imagefader.js');
	if(p.inputdate)          loadDateClass();
	if(p.os_basket)          loadScript('/ajax/os_basket.php');
	if(p.os_countries)       loadScript('/j/os_countries.js');
	if(p.os_discount_codes)  loadScript('/j/os_discount_codes.js');
	if(p.os_payment_types)   loadScript('/j/os_payment_types.js');
	if(p.os_payment_vouchers)loadScript('/ajax/os_vouchers.php');
	if(p.os_quickfind)       loadScript('/ajax/os_quickfind.php?pageid='+pagedata.id);
	if(p.removeRowIfEmpty)   removeRowIfEmpty();
	if(p.sc_search)          loadScript('/ajax/sc_search.php?pageid='+pagedata.id);
	if(p.scrollingEvents)    loadScript('/j/scrollingEvents.js');
	if(p.scrollingNews)      loadScript('/j/scroller.js');
	if(p.vkfade)             loadJS('/j/jquery.fade.js',0,0,"$('.fademe').vkfade()");

	// the following items have not yet been optimised at the PHP source
  if(p.eventsAdmin)      loadScript('/ww.admin/ajax/events.admin.php?pageid='+pagedata.id);
	if(p.newsAdmin)        loadScript('/ww.admin/ajax/news.admin.php?pageid='+pagedata.id);

	if(document.getElementById('webmeComments'))loadScript('/ajax/comments.php?pageid='+pagedata.id);
}
function X(d,s){
	return $.extend(d,s);
}
{ // variables
	var browser=new Browser(),loadedScripts=[],kaejax_is_loaded=0,inCheckout=0;
	var showhideDivs=[],showhideNum=0,months=['--','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var boxdropTracers=[],function_urls=[],sc_supplements=[];
	var kaejax_timeouts=[],kaejax_xhrinstances=[],ms_select_defaults=[],ms_show_toplinks=true;
	var FCKEDITOR='fckeditor-2.6.4';
}
function html2dom(str,parent){
	var codeprefixes=['SC_'],lastIndex=str.lastIndexOf('<')+1,obj,substr,indentlevel=charat=0,reg=/[ \/\>].*/;
	if(!lastIndex||str.charAt(str.length-1)!='>')lastIndex=str.length;
	while(charat<lastIndex){
		var addAsInnerHtml=0;
		if(str.charAt(charat)=='<'){ // element
			var end=str.indexOf('>',charat+1),nextChar=str.charAt(charat+1);
			substr=str.substring(charat,end+1);
			var slen=substr.length
			if(nextChar=='!'||nextChar=='?')obj=newEl('!');
			else{
				var tName=substr.substring(1,slen).replace(reg,'');
				obj=newEl(tName);
				if(tName.length+3<slen){
					var i=stage=inquotes=0,name=value='',params=substr.substring(tName.length+2,slen-1),setAttribute=window.setAttribute;
					if(params.charAt(params.length-1)=='/')params=params.substring(0,params.length-1);
					var p1=(' '+params+' ').split(/="/);
					for(var p2=1;p2<p1.length;p2++){
						var name=p1[p2-1].substring(p1[p2-1].lastIndexOf(' ')+1,p1[p2-1].length);
						var value=p1[p2].substring(0,p1[p2].lastIndexOf('" '));
						setAttribute(obj,name,value);
					}
				}
				if(str.charAt(end-1)!='/'){ // not self-closing
					indentlevel=1;
					var subelstart=end+1,tracechar=end,subelend=0;
					while(indentlevel){
						subelend=str.indexOf('<',tracechar+1);
						tracechar=end=str.indexOf('>',subelend+1);
						if(str.charAt(subelend+1)=='/')--indentlevel;
						else if(str.charAt(end-1)!='/')++indentlevel;
					}
					html2dom(str.substring(subelstart,subelend),obj);
				}
			}
			charat=end+1;
		}
		else{ // text
			var end=str.indexOf('<',charat+1)-1,i;
			if(end<1)end=str.length-1;
			var isCode=0,text=str.substring(charat,end+1);
			codeprefixes.each(function(pre){
				if(text.indexOf('%'+pre)!=-1)isCode=1;
			});
			if(isCode){
				text=text.replace(/\n/g,'').replace(/^[^%]*|[^%]*$/g,'');
				var name=text.replace(/^%([^{]*)({.*|)%$/,'$1');
				var values=text.replace(/^%[^{]*{?((.*)}|)%$/,'$2');
				obj=sc_buildWidget(name,values);
			}else{
				var text2=text2html(text,1);
				if(text!=text2||text.indexOf('&')!=-1){
					obj=text2;
					addAsInnerHtml=1;
				}
				else obj=newText(text);
			}
			charat=end+1;
		}
		if(addAsInnerHtml)parent.innerHTML+=obj;
		else addEls(parent,obj);
	}
	return parent;
}
$(document).ready(webme_start);
