function getOffset(el,s) {
	var n=parseInt(el['offset'+s]),p=el.offsetParent;
	if(p)n+=getOffset(p,s);
	return n;
}
