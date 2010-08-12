<?php
/**
  * Gets the data for all the products and prompts the user to save it
  *
  * PHP Version 5
  *
  * @category   ProductsPlugin
  * @package    WebworksWebme
  * @subpackage ProdcutsPlugin
  * @author     Belinda Hamilton <bhamilton@webworks.ie>
  * @license    GPL Version 2
  * @link       www.webworks.ie
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
$now = dbOne('select now()', 'now()');
$filename = 'webworks_webme_products_export_'.$now.'.csv';
header('Content-Type: text/csv');
header('Content-Dispositon: attachment; filename="'.$filename.'"');
// { Get the headers
$fields = dbAll('describe products');
$row = '';
foreach ($fields as $field) {
    $row.= '"_'.$field['Field'].'", ';
}
$contents = substr_replace($row, "\n", strrpos($row, ', '));
// } 
// { Get the data
$results = dbAll('select * from products');
foreach ($results as $product) {
	$row = '';
	foreach ($fields as $field) {
		$row.= '"'.str_replace('"', '""', $product[$field['Field']]).'", ';
	}
	$row = substr_replace($row, "\n", strrpos($row, ', '));
	$contents.= $row;
}
echo $contents;
// }
