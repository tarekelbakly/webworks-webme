function tabs_init(){
	var a=jQuery('div.tabs');
	if(!a||!a.length)return;
	for(var i=a.length-1;i>-1;--i){
		tabs_instances++;
		tabs_functions[tabs_instances]=[];
		var wrapper=a[i];
		wrapper.className='tabs_wrapper';
		var pages=jQuery('div.tabPage',wrapper),menu;
		menu=document.createElement('div');
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
	var url=document.location.toString();
	var tabname=unescape(url.replace(/.*#.*tab=([^&]*)(&|$).*/,"$1"));
	tabs_open_by_name(tabname);
}
function tabs_open_by_name(name){
	var v=tabs_names[name];
	if(!v)return;
	tabs_show(v[0],v[1]);
}
function tabs_show(a,b){
	if(!document.getElementById('tabs_menu_link_'+a+'_'+b))return;
	var f=tabs_functions[a][b];
	if(f)f();
	for(var i=0;document.getElementById('tabs_menu_link_'+a+'_'+i);++i){
		var el=document.getElementById('tabs_menu_link_'+a+'_'+i);
		el.className=el.className.toString().replace(/active/,'');
		document.getElementById('tabs_page_'+a+'_'+i).style.display='none';
	}
	var el=document.getElementById('tabs_menu_link_'+a+'_'+b);
	el.blur();
	el.className+=' active';
	document.getElementById('tabs_page_'+a+'_'+b).style.display='block';
}
var tabs_instances=0,tabs_functions=[],tabs_names=[];
