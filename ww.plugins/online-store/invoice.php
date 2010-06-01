<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';

if(!is_admin())die('access denied');

$r=dbRow('select invoice from online_store_orders where id='.((int)$_REQUEST['id']));
echo $r['invoice'];
