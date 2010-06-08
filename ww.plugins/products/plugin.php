<?php
/*
	Webme Products Plugin
	report bugs to Kae (kae@webworks.ie)
*/
// { plugin declaration
$plugin=array(
	'name' => 'Products',
	'hide_from_admin' => true,
	'admin' => array(
		'menu' => array(
			'Products>Products'   => 'products',
			'Products>Categories' => 'categories',
			'Products>Product Types'=> 'types'
		),
		'page_type' => 'products_admin_page_form'
	),
	'description' => 'Product catalogue.',
	'frontend' => array(
		'page_type' => 'products_frontend'
	),
	'triggers' => array(
		'initialisation-completed' => 'products_add_to_cart'
	),
	'version' => '6'
);
// }

function products_admin_page_form($page,$vars){
	$id=$page['id'];
	$c='';
	require dirname(__FILE__).'/admin/page-form.php';
	return $c;
}
function products_frontend($PAGEDATA){
	require_once dirname(__FILE__).'/frontend/show.php';
	return $PAGEDATA->render().products_show($PAGEDATA);
}
function products_add_to_cart($PAGEDATA){
	if(!isset($_REQUEST['products_action']))return;
	$id=(int)$_REQUEST['product_id'];
	require dirname(__FILE__).'/frontend/show.php';
	$product=Product::getInstance($id);
	if(!$product)return;
	online_store_add_to_cart((float)$product->get('price'),1,$product->get('name'),'','products_'.$id,$_SERVER['HTTP_REFERER']);
}
