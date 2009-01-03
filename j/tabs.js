function tabs_show(a,b){
	if(!document.getElementById('tabs_menu_link_'+a+'_'+b))return;
	var f=tabs_functions[a][b];
	if(f)f();
	for(var i=0;document.getElementById('tabs_menu_link_'+a+'_'+i);++i){
		var el=document.getElementById('tabs_menu_link_'+a+'_'+i);
		el.className=el.className.toString().replace(/active/,'');
		el.style.top='-2px';
		document.getElementById('tabs_page_'+a+'_'+i).style.display='none';
	}
	var el=document.getElementById('tabs_menu_link_'+a+'_'+b);
	el.blur();
	el.className+=' active';
	el.style.top='-1px';
	document.getElementById('tabs_page_'+a+'_'+b).style.display='block';
}
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
			page.style.border='1px solid #000';
			var e=jQuery('h2',page)[0];
			if(e){
				text=e.innerHTML;
				e.parentNode.removeChild(e);
			}
			else text='Page '+(j+1);
			link=document.createElement('a');
			link.href='javascript:tabs_show('+tabs_instances+','+j+')';
			link.appendChild(document.createTextNode(text));
			link.id='tabs_menu_link_'+tabs_instances+'_'+j;
			link.className='tabs_menu_link'+(j?'':' active');
			link.style.border='1px solid #000';
			link.style.borderBottom=0;
			link.style.padding='2px 5px';
			link.style.background='#fff';
			link.style.position='relative';
			link.style.top='-2px';
			menu.appendChild(link);
			var ontabshow=page.getAttribute('ontabshow');
			if(ontabshow)tabs_functions[tabs_instances][j]=new Function(ontabshow);
			page.id='tabs_page_'+tabs_instances+'_'+j;
			page.style.display='none';
		}
		wrapper.insertBefore(menu,pages[0]);
		tabs_show(tabs_instances,0);
	}
}
var tabs_instances=0,tabs_functions=[];
