<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');

$fields=array();
$filter='';
if($_REQUEST['other_GET_params']){
	$filter=' where id='.(int)$_REQUEST['other_GET_params'];
}
$rs=dbAll('select data_fields from products_types'.$filter);
foreach($rs as $r){
	$fs=json_decode($r['data_fields']);
	foreach($fs as $f)$fields[]=$f->n;
}
$fields=array_unique($fields);
asort($fields);
foreach($fields as $field){
	$c.='<option';
	if($field==$vars['products_order_by'])$c.=' selected="selected"';
	$c.='>'.htmlspecialchars($field).'</option>';
}
echo $c;
