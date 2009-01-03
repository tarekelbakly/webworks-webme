function newEl(t,id,cn,els){
	var el=document.createElement(t);
	if(t=='iframe')el.src='/i/blank.gif';
	if(id)X(el,{id:id,name:id});
	if(els){
		if($type(els)=='string')el.innerHTML=els;
		else addEls(el,els);
	}
	if(cn)el.className=cn;
	return el;
}
