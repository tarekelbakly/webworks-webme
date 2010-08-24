window.os_statuses=['Unpaid','Paid','Paid and Delivered'];
function os_invoice(id){
	var w=$(window);
	var wh=w.height(),ww=w.width();
	$('<iframe id="externalSite" class="externalSite" src="/ww.plugins/online-store/admin/show-invoice.php?id='+id+'" />').dialog({
		autoOpen: true,
		width: ww-100,
		height: wh-100,
		modal: true,
		resizable: true,
		autoResize: true
	}).width(ww-130).height(wh-130);    
}
function os_form_vals(id){
	var w=$(window);
	var wh=w.height(),ww=w.width();
	$('<iframe id="externalSite" class="externalSite" src="/ww.plugins/online-store/admin/show-details.php?id='+id+'" />').dialog({
		autoOpen: true,
		width: ww-100,
		height: wh-100,
		modal: true,
		resizable: true,
		autoResize: true
	}).width(ww-130).height(wh-130);    
}
function os_status(id,current_status){
	var options=[];
	for(var i=0;i<window.os_statuses.length;++i){
		var html='<option value="'+i+'"';
		if(i==current_status){
			html+=' selected="selected"';
		}
		html+='>'+window.os_statuses[i]+'</option>';
		options.push(html);
	}
	var target=$('#os_status_'+id);
	$('<select id="os_status_select_'+id+'">'+options.join('')+'</select>')
		.change(os_status_change)
		.insertAfter(target);
	target.remove();
}
function os_update_fields(force){
	if(!force && !window.ckeditor_body.checkDirty()){
		return;
	}
	var $wrapper=$('#online-stores-fields').empty();
	var $form=$('<div id="online-stores-tester" style="display:none">'+window.ckeditor_body.getData()+'</div>').appendTo($wrapper);
	for(var i in os_fields){
		if(typeof(os_fields[i])!="object"){
			continue;
		}
		os_fields[i].show=0;
	}
	var $inputs=$form.find('input,select,textarea');
	var c=0,to_show=[];
	for(var i=0;i<$inputs.length;++i){
		if(!os_fields[$inputs[i].name]){
			os_fields[$inputs[i].name]={
				required:0
			}
		}
		os_fields[$inputs[i].name].show=1;
		++c;
		to_show.push($inputs[i].name);
	}
	$wrapper.empty();
	if(!c){
		$wrapper.append('<em>no fields defined. please create a form in the Form tab.</em>');
	}
	else{
		var table='<table id="online_stores_fields_table" style="width:100%"><tr><th>Name</th><th>Required</th></tr>';
		for(var i=0;i<c;++i){
			table+='<tr><td></td><td></td><td></td></tr>';
		}
		$wrapper.append(table+'</table>');
		var $rows=$wrapper.find('tr');
		for(var i=0;i<c;++i){
			var $row=$($rows[i+1]);
			$row.data('os_name',to_show[i]);
			var $cells=$row.find('td');
			$($cells[0]).text(to_show[i]);
			$('<input class="is-required" type="checkbox"'+(os_fields[to_show[i]].required?' checked="checked"':'')+' />').appendTo($cells[1]);
		}
	}
	$('<input id="online_stores_fields_input" type="hidden" name="page_vars[online_stores_fields]" />').val(Json.toString(os_fields)).appendTo($wrapper);
}
function os_update_fields_value(){
	var name=$(this).closest('tr').data('os_name');
	if(this.className=='is-required'){
		os_fields[name].required=this.checked?1:0;
	}
	$('#online_stores_fields_input').val(Json.toString(os_fields));
}
function os_status_change(ev){
	var el=ev.target;
	var id=el.id.replace(/os_status_select_/,''),val=+$(el).val();
	$.get('/ww.plugins/online-store/admin/change-status.php?id='+id+'&status='+val,function(){
		$('#os_status_select_'+id).replaceWith(
			$('<a id="os_status_'+id+'" href="javascript:;">'+window.os_statuses[val]+'</a>')
				.click(function(){
					os_status(id,val);
				})
		);
	});
}
$(function(){
	$('#online-store-status').change(function(ev){
		document.location='/ww.admin/pages/form.php?id='+window.page_menu_currentpage+'&online-store-status='+$(ev.target).val();
	});
	os_update_fields();
	$('.tabs_menu_link').live('mousedown',os_update_fields);
	$('form').bind('submit',os_update_fields);
	$("#online_store_redirect_to").remoteselectoptions({
		url:"/ww.admin/pages/get_parents.php"
	});
});
$('#online_stores_fields_table input').live('click',os_update_fields_value);
