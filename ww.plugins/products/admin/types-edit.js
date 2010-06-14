function products_data_fields_setup(){
	var ta=$('#data_fields');
	var data=ta.val();
	if(data=='')data='[]';
	window.data_fields=eval(data);
	ta.css('display','none');
	$('<div id="data_fields_rows"/>').insertAfter(ta);
	products_data_fields_redraw()
}
function products_data_fields_add_row(rdata,i){
	// { name
	var row='<tr><td><input class="product-type-fd-name" id="product_type_fd'+i+'_name" value="'+htmlspecialchars(rdata.n)+'" /></td>';
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
	row+='<td>&nbsp;</td>';
	// }
	return row;
}
function products_data_fields_redraw(){
	var wrapper=$('#data_fields_rows');
	wrapper.empty();
	table='<table><tr><th>Name</th><th>Type</th><th>Searchable</th><th>Required</th><th>Extra</th></tr>';
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
}
function products_data_fields_reset_value(){
	var vals=[];
	for(var i=0;document.getElementById('product_type_fd'+i+'_name');++i){
		if($('#product_type_fd'+i+'_name').val()=='')continue;
		var val={
			'n':$('#product_type_fd'+i+'_name').val(),
			't':$('#product_type_fd'+i+'_type').val(),
			's':$('#product_type_fd'+i+'_searchable')[0].checked?1:0,
			'r':$('#product_type_fd'+i+'_required')[0].checked?1:0
		};
		vals.push(val);
	}
	$('#data_fields').val(Json.toString(vals));
	window.data_fields=vals;
}
$(products_data_fields_setup);
$(function(){
	$('input[type=submit]').mousedown(products_data_fields_reset_value);
});