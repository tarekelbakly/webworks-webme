function getMouseAt(e){
	e=getEvent(e);
	var m=getWindowScrollAt();
	m.x+=e.clientX;
	m.y+=e.clientY;
	return m;
}
