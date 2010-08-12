<?php
/**
  * Exports the data in the products table to a downloadable csv file
  *
  * PHP Version 5
  *
  * @category   WebworksWebmeProductPlugin
  * @package    WebworksWebme
  * @subpackage Products_Plugin
  * @author     Belinda Hamilton <bhamilton@webworks.ie>
  * @license    GPL Version 2
  * @link       www.webworks.ie
 */
$now = dbOne('select now()', 'now()');
$dir = USERBASE.'ww.cache/products/exports';
$filename = 'webworks_webme_products_export_'.$now.'.csv';
if (!is_dir($dir)) {
	mkdir($dir);
}
$file = fopen($dir.'/'.$filename, 'w');
// { Get the headers
$fields = dbAll('describe products');
$row = '';
foreach ($fields as $field) {
	$row.= '"_'.$field['Field'].'", ';
}
$row = substr_replace($row, "\n",  strrpos($row, ', '));
fwrite ($file, $row);
// }
// { Get the data
$results = dbAll('select * from products');
foreach ($results as $product) {
	$row = '';
	foreach ($fields as $field) {
		echo ' ';
		$csvField = str_replace('"', '""', $product[$field['Field']]);
		$row.= '"'.$csvField.'", ';
	}
	$row = substr_replace($row, "\n", strrpos($row, ', '));
	fwrite($file, $row);
}
// }
fclose($file);
// { Attach it
$location = '../ww.plugins/products/admin/save-file.php';
header('Location: '.$location.'?dir='.$dir.'&filename='.$filename);
// }
