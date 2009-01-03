function getEls(i,p){
	if(!i)i='*';
	var p=p?p:document,els=p.getElementsByTagName(i);
	return els;
}
