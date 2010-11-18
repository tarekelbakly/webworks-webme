<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
require 'show.php';

$PAGEDATA=Page::getInstance($_REQUEST['pid']);
$PAGEDATA->initValues();

$columns=explode(
	',',
	preg_replace('/[^a-z0-9\-_,]/','_',strtolower($_REQUEST['sColumns']))
);
$sort_col=$columns[(int)$_REQUEST['iSortCol_0']];
$sort_dir=$_REQUEST['sSortDir_0'];

$search=$_REQUEST['sSearch'];
$search_arr=array();
for ($i=0; $i<count($columns); ++$i) {
	if (!$_REQUEST['sSearch_'.$i]) {
		continue;
	}
	$search_arr[$columns[$i]]=$_REQUEST['sSearch_'.$i];
}

switch($PAGEDATA->vars['products_what_to_show']) {
	case '1': // { by type
		$id=(int)$PAGEDATA->vars['products_type_to_show'];
		$products=Products::getByType(
			$id, $search, $search_arr, $sort_col, $sort_dir
		);
	break; // }
	case '2': // { by category
		$id=(int)$PAGEDATA->vars['products_category_to_show'];
		$products=Products::getByCategory(
			$id, $search, $search_arr, $sort_col, $sort_dir
		);
	break; //}
	default:
		exit;
}

$total_records=count($products->product_ids);
$returned_products=array();
$i=0;

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="nfgws-export.csv"');

function sputcsv($row, $delimiter = ',', $enclosure = '"', $eol = "\n") {
    static $fp = false;
    if ($fp === false) {
        $fp = fopen('php://temp', 'r+'); // see http://php.net/manual/en/wrappers.php.php - yes there are 2 '.php's on the end.
        // NB: anything you read/write to/from 'php://temp' is specific to this filehandle
    }
    else {
        rewind($fp);
    }
     if (fputcsv($fp, $row, $delimiter, $enclosure) === false) {
         return false;
     }
    rewind($fp);
    $csv = fgets($fp);
     if ($eol != PHP_EOL) {
         $csv = substr($csv, 0, (0 - strlen(PHP_EOL))) . $eol;
     }
    return $csv;
}

echo '"'.join('","', $columns)."\"\n";

for (; $i<$total_records; ++$i) {
	$arr=array();
	$p=Product::getInstance($products->product_ids[$i]);
	foreach ($columns as $name) {
		$arr[]=$p->getString($name);
	}
	echo sputcsv($arr);
}
