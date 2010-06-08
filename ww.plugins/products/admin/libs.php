<?php
function products_categories_get_data($id){
	$ps=dbAll('select product_id from products_categories_products where category_id='.$id);
	$products=array();
	foreach($ps as $p)$products[]=$p['product_id'];
	$data=array(
		'attrs'=>dbRow('select id,name,enabled,parent_id from products_categories where id='.$id),
		'products'=>$products
	);
	return $data;
}
