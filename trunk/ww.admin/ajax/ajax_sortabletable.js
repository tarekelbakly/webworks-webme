function ajax_sortabletable_dragstart(e){
	window.status='menu sort is broken. a new sorting mechanism is being written';
	return false;
}
function ajax_sortabletable_scroller(){
	var name=ajax_sortabletable_details.owner.id.replace(/_table$/,'').replace(/_/g,' ')
	if(ajax_sortabletable_details.scrollSpeed)dbase_redraw(name,parseInt(dbases[name].viewAt+ajax_sortabletable_details.scrollSpeed));
	ajax_sortabletable_details.scrollTimer=setTimeout('ajax_sortabletable_scroller()',100);
}
{ // variables
	var ajax_sortabletable_details={owner:null,el:null};
}
