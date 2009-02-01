/*
	KFCKDrag - a drag/drop thing for fckeditor
	this plugin allows an external drag/drop item to be dragged into the FCKeditor window.
	when the mouse is released, a function is called which decides what to do based on the dragged object type.
*/
function kfd_init(FCK){
	FCK.kfd_dragstate=false;
	if(!window.kfd_windowEventsInit)return; // weird sh^Htuff going on here...
	window.kfd_windowEventsInit(); // initialise window events
	kfd_elementEventsInit(); // add mouseup drop checker to every element
	if(!FCK.kfd_events_added){
		FCK.Events.AttachEvent('OnPaste',kfd_elementEventsInit);
		FCK.Events.AttachEvent('OnLoad',kfd_init);
		FCK.Events.AttachEvent('OnAfterSetHTML',kfd_init);
		FCK.Events.AttachEvent('OnStatusChange',kfd_init);
	}
	FCK.kfd_events_added=true;
}
function getEvent(e){
	return e?e:(window.event?window.event:"");
}
function getMouseAt(e){
	e=getEvent(e);
	var m=getWindowScrollAt();
	m.x+=e.clientX;
	m.y+=e.clientY;
	return m;
}
function getWindowSize(){
	return {x:window.innerWidth,y:window.innerHeight};
}
function getWindowScrollAt(){
	return {x:window.pageXOffset,y:window.pageYOffset};
}
window.kfd_elementEventsInit=function(){
	var doc=FCK.EditorDocument;
	if(!doc)return;
	var els=doc.getElementsByTagName('*');
	for(var i=0;i<els.length;++i){
		var el=els[i];
		if(el.kfd_initialised)continue;
		el.addEventListener('mouseup',kfd_checkForDrop,false);
		el.kfd_initialised=true;
	}
	return true;
}
function kfd_windowEventsInit(){
	var win=FCK.EditorWindow;
	if(!win)return;
	if(win.kfd_events_added)return;
	win.addEventListener('mouseover',function(){
		if(FCK.kfd_dragstate)return;
		var p=window.parent;
		if(!p.kdnd_dragging)return;
		var w=p.kdnd_drag_wrapper;
		var o=p.kdnd_source_el;
		if(!o)return;
		if(!/fck_droppable/.test(o.className))return; // wrong drag type
		FCK.kfd_dragstate=p.kdnd_dragging;
		if(p.kdnd_dragging){
			FCK.drag_wrapper=p.kdnd_drag_wrapper.cloneNode(true);
			p.kdnd_drag_wrapper.style.display='none';
			FCK.drag_wrapper.id='kfd_dragWrapper';
			FCK.drag_wrapper.ctype=o.type;
			FCK.EditorDocument.body.appendChild(FCK.drag_wrapper);
		}
	},false);
	win.addEventListener('mouseout',function(e){
		var m=getMouseAt(e),ws=getWindowSize();
		if((m.x>=0&&m.x<ws.x&&m.y>=0&&m.y<ws.y))return; // browser bug triggers fake mouseouts sometimes...
		kfd_stopDragging();
		if(window.parent.kdnd_drag_wrapper)window.parent.kdnd_drag_wrapper.style.display='block';
	},false);
	win.addEventListener('mousemove',function(e){
		if(!FCK.kfd_dragstate)return;
		var m=getMouseAt(e);
		FCK.drag_wrapper.style.left=(m.x+16)+'px';
	  FCK.drag_wrapper.style.top=m.y+'px';
	  if(window.parent.kdnd_drag_wrapper)window.parent.kdnd_drag_wrapper.style.display='none';
	},false);
	win.kfd_events_added=true;
}
function kfd_stopDragging(run){
	if(FCK.drag_wrapper){
		var el=FCK.EditorDocument.getElementById('kfd_dragWrapper');
		if(el)el.parentNode.removeChild(el);
		FCK.drag_wrapper=null;
	}
	FCK.kfd_dragstate=false;
}
function kfd_checkForDrop(e){
	if(!e||!FCK.kfd_dragstate)return; // nothing being dragged
	var p=window.parent;
	var wrapper=p.kdnd_source_el;
	var name=wrapper.title;
	var target=e.currentTarget;
	if(target.tagName=='HTML')target=FCK.EditorDocument.body;
	var new_el=document.createElement('a');
	new_el.href='/'+name;
	new_el.innerHTML=name;
	new_el_html='<a href="/'+name.replace(/ /g,'-')+'">'+name+'</a>';
	if(new_el){
		if(target.tagName=='BODY'||target.tagName=='TD'||target.tagName=='TH'){
			target.appendChild(new_el);
			target.appendChild(document.createElement('br'));
		}
		else{
//			var parent=target.parentNode,refChild=target.nextSibling;
//			if(refChild)parent.insertBefore(new_el,refChild);
//			else{
//				parent.appendChild(new_el);
//				parent.appendChild(document.createElement('br'));
//			}
				target.innerHTML+=new_el_html;
		}
		e.stopPropagation();
	}
	FCK.ResetIsDirty();
	if(window.parent.kdnd_dragFinish)window.parent.kdnd_dragFinish(e,true);
	e.stopPropagation();
	kfd_stopDragging();
	kfd_elementEventsInit(FCK.EditorDocument);
}
window.parent.fckeditor_addOnComplete('kfckdrag',kfd_init);
var dhs_component_type_names=window.parent.dhs_component_type_names;
FCKConfig.ProtectedSource.RegexEntries.push(/<div title="dhs".*div>/gi);
