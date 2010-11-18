<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';

$term=$_REQUEST['term'];

$rs=dbAll('select id,name from products where name like "%'.addslashes($term).'%" or data_fields like "%'.addslashes($term).'%" limit 20');

$res=array();
foreach ($rs as $r) {
	$res[]=array(
		'id'=>$r['id'],
		'label'=>$r['name'],
		'value'=>$r['name']
	);
}
echo json_encode($res);
