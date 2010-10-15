<?php

/**
  * Gets the new json for the type
  *
  * @category   ProductsPlugin
  * @package    WebworksWebme
  * @subpackage ProductsPlugin
  * @author     Belinda Hamilton <bhamilton@webworks.ie>
  * @license    GPL Version 2.0
  * @link       www.webworks.ie
**/

require_once $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
$typeID = $_REQUEST['type'];
$productID = $_REQUEST['product'];
if (!is_admin()) {
	die('You do not have permission to do this');
}
if (!is_numeric($typeID)||!is_numeric($productID)) {
	exit('Invalid arguments');
}
if (!dbOne('select id from products_types where id = '.$typeID, 'id')) {
	echo '{"status":0, "message":"Could not find this type"}';
}
else {
	$typeFields 
		= dbOne(
			'select data_fields from products_types where id = '.$typeID,
			'data_fields'
		);
	if ($productID != 0) {
		$product 
			= dbRow(
				'select data_fields, product_type_id 
				from products where id = '.$productID
			);
		$productFields = $product['data_fields'];
		$oldType 
			= dbOne(
				'select data_fields 
				from products_types 
				where id = '.$product['product_type_id'],
				'data_fields'
			);
	}
	echo '{"type":'.$typeFields;
	if (isset($productFields)) {
		echo '"product": '.$productFields;
	}
	if (isset($oldType)) {
		echo '"oldType": '.$oldType;
	}
	echo '}';
}
