<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
require 'show.php';

$PAGEDATA=Page::getInstance($_REQUEST['pid']);
$PAGEDATA->initValues();

$columns=explode(
	',',
	preg_replace('/[^a-z0-9\-_,]/','_',strtolower($_REQUEST['sColumns']))
);
$sort_col=(int)$columns[(int)$_REQUEST['iSortCol_0']];
$sort_dir=$_REQUEST['sSortDir_0'];
if ($sort_dir!='des') {
	$sort_dir='asc';
}

$search=$_REQUEST['sSearch'];
$search_arr=array();
for ($i=0; $i<count($columns); ++$i) {
	if (!isset($_REQUEST['sSearch_'.$i]) || $_REQUEST['sSearch_'.$i]==='') {
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
$i=$_REQUEST['iDisplayStart'];
$finish=$_REQUEST['iDisplayStart']+$_REQUEST['iDisplayLength'];

for (; $i<$finish && $i<$total_records; ++$i) {
	$arr=array();
	$p=Product::getInstance($products->product_ids[$i]);
	foreach ($columns as $name) {
		$arr[]=$p->getString($name);
	}
	$returned_products[]=$arr;
}

echo json_encode(array(
	'iTotalRecords'=>$total_records,
	'iTotalDisplayRecords'=>$total_records,
	'aaData'=>$returned_products
));

/*
pid=83
_=1289213251329
sEcho=6
iColumns=55
sColumns=Name%2CID%2CCoTotal%2CDVO%2CJob+Title%2CFirstName%2CSurname%2CCompanyName%2CAddress1%2CAddress2%2CAddress3%2CTelephone%2CMobile%2CDOM%2CAf+2000%2CAff+2001%2CAff+02%2CShare+App%2CP%2FPP%2CAff03%2CAff04%2CTr+P%2CTakenOver%2CAff05%2CInfo4%2CType+of+Source%2CS+Address1%2CSAddress2%2CType+of+Source2%2CAddress+SecondS%2CTreatment%2CType+of+Treatment%2CYear+Installed%2CType+Treatment2%2CYear+Installed2%2CDBO%2CBundle+Name%2CDBOC%2CCR%2CO%26M+Contract+Signed%2CMG+Tr%2CSP+SafePass%2CAff15%2CAff14%2CAff13%2CPMS%2CAff12%2CLC+Tr%2CAff11%2CAff06%2CAff10%2CAff09%2CAff08%2CAff07%2CEmail
iDisplayStart=0
iDisplayLength=10
sNames=Name%2CID%2CCoTotal%2CDVO%2CJob+Title%2CFirstName%2CSurname%2CCompanyName%2CAddress1%2CAddress2%2CAddress3%2CTelephone%2CMobile%2CDOM%2CAf+2000%2CAff+2001%2CAff+02%2CShare+App%2CP%2FPP%2CAff03%2CAff04%2CTr+P%2CTakenOver%2CAff05%2CInfo4%2CType+of+Source%2CS+Address1%2CSAddress2%2CType+of+Source2%2CAddress+SecondS%2CTreatment%2CType+of+Treatment%2CYear+Installed%2CType+Treatment2%2CYear+Installed2%2CDBO%2CBundle+Name%2CDBOC%2CCR%2CO%26M+Contract+Signed%2CMG+Tr%2CSP+SafePass%2CAff15%2CAff14%2CAff13%2CPMS%2CAff12%2CLC+Tr%2CAff11%2CAff06%2CAff10%2CAff09%2CAff08%2CAff07%2CEmail
sSearch_6=fell
iSortingCols=1
iSortCol_0=0
sSortDir_0=asc
*/
