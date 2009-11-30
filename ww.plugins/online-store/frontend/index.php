<?php
$c='';
if(isset($_REQUEST['action']) && $_REQUEST['action']){
	unset($_REQUEST['action']);
	unset($_REQUEST['page']);
	$formvals=addslashes(json_encode($_REQUEST));
	$items=addslashes(json_encode($_SESSION['online-store']['items']));
	$total=$_SESSION['online-store']['total'];
	dbQuery("insert into online_store_orders (form_vals,total,items,date_created) values('$formvals',$total,'$items',now())");
	$id=dbOne('select last_insert_id() as id','id');
	$c.='<p>Your order has been recorded. Please click the button below to go to PayPal for payment. Thank you.</p>';
	$c.='<form method="post" action="https://www.paypal.com/cgi-bin/webscr"><input type="hidden" value="_xclick" name="cmd"/>'
			.'<input type="hidden" value="'.$PAGEDATA->vars['online_stores_paypal_address'].'" name="business"/>'
			.'<input type="hidden" value="Purchase made from '.$_SERVER['HTTP_HOST'].'" name="item_name"/>'
			.'<input type="hidden" value="'.$id.'" name="item_number"/>'
			.'<input type="hidden" value="'.$total.'" name="amount"/>'
			.'<input type="hidden" value="EUR" name="currency_code"/><input type="hidden" value="1" name="no_shipping"/><input type="hidden" value="1" name="no_note"/>'
			.'<input type="hidden" value="http://'.$_SERVER['HTTP_HOST'].'/ww.plugins/online-checkout/verify/paypal.php" name="notify_url"/>'
			.'<input type="hidden" value="IC_Sample" name="bn"/><input type="image" alt="Make payments with payPal - it\'s fast, free and secure!" name="submit" src="https://www.paypal.com/en_US/i/btn/x-click-but23.gif"/><img width="1" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt=""/></form>';
}
else{
	$c.='<form method="post">';
	$c.=$PAGEDATA->body;
	$c.='<input type="submit" name="action" value="Proceed to Payment" /></form>';
}
