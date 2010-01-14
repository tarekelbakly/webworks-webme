<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');

if(!isset($_REQUEST['id']))exit;
$id=(int)$_REQUEST['id'];
$status=(int)$_REQUEST['status'];

dbQuery('update online_store_orders set status='.$status.' where id='.$id);
echo 1;
