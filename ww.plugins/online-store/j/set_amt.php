<?php
session_start();
$md5=$_REQUEST['md5'];
if(!isset($_SESSION['online-store']['items'][$md5]))die('no such item');

$amt=(int)$_REQUEST['amt'];
if($amt<1)unset($_SESSION['online-store']['items'][$md5]);
else $_SESSION['online-store']['items'][$md5]['amt']=$amt;

require '../libs.php';
$total=online_store_calculate_total();
$item_total=$amt?$_SESSION['online-store']['items'][$md5]['cost']*$amt:0;

echo '{"amt":'.$amt.',"item_total":'.$item_total.',"total":'.$total.'}';
