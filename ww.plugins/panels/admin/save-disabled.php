<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');

if(isset($_REQUEST['id']) && isset($_REQUEST['disabled'])){
	$id=(int)$_REQUEST['id'];
	$disabled=(int)$_REQUEST['disabled'];
	dbQuery("update panels set disabled='$disabled' where id=$id");
	echo "update panels set disabled='$disabled' where id=$id";
}
echo 'done';
