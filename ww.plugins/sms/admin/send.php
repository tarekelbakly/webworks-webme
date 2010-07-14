<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');
require SCRIPTBASE.'ww.plugins/sms/admin/libs.php';

$to=$_REQUEST['to'];
if(!$to || preg_replace('/[^0-9]/','',$to)!=$to)exit;
$msg=$_REQUEST['msg'];
if(!$msg)exit;
if(preg_replace('/a-zA-Z0-9 !_\-.,:\'"/','',$msg)!=$msg)exit;

$ret=SMS_callApi('send','&to='.$to.'&message='.urlencode($msg));
echo json_encode($ret);
