<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');

if(!isset($_REQUEST['id']))exit;
$id=(int)$_REQUEST['id'];

echo dbOne('select invoice from online_store_orders where id='.$id,'invoice');
