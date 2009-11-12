window.form_input_types=['input box','email','textarea','date','checkbox','selectbox','hidden','ccdate'];
function formfieldsAddRow(){
	formfieldElements++;
	$('<li><table width="100%"><tr><td width="30%"><input name="formfieldElementsName['+formfieldElements+']" /></td><td width="30%"><select name="formfieldElementsType['+formfieldElements+']"><option>'+form_input_types.join('</option><option>')+'</option></select></td><td width="10%"><input type="checkbox" name="formfieldElementsIsRequired['+formfieldElements+']" /></td><td></td></tr></table></li>').appendTo($('#form_fields'));
	$('#form_fields').sortable();
	$('#form_fields input,#form_fields select,#form_fields textarea').bind('click.sortable mousedown.sortable',function(ev){
		ev.target.focus();
	});
}
function formfieldsChange(e){
}
function form_export(id){
	if(!id)return alert('cannot export from an empty form database');
	if(!(+$('select[name="page_vars\\[forms_record_in_db\\]"]').val()))return alert('this form doesn\'t record to database');
	var d=$('#export_from').val();
	document.location='/ww.plugins/forms/j/export.php?date='+d+'&id='+id;
}
if(!formfieldElements)var formfieldElements=0;
$(document).ready(function(){
	formfieldsAddRow();
	$('.date').datepicker({dateFormat:'yy-m-d'});
});
