<?php
require '../common.php';

$strings=array('Credit Card Number','Card Type','Visa','Laser','Mastercard','Expiry Date','Card Security Number','3-digit number on back of card',
	'You\'ve selected PayPal. When you submit this form, you will be redirected to the PayPal site. When you are finished there, please follow their link back to this site.',
	'You\'ve selected Cheque. When you submit this form, you will be given a transaction ID. Please write the ID down as it will be used when you are paying for the transaction.');

header('Cache-Control: max-age=2592000');
header('Expires-Active: On');
header('Expires: Fri, 1 Jan 2500 01:01:01 GMT');
header('Pragma:');
header('Content-type: text/javascript; charset=utf-8');

foreach($strings as $str)echo 'lang["'.addslashes($str).'"]="'.addslashes(__($str)).'";';
