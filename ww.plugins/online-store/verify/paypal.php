<?php
// taken from https://www.paypaltech.com/SG2/
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}
if($req=='cmd=_notify-validate')die('please don\'t access this file directly');
// post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
if (!$fp) {
	// HTTP ERROR
} else {
	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, "VERIFIED") == 0) {

			// TODO
			// check the payment_status is Completed
			// check that txn_id has not been previously processed

			require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
			list($userid,$id)=explode('|',$_POST['item_number']);
			$userid=(int)$userid;
			$id=(int)$id;
			if(!$id || !$userid)exit;

			// check that payment_amount/payment_currency are correct
			$order=dbRow("SELECT * FROM online_store_orders WHERE id=$id");
			if($order['cost'] != $_POST['mc_gross']){
				$str='';
				foreach($_POST as $key => $value){
					$str.=$key." = ". $value."\n";
				}
				mail('kae@verens.com',$_SERVER['HTTP_HOST'].' paypal hack',$str);
				exit;
			}

			// process payment
			require dirname(__FILE__).'/process-order.php';
			process_order($id,$order);
//			$db->query("UPDATE online_store_orders SET status='1' WHERE id=$id");
//			$form_vals=json_decode($order['form_vals']);
//			$from='noreply@'.str_replace('www.',$_SERVER['HTTP_HOST']);
//			$headers = "From: $from\r\nReply-To: $from\r\nX-Mailer: PHP/" . phpversion();
//			$headers.='MIME-Version: 1.0' . "\r\n";
//			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
//			$headers .= 'To: '.$form_vals['Email']. "\r\n";
//			mail($form_vals['Email'],'['.str_replace('www.',$_SERVER['HTTP_HOST']).'] invoice #'.$id, $order['invoice'], $headers);
		}
		else if (strcmp ($res, "INVALID") == 0) {
			// echo the response
			mail('kae@webworks.ie','['.$_SERVER['HTTP_HOST'].'] error in paypal response',"The response from IPN was: <b>" .$res ."</b>");
		}
		
	}
	fclose ($fp);
}
?>
