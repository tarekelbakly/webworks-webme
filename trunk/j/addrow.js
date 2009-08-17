function addRow(t,p,c){
	var r=t.insertRow(p);
	if(c)r.className=c;
	return r;
}
