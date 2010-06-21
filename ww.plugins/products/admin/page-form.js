function products_what_to_show_change(){
	var val=+$('#products_what_to_show').val();
	$('#products_what_to_show_1').css('display',val==1?'table-row':'none');
	$('#products_what_to_show_2').css('display',val==2?'table-row':'none');
	$('#products_what_to_show_3').css('display',val==3?'table-row':'none');
	$('#products_search').css('display',val<3?'table-row':'none');
	$('#products_order_by').css('display',val<3?'table-row':'none');
	$('#products_per_page').css('display',val<3?'table-row':'none');
}
$(function(){
	$('#products_what_to_show').change(products_what_to_show_change);
	products_what_to_show_change();
	$('#products_order_by_select').remoteselectoptions({
		url:'/ww.plugins/products/admin/get-order-fields.php',
		other_GET_params:function(){
			if($('#products_what_to_show').val()=='1'){
				return $('#products_what_to_show_1 select').val();
			}
			return '';
		},
		always_retrieve:true
	});
	$('#products_what_to_show_1 select').change(function(){
		$('#products_order_by_select').trigger('mousedown');
	});
});
