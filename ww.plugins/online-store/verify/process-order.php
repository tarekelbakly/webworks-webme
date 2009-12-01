<?php
function process_order($id,$order){
	dbQuery("UPDATE online_store_orders SET status='1' WHERE id=$id");
	$form_vals=json_decode($order['form_vals'],true);
	$from='noreply@'.str_replace('www.','',$_SERVER['HTTP_HOST']);
	$headers = "From: $from\r\nReply-To: $from\r\nX-Mailer: PHP/".phpversion()."\r\n";
	$headers.='MIME-Version: 1.0'."\r\n";
	$headers.= 'Content-type: text/html; charset=utf-8'."\r\n";
	$headers.= 'To: '.$form_vals['Email']. "\r\n";
	mail($form_vals['Email'],'['.str_replace('www.','',$_SERVER['HTTP_HOST']).'] invoice #'.$id, $order['invoice'], $headers);
}
