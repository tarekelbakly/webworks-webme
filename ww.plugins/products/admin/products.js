function products_form_validate(){
	var errors=[];
	var req=$('#products-form .required');
	req.each(function(){
		if(!this.value)errors.push('The '+this.name+' field must be filled in.');
	});
	if(!errors.length)return true;
	alert(errors.join("\n"));
	return false;
}
function change_href (id) {
	var href = $('#delete_link_'+id)
		.attr('href');
	var boxIsChecked = $('#delete_checkbox_'+id).attr('checked');
	if (boxIsChecked) {
			href += '&delete-images=1';
			$('#delete_link_'+id)
				.attr ('href', href);
	}
	else {
		href = href.replace('&delete-images=1', '');
		$('#delete_link_'+id)
			.attr ('href', href);
	}
}
function toggle_remove_associated_files() {
	$('#new_line').remove();
	var addString = '<div id="remove_wrapper">';
	addString += 'Remove associated files? ';
	addString += '<input type="checkbox" id="remove_associated_files"';
	addString += ' name="remove_associated_files" /></div>';
	var newLineString = '<div id="new_line"></div>';
	switch ($('#clear_database').attr('checked')) {
		case true: // {
			$(addString).insertAfter('#clear_database');
			$('#remove_associated_files').attr('checked', true); 
		break; // }
		case false : // {
			$('#remove_wrapper').remove();
			$(newLineString).insertAfter('#clear_database');
		break; // }
	}
}
$(function(){
	$('#product-images-wrapper a.mark-as-default').bind('click',function(){
		var $this=$(this);
		var id=$this[0].id.replace('products-dfbtn-','');
		$.get('/ww.plugins/products/admin/set-default-image.php?product_id='+product_id+'&id='+id,function(ret){
			$('div.default').removeClass('default');
			$this.closest('div').addClass('default');
		});
	});
	$('#product-images-wrapper a.delete').bind('click',function(){
		var $this=$(this);
		var id=$this[0].id.replace('products-dbtn-','');
		if(!$('#products-dchk-'+id+':checked').length){
			alert('you must tick the box before deleting');
			return;
		}
		$.get('/j/kfm/rpc.php?action=delete_file&id='+id,function(ret){
			$this.closest('div').remove();
		});
	});
	$("#tabs").tabs();
	$('#products-form').submit(products_form_validate);
});

