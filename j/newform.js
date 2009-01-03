function newForm(action,method,enctype,target){
	return new Element('form',{
		action:action,
		method:method,
		enctype:enctype,
		target:target
	});
}
