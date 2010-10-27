function products_data_fields_setup(){
	var ta=$('#data_fields');
	var data=ta.val();
	if(data=='')data='[]';
	window.data_fields=eval(data);
	ta.css('display','none');
	$('<div id="data_fields_rows"/>').insertAfter(ta);
	products_data_fields_redraw();
}
function products_data_fields_add_row(rdata,i){
	// { internal name
	var row='<tr><td><input class="product-type-fd-name" id="product_type_fd'+i+'_name" value="'+htmlspecialchars(rdata.n)+'" /></td>';
	// }
	// { displayed name
	if (!rdata.ti) {
		rdata.ti=rdata.n
	}
	row+='<td><input class="product-type-fd-title" id="product_type_fd'+i+'_title" value="'+htmlspecialchars(rdata.ti)+'" /></td>';
	// }
	// { type
	row+='<td><select id="product_type_fd'+i+'_type">';
	var types=['inputbox','textarea','date','checkbox','selectbox'];
	for(var j=0;j<types.length;++j){
		row+='<option value="'+types[j]+'"';
		if(types[j]==rdata.t)row+=' selected="selected"';
		row+='>'+types[j]+'</option>';
	}
	row+='</select></td>';
	// }
	// { searchable
	row+='<td><input id="product_type_fd'+i+'_searchable" type="checkbox"';
	if(rdata.s)row+=' checked="checked"';
	row+=' /></td>';
	// }
	// { required
	row+='<td><input id="product_type_fd'+i+'_required" type="checkbox"';
	if(rdata.r)row+=' checked="checked"';
	row+=' /></td>';
	// }
	// { extra
	if(rdata.t=='selectbox'){
		row+='<td><textarea id="product_type_fd'+i+'_extra" class="small">'+htmlspecialchars(rdata.e)+'</textarea></td>';
	}
	row+='<td>&nbsp;</td>';
	// }
	return row;
}
function products_data_fields_redraw(){
	var wrapper=$('#data_fields_rows');
	wrapper.empty();
	table='<table><tr><th>Internal Name</th><th>Displayed Name</th><th>Type</th><th>Searchable</th><th>Required</th><th>Extra</th></tr>';
	var rows=0;
	$.each(window.data_fields,function(i,rdata){
		table+=products_data_fields_add_row(rdata,rows++);
	});
	table+=products_data_fields_add_row({n:''},rows);
	table=$(table+'</table>');
	table.appendTo(wrapper);
	$('input.product-type-fd-name',table).change(function(){
		products_data_fields_reset_value();
		products_data_fields_redraw();
	});
	$('#data_fields_rows .product-type-fd-name').each(function(){
		products_data_check_field_name(this);
	});
}
function products_data_fields_reset_value(){
	var vals=[];
	for(var i=0;document.getElementById('product_type_fd'+i+'_name');++i){
		if($('#product_type_fd'+i+'_name').val()=='')continue;
		var val={
			'n':$('#product_type_fd'+i+'_name').val(),
			'ti':$('#product_type_fd'+i+'_title').val(),
			't':$('#product_type_fd'+i+'_type').val(),
			's':$('#product_type_fd'+i+'_searchable')[0].checked?1:0,
			'r':$('#product_type_fd'+i+'_required')[0].checked?1:0,
			'e':$('#product_type_fd'+i+'_extra').val()
		};
		vals.push(val);
	}
	$('#data_fields').val(Json.toString(vals));
	window.data_fields=vals;
}
function products_data_check_field_name(el){
	var name=$(el).val();
	var errors=[];
	if(name.replace(/[^a-zA-Z_]/,'')!==name)errors.push('please only use letters a-z and underscores _');
	if(name.toLowerCase()!==name)errors.push('please only use lowercase letters');
	if(errors.length){
		el.title=errors.join(', ');
		el.className="product-type-fd-name error";
	}
	else{
		el.className="product-type-fd-name";
		el.title='';
	}
}
function products_validate_form(){
	if($('#data_fields_rows .product-type-fd-name.error').length){
		alert("one or more field names has an error\nhover your mouse over the field name to get an explanation");
		return false;
	}
	return true;
}
$(function(){
	products_data_fields_setup();
	$(".tabs").tabs();
	$('input[type=submit]').mousedown(products_data_fields_reset_value);
	$('div.has-left-menu>form').submit(products_validate_form);
	$('#data_fields_rows .product-type-fd-name').live('keyup',function(){
		products_data_check_field_name(this);
	});
});
