function ajax_sortabletable_init(){
	var els=$('tr.draggable');
	els.each(function(el){el.addEvent('mousedown',ajax_sortabletable_dragstart);});
}
function ajax_sortabletable_dragstart(e){
	{ // events
		document.onmousemove=function(){return false;};
		document.body.ondrag=function(){return false;};
		document.addEvent('mouseup',ajax_sortabletable_dragstop);
		document.addEvent('mousemove',ajax_sortabletable_drag);
	}
	{ // variables
		var m=getMouseAt(e);
		var tr=(new Event(e)).target;
		while(tr.tagName!='TR'&&tr)tr=tr.parentNode;
		var table=tr.parentNode.parentNode;
	}
	delEl(ajax_sortabletable_details.el);
	{ // set up drag variable
		ajax_sortabletable_details.left=getOffset(table,'Left');
		ajax_sortabletable_details.top=getOffset(table,'Top');
		ajax_sortabletable_details.width=table.offsetWidth;
		ajax_sortabletable_details.height=table.offsetHeight;
		ajax_sortabletable_details.startPosition=0;
		ajax_sortabletable_details.endPosition=0;
		ajax_sortabletable_details.mouseX=m.x;
		ajax_sortabletable_details.mouseY=m.y;
		ajax_sortabletable_details.trOrigin=tr;
		if(hasClass(table,'dbase')){
			ajax_sortabletable_details.scrollSpeed=0;
			ajax_sortabletable_details.scrollTimer=setTimeout('ajax_sortabletable_scroller()',100);
			ajax_sortabletable_details.originRowNum=parseInt(getEls('td',tr)[0].innerHTML);
		}
		ajax_sortabletable_details.originId=parseInt(tr.id.replace(/ajaxmenu_expandable_row/,''))
		ajax_sortabletable_details.trOver=tr;
		ajax_sortabletable_details.owner=table;
		ajax_sortabletable_details.el=newEl('table','ajax_sortabletable_dragElement','pagesContents');
		ajax_sortabletable_details.separator=newEl('div','ajax_sortabletable_separator');
		ajax_sortabletable_details.separator.appendChild(newText(' '));
		window.kdnd_dragging=1;
		window.kdnd_source_el=$E('.fck_droppable',tr);
		window.kdnd_drag_wrapper=ajax_sortabletable_details.el;
		window.kdnd_dragFinish=ajax_sortabletable_dragstop;
	}
	{ // styles
		addClass(ajax_sortabletable_details.el,'draggable_active');
		ajax_sortabletable_details.el.setStyles({
			'width':table.offsetWidth,
			'position':'absolute',
			'opacity':.8,
			'z-index':3,
			'visibility':'hidden'
		});
		ajax_sortabletable_details.separator.setStyles({
			'left':getOffset(table,'Left'),
			'width':table.offsetWidth-2,
			'position':'absolute',
			'z-index':2,
			'visibility':'hidden',
			'border':'1px solid #000',
			'background':'#ff0',
			'height':0,
			'margin':-1,
			'line-height':0
		});
	}
	{ // create the floating row
		var newTr=ajax_sortabletable_details.el.insertRow(0);
		for(var i=0;i<tr.childNodes.length;i++)if(tr.childNodes[i].cloneNode){
			var td=tr.childNodes[i].cloneNode(true);
			td.id='';
			newTr.appendChild(td);
		}
	}
	document.body.appendChild(ajax_sortabletable_details.el);
	document.body.appendChild(ajax_sortabletable_details.separator);
	ajax_sortabletable_details.el.setStyles({
		'left':m.x+5,
		'top':m.y+5
	});
	{ // return values
		e.returnValue=false;
		return false;
	}
}
function ajax_sortabletable_scroller(){
	var name=ajax_sortabletable_details.owner.id.replace(/_table$/,'').replace(/_/g,' ')
	if(ajax_sortabletable_details.scrollSpeed)dbase_redraw(name,parseInt(dbases[name].viewAt+ajax_sortabletable_details.scrollSpeed));
	ajax_sortabletable_details.scrollTimer=setTimeout('ajax_sortabletable_scroller()',100);
}
function ajax_sortabletable_drag(e){
	{ // variables
		var m=getMouseAt(e);
		var tr=ajax_sortabletable_details;
	}
	if(m.x<tr.mouseX-5||m.x>tr.mouseX+5||m.y<tr.mouseY-5||m.y>tr.mouseY+5){
		tr.el.setStyles({
			'left':m.x+5,
			'top':m.y+5,
			'visibility':'visible'
		});
		if(browser.isFirefox)window.getSelection().removeAllRanges();
		{ // what row are we over
			var r=tr.trOrigin,trs=getEls('tr',r.parentNode);
			var r2=trs.length-1;
			while(r2--&&m.y<getOffset(trs[r2],'Top')+trs[r2].offsetHeight);
			r2++;
			if(hasClass(tr.owner,'dbase')){
				var speed=0,records_per_page=dbases[tr.owner.id.replace(/_table$/,'').replace(/_/g,' ')].records_per_page;
				if(r2==2)speed=-1;
				if(r2<2)speed=-.1;
				if(r2==records_per_page-1)speed=.1;
				if(r2>records_per_page-1)speed=1;
				ajax_sortabletable_details.scrollSpeed=speed;
			}
			var t=getOffset(trs[r2],'Top'),h=trs[r2].offsetHeight;
			ajax_sortabletable_details.trOver=trs[r2];
			if(hasClass(trs[r2],'draggable')){ // does the row have the right parent
				if(/ajaxmenu_expandable_row/.test(trs[r2].id)){
					var id=parseInt(trs[r2].id.replace(/ajaxmenu_expandable_row/,''));
					if(ajaxmenu_expandable_menuitemdetails[id].parent==ajaxmenu_expandable_menuitemdetails[ajax_sortabletable_details.originId].parent)tr.separator.setStyles({
						'top':t+h*((t+h/2)>m.y?0:1),
						'visibility':'visible'
					});
				}
				else tr.separator.setStyles({
					'top':t+h*((t+h/2)>m.y?0:1),
					'visibility':'visible'
				});
			}
			else tr.separator.setStyle('visibility','hidden');
		}
	}
	else{
		tr.el.setStyle('visibility','hidden');
		tr.separator.setStyle('visibility','hidden');
	}
}
function ajax_sortabletable_dragstop(e){
	clearTimeout(ajax_sortabletable_details.scrollTimer);
	{ // variables
		var tr=ajax_sortabletable_details,y=getMouseAt(e).y;
		var b=(tr.owner.id=='ajaxmenu_expandable')?ajaxmenu_expandable_menuitemdetails:0;
		kdnd_dragging=0;
	}
	{ // events
		document.removeEvent('mouseup',ajax_sortabletable_dragstop);
		document.removeEvent('mousemove',ajax_sortabletable_drag);
		document.onmousemove=null;
		document.body.ondrag=null;
	}
	delEl([tr.el,tr.separator]);
	if(hasClass(tr.owner,'dbase')){
		var overRowNum=parseInt(getEls('td',tr.trOver)[0].innerHTML);
		if(hasClass(tr.trOver,'draggable')&&overRowNum!=tr.originRowNum){
			var t=getOffset(tr.trOver,'Top'),h=tr.trOver.offsetHeight,parent=tr.trOver.parentNode,moveTo=overRowNum;
			if((t+h/2)<y)++moveTo;
			var name=tr.owner.id.replace(/_table$/,'').replace(/_/g,' ');
			if(moveTo>dbases[name].rows)moveTo=dbases[name].rows+1;
			x_dbase_moveTo(name,-1,tr.originRowNum,moveTo,dbase_reloadSortOrder);
		}
	}
	else{ // pages
		if(hasClass(tr.trOver,'draggable')&&(tr.trOver!=tr.trOrigin)){
			{ // does the row have the right parent
				var id=parseInt(tr.trOver.id.replace(/ajaxmenu_expandable_row/,''));
				if(b[id].parent==b[tr.originId].parent){
					var t=getOffset(tr.trOver,'Top'),h=tr.trOver.offsetHeight,parent=tr.trOver.parentNode;
					if((t+h/2)>y)parent.insertBefore(tr.trOrigin,tr.trOver);
					else parent.insertBefore(tr.trOrigin,tr.trOver.nextSibling);
					{ // gather the new order and record it
						var trs=$('tr');
						var newOrder=[];
						$.each(trs,function(key,tr){
							if(tr.id){
								var nid=parseInt(tr.id.replace(/ajaxmenu_expandable_row/,''));
								if((b[nid]&&b[nid].parent==b[id].parent))newOrder[newOrder.length]=nid;
							}
						});
						x_ajax_sortabletable_reorder(b[id].parent,newOrder,ajaxmenu_expandable_open);
					}
				}
			}
		}
	}
}
{ // variables
	var ajax_sortabletable_details={owner:null,el:null};
}
ajax_sortabletable_init();
