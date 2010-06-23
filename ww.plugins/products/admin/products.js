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
