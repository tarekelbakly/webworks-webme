<?php
/*
	Webme Products Plugin
	report bugs to Kae (kae@webworks.ie)
*/

$kfm_do_not_save_session=true;
require_once KFM_BASE_PATH.'/api/api.php';
require_once KFM_BASE_PATH.'/initialise.php';

// { plugin declaration
$plugin=array(
	'name' => 'Products',
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
		'admin-script' => '/ww.plugins/products/j/frontend-admin.js',
		'page_type' => 'products_frontend',
		'template_functions' => array(
			'PRODUCTS_BUTTON_ADD_TO_CART' => array(
				'function' => 'products_get_add_to_cart_button'
			),
			'PRODUCTS_CATEGORIES' => array (
				'function' => 'products_categories'
			),
			'PRODUCTS_DATATABLE' => array (
				'function' => 'products_datatable'
			),
			'PRODUCTS_IMAGE' => array(
				'function' => 'products_image'
			),
			'PRODUCTS_IMAGES' => array(
				'function' => 'products_images'
			),
			'PRODUCTS_LINK' => array (
				'function' => 'products_link'
			),
			'PRODUCTS_REVIEWS' => array (
				'function' => 'products_reviews'
			)
		)
	),
	'triggers' => array(
		'initialisation-completed' => 'products_add_to_cart'
	),
	'version' => '11'
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
	if (isset($_REQUEST['product_id'])) {
		$PAGEDATA->vars['products_what_to_show']=3;
		$PAGEDATA->vars['products_product_to_show']=(int)$_REQUEST['product_id'];
	}
	if (isset($_REQUEST['product_cid'])) {
		$PAGEDATA->vars['products_what_to_show']=2;
		$PAGEDATA->vars['products_category_to_show']=(int)$_REQUEST['product_cid'];
	}
	if(!isset($PAGEDATA->vars['footer']))$PAGEDATA->vars['footer']='';
	return $PAGEDATA->render().products_show($PAGEDATA).$PAGEDATA->vars['footer'];
}
function products_add_to_cart($PAGEDATA){
	if(!isset($_REQUEST['products_action']))return;
	$id=(int)$_REQUEST['product_id'];
	require_once dirname(__FILE__).'/frontend/show.php';
	$product=Product::getInstance($id);
	if(!$product)return;
	OnlineStore_addToCart((float)$product->get('price'),1,$product->get('name'),'','products_'.$id,$_SERVER['HTTP_REFERER']);
}
