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
		if(i==current_status)html+=' selected="selected"';
		html+='>'+window.os_statuses[i]+'</option>';
		options.push(html);
	}
	var target=$('#os_status_'+id);
	$('<select id="os_status_select_'+id+'">'+options.join('')+'</select>')
		.change(os_status_change)
		.insertAfter(target);
	target.remove();
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
$(document).ready(function(){
	$('#online-store-status').change(function(ev){
		document.location='/ww.admin/pages.php?id='+window.page_menu_currentpage+'&online-store-status='+$(ev.target).val();
	});
});
