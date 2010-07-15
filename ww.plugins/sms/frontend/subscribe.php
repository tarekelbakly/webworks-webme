<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';

$aid=(int)$_REQUEST['id'];
$name=$_REQUEST['name'];
$phone=$_REQUEST['phone'];

if(!$name || $name=='[insert subscriber name]'){
	echo '{"err":1,"errmsg":"no name provided"}';
	exit;
}
if(preg_replace('/[^0-9]/','',$phone)!=$phone
	|| !preg_match('/^44|^353/',$phone)
){
	echo '{"err":2,"errmsg":"incorrect number format"}';
	exit;
}

$sid=dbOne('select id from sms_subscribers where phone="'.$phone.'" limit 1','id');
if(!$sid){
	dbQuery('insert into sms_subscribers (name,phone,date_created) values("'.addslashes($name).'","'.$phone.'",now())');
	$sid=dbOne('select id from sms_subscribers where phone="'.$phone.'" limit 1','id');
}

$subscribers=json_decode(dbOne('select subscribers from sms_addressbooks where id='.$aid,'subscribers'));

if(in_array($sid,$subscribers)){
	echo '{"err":3,"errmsg":"you are already subscribed to this list"}';
	exit;
}

$subscribers[]=$sid;
dbQuery('update sms_addressbooks set subscribers="'.addslashes(json_encode($subscribers)).'" where id='.$aid);

echo '{"err":0}';
