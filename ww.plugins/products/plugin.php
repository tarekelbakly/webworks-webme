<?php
/*
	Webme Products Plugin
	report bugs to Kae (kae@webworks.ie)
*/
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
	'version' => '6'
);

function products_admin_page_form($page,$vars){
	$id=$page['id'];
	$c='';
	require dirname(__FILE__).'/admin/page-form.php';
	return $c;
}
function products_frontend($PAGEDATA){
	require dirname(__FILE__).'/frontend/show.php';
	return $PAGEDATA->render().products_show($PAGEDATA);
}
