function ajaxmenu_expandable_init(){
	var els=$('tr.menuItemTop');
	ajaxmenu_expandable_menus[0]=[];
	for(var j=0;j<els.length;++j){
		var id=els[j].id.replace(/ajaxmenu_expandable_row/,'');
		ajaxmenu_expandable_menuitemdetails[id]={parent:0};
		ajaxmenu_expandable_menus[0][j]=[id];
	}
	var el=$E('tr.ajaxmenu_containsCurrentPage');
	if(el){
		var id=el.id.replace(/ajaxmenu_expandable_row/,'');
		ajaxmenu_expandable_open(id);
	}
}
function ajaxmenu_expandable_open(id){
	if(isArray(id)){
		ajaxmenu_expandable_menus[id[0]]=id[1];
		id=id[0];
	}
	if(!ajaxmenu_expandable_menus[id])return x_ajaxmenu_expandable_getChildren(id,ajaxmenu_expandable_currentPage,ajaxmenu_expandable_open);
	if(ajaxmenu_expandable_openmenus[id])ajaxmenu_expandable_close(id);
	var caller=$M('ajaxmenu_expandable_row'+id),toOpen=0,link;
	if(caller){
		var rowStart=parseInt(caller.rowIndex)+1,indent=caller.indent?caller.indent+1:1,table=caller.parentNode.parentNode;
	}
	else{
		var rowStart=0,indent=0,table=$E('table.ajaxmenu_expandable');
	}
	for(i=0;i<ajaxmenu_expandable_menus[id].length;++i){
		r=ajaxmenu_expandable_menus[id][i];
		var row=addRow(table,rowStart+parseInt(i)),cell;
		row.id='ajaxmenu_expandable_row'+r.id;
		$M(row).addEvent('mousedown',ajax_sortabletable_dragstart);
		row.indent=indent;
		ajaxmenu_expandable_menuitemdetails[r.id]={parent:r.parent};
		ajaxmenu_expandable_openmenus[r.id]=0;
		row.className=r.classes+' draggable';
		{ // tree
			cell=row.insertCell(0);
			cell.className="ajaxmenu_menuname";
			for(var j=0;j<indent;++j)cell.appendChild(new Element('span',{'class':'ajaxmenu_expandable_line'}));
			if(/hasChildren/.test(r.classes)){
				var link=newLink('javascript:ajaxmenu_expandable_open('+r.id+')',0,'ajaxmenu_expandable_opener'+r.id);
				link.className='ajaxmenu_expandable_closed';
				var enditem=(i==ajaxmenu_expandable_menus[id].length-1)?'enditem_':'';
				cell.appendChild(link);
			}
			else if(i==ajaxmenu_expandable_menus[id].length-1)cell.appendChild(new Element('span',{'class':'ajaxmenu_expandable_enditem'}));
			else cell.appendChild(new Element('span',{'class':'ajaxmenu_expandable_item'}));
		}
		{ // name and edit link
			var name=r.name;
			if(!name)name='****NO NAME****';
			$('<a href="pages.php?action=edit&id='+r.id+'" class="fck_droppable navlink"><span>&nbsp;</span>'+name+'</a>').appendTo(cell);
		}
		cell=row.insertCell(1);
		cell.appendChild(newLink('#','[n]',0,'newsubpage'));
		cell=row.insertCell(2);
		cell.appendChild(newLink('#','[x]',0,'deletepage'));
		if(/ajaxmenu_currentPage/.test(r.classes))ajaxmenu_expandable_finishedLoading=1;
		if(!ajaxmenu_expandable_finishedLoading&&/ajaxmenu_containsCurrentPage/.test(r.classes))toOpen=r.id;
	}
	{ // change opener to a closer
		var opener=$M('ajaxmenu_expandable_opener'+id);
		if(opener){
			removeChildren(opener);
			opener.className='ajaxmenu_expandable_opened';
			opener.href='javascript:ajaxmenu_expandable_close('+id+');';
		}
	}
	if(toOpen)ajaxmenu_expandable_open(toOpen);
	ajaxmenu_expandable_openmenus[id]=1;
	$('.newtoppage,.newsubpage').click(pages_new);
	$('.deletepage').click(pages_delete);
}
function ajaxmenu_expandable_close(id){
	id=parseInt(id);
	if(id){
		var opener=$M('ajaxmenu_expandable_opener'+id);
		opener.className='ajaxmenu_expandable_closed';
		removeChildren(opener);
		opener.href='javascript:ajaxmenu_expandable_open('+id+');';
		var caller=$M('ajaxmenu_expandable_row'+id);
		var rowStart=caller.rowIndex,table=caller.parentNode.parentNode;
	}
	else{
		var rowStart=-1,table=$E('table.ajaxmenu_expandable');
	}
	for(var i=ajaxmenu_expandable_menus[id].length;i>0;--i){
		var rowId=ajaxmenu_expandable_menus[id][i-1][0];
		var el=$M('ajaxmenu_expandable_row'+rowId);
		if(el&&ajaxmenu_expandable_openmenus[rowId])ajaxmenu_expandable_close(rowId);
		table.deleteRow(rowStart+i);
	}
	ajaxmenu_expandable_openmenus[id]=0;
}
{ // variables
	var ajaxmenu_expandable_menus=[],ajaxmenu_expandable_finishedLoading=0,ajaxmenu_expandable_charsToRemove=3,ajaxmenu_expandable_openmenus=[1],ajaxmenu_expandable_menuitemdetails=[];
}
ajaxmenu_expandable_init();
