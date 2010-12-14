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
			'Products>Types'=> 'types',
			'Products>Relation Types'=> 'relation-types',
			'Products>Export Data' => 'exports'
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
			'PRODUCTS_BUTTON_ADD_MANY_TO_CART' => array(
				'function' => 'Products_getAddManyToCartButton'
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
			'PRODUCTS_LIST_CATEGORIES' => array(
				'function' => 'Products_listCategories'
			),
			'PRODUCTS_LIST_CATEGORY_CONTENTS' => array(
				'function' => 'Products_listCategoryContents'
			),
			'PRODUCTS_PLUS_VAT' => array (
				'function' => 'Products_plusVat'
			),
			'PRODUCTS_RELATED' => array (
				'function' => 'Products_showRelatedProducts'
			),
			'PRODUCTS_REVIEWS' => array (
				'function' => 'products_reviews'
			)
		)
	),
	'triggers' => array(
		'initialisation-completed' => 'products_add_to_cart'
	),
	'version' => '15'
);
// }

function products_admin_page_form($page,$vars){
	$id=$page['id'];
	$c='';
	require_once dirname(__FILE__).'/admin/page-form.php';
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
	if (!isset($_REQUEST['products_action'])) {
		return;
	}
	$id=(int)$_REQUEST['product_id'];
	require_once dirname(__FILE__).'/frontend/show.php';
	$product=Product::getInstance($id);
	if(!$product)return;
	$amount=1;
	if (isset($_REQUEST['products-howmany'])) {
		$amount=(int)$_REQUEST['products-howmany'];
	}
	if (isset($product->vals['online-store'])) {
		$p=$product->vals['online-store'];
		$price=(float)$p['_price'];
		if(isset($p['_sale_price']) && $p['_sale_price']>0)$price=$p['_sale_price'];
	  if(isset($p['_bulk_price']) && $p['_bulk_price']>0 && $p['_bulk_price']<$price && $amount>=$p['_bulk_amount'])$price=$p['_bulk_price'];
		$vat=(isset($p['_vatfree']) && $p['_vatfree']=='1')?false:true;
	}
	else {
		$price=(float)$product->get('price');
		$vat=true;
	}
	// { find "custom" values
	$vals=array();
	$long_desc='';
	$md5='';
	foreach ($_REQUEST as $k=>$v){
		if (strpos($k, 'products_values_')===0) {
			$vals[]=str_replace('products_values_', '', $k)
				.': '.$v;
		}
	}
	if (count($vals)) {
		$long_desc=join("\n", $vals);
		$md5=','.md5($long_desc.'products_'.$id);
	}
	// }
	OnlineStore_addToCart(
		$price,
		$amount,
		$product->get('name'),
		$long_desc,
		'products_'.$id.$md5,
		$_SERVER['HTTP_REFERER'],
		$vat
	);
}
function Products_listCategories($params, &$smarty){
	require_once dirname(__FILE__).'/frontend/show.php';
	return _Products_listCategories($params, $smarty);
}
function Products_listCategoryContents($params, &$smarty){
	require_once dirname(__FILE__).'/frontend/show.php';
	return _Products_listCategoryContents($params, $smarty);
}
