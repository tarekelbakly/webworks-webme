$('#product-images-wrapper a').bind('click',function(){
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
