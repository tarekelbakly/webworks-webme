window.form_input_types=['input box','email','textarea','date','checkbox','selectbox','hidden','ccdate'];
function formfieldsAddRow(){
	formfieldElements++;
	$('<li><table width="100%"><tr><td width="30%"><input name="formfieldElementsName['+formfieldElements+']" /></td><td width="30%"><select name="formfieldElementsType['+formfieldElements+']"><option>'+form_input_types.join('</option><option>')+'</option></select></td><td width="10%"><input type="checkbox" name="formfieldElementsIsRequired['+formfieldElements+']" /></td><td></td></tr></table></li>').appendTo($('#form_fields'));
	$('#form_fields').sortable();
	$('#form_fields input').bind('click.sortable mousedown.sortable',function(ev){
		ev.target.focus();
	});
}
function formfieldsChange(e){
}
if(!formfieldElements)var formfieldElements=0;
$(document).ready(formfieldsAddRow);
