<?php
/**
	* partly from https://www.paypaltech.com/SG2/
	* read the post from PayPal system and add 'cmd'
	*
	* PHP version 5
	*
	* @category None
	* @package  None
	* @author   Kae Verens <kae@webworks.ie>
	* @license  GPL 2.0
	* @link     None
*/
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}
if ($_POST['payment_status'] == 'Refunded') {
	exit;
}
if ($req=='cmd=_notify-validate') {
	die('please don\'t access this file directly');
}
// post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen('ssl://www.paypal.com', 443, $errno, $errstr, 30);
if (!$fp) {
	// HTTP ERROR
} else {
	fputs($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets($fp, 1024);
		if (strcmp($res, "VERIFIED") == 0) {
			require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
			$gid=(int)$_POST['item_number'];
			$uid=(int)$_POST['custom'];
			if ($gid<1 || $uid<1) {
				exit;
			}
			$meta=json_decode(dbOne('select meta from groups where id='.$gid, 'meta'), true);
			$r=dbOne('select expires from users_groups where user_accounts_id='.$uid.' and groups_id='.$gid, 'expires');
			if ($r) {
				dbQuery('update users_groups set expires=date_add(expires, interval '.$meta['paid-membership-subscription-period-num'].' '.$meta['paid-membership-subscription-period'].') where user_accounts_id='.$uid.' and groups_id='.$gid);
			}
			else {
//				Core_addUserToGroup($uid, $gid);
				dbQuery('insert into users_groups set expires=date_add(now(), interval '.$meta['paid-membership-subscription-period-num'].' '.$meta['paid-membership-subscription-period'].'),user_accounts_id='.$uid.',groups_id='.$gid);
			}
		}
		else if (strcmp($res, "INVALID") == 0) {
		}
	}
	fclose($fp);
}
