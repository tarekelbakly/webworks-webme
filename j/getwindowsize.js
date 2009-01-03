function getWindowSize(){
	return {x:window.innerWidth,y:window.innerHeight};
}
window.getSize=function(){
	return {size:getWindowSize()};
}
