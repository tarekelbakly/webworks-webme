<?php
require '../../ww.incs/basics.php';
if(!is_admin())exit;

$id=(int)$_REQUEST['id'];
$body=addslashes($_REQUEST['body']);
dbQUery("update pages set body='$body' where id=$id");
cache_clear('pages');
echo 'ok';
