<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
$id=(int)$_REQUEST['id'];
$userid=(int)$_REQUEST['id'];
$ccn=$_REQUEST['ccn'];
$cc_name=$_REQUEST['cc_name'];
$ccvn=$_REQUEST['ccvn'];
$expiry_year=$_REQUEST['expiry_year'];
$expiry_month=$_REQUEST['expiry_month'];

require 'credit-card-realex.php';

$order=dbRow("SELECT * FROM planzone_orders WHERE id=$id");

die('mwuahaha!');
list() = os_submitRealex(
	'planzone',     // $merchantid,
	'sCZtPFrKaV',   // $secret
	'planzone',     // ,$account
	$order['cost'], // ,$amount,
	$ccn,           // $cardnumber,
	$cc_name$cardname,$cardtype,$expdate,$formid);

// process payment
$db->query("UPDATE planzone_orders SET status='1' WHERE id=$id");
$details=json_decode($order['planzone_items'],true);
if(isset($details['voucher'])){
	dbQuery('update planzone_vouchers set user_id='.$userid.',date_used=now() where id='.$details['voucher']['id']);
}
require 'build-links.php';
