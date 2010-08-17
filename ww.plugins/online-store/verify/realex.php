<?php
/**
	* based on information in the Real Auth Developers Guide
	*
	* PHP version 5
	*
	* @category None
	* @package  None
	* @author   Kae Verens <kae@webworks.ie>
	* @license  GPL 2.0
	* @link     None
*/

$merchantid=$_REQUEST['MERCHANT_ID'];
$account=   $_REQUEST['ACCOUNT'];
$id=        $_REQUEST['ORDER_ID'];
$authcode=  $_REQUEST['AUTHCODE'];
$result=    $_REQUEST['RESULT'];
$message=   $_REQUEST['MESSAGE'];
$pasref=    $_REQUEST['PASREF'];
$batchid=   $_REQUEST['BATCHID'];

require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
$id=(int)$_POST['item_number'];
if ($id<1) {
	exit;
}

// check that payment_amount/payment_currency are correct
$order=dbRow("SELECT * FROM online_store_orders WHERE id=$id");
if ($order['total'] != $_POST['mc_gross']) {
	$str='';
	foreach ($_POST as $key => $value) {
		$str.=$key." = ". $value."\n";
	}
	mail('kae@verens.com', $_SERVER['HTTP_HOST'].' realex hack', $str);
	exit;
}

// process payment
require dirname(__FILE__).'/process-order.php';
OnlineStore_processOrder($id, $order);
