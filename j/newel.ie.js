function newEl(t,id,cn,els){
	if(t=='iframe')return $M(newEl('<iframe name="'+id+'" src="/"></iframe>'));
	var el=$M(document.createElement(t));
	if(id)X(el,{id:id,name:id});
	if(els)addEls(el,els);
	if(t=='table')el.cellSpacing='0';
	if(cn)el.className=cn;
	return el;
}
