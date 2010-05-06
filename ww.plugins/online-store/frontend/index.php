<?php
$c='';
global $DBVARS,$online_store_currencies;
$csym=$online_store_currencies[$DBVARS['online_store_currency']][0];
if(isset($_REQUEST['action']) && $_REQUEST['action']){
	unset($_REQUEST['action']);
	unset($_REQUEST['page']);
	$formvals=addslashes(json_encode($_REQUEST));
	$items=addslashes(json_encode($_SESSION['online-store']['items']));
	$total=$_SESSION['online-store']['total'];
	// { save data
	dbQuery("insert into online_store_orders (form_vals,total,items,date_created) values('$formvals',$total,'$items',now())");
	$id=dbOne('select last_insert_id() as id','id');
	// }
	// { generate invoice
	include_once SCRIPTBASE . 'common/Smarty/Smarty.class.php';
	$smarty = new Smarty;
	$smarty->compile_dir=USERBASE . 'templates_c';
	$smarty->register_function('INVOICETABLE','online_store_invoice_table');
	foreach($_REQUEST as $key=>$val)$smarty->assign($key,$val);
	// { table of items
	$table='<table width="100%"><tr><th class="quantityheader">Quantity</th><th class="descriptionheader">Description</th><th class="unitamountheader">Unit Price</th><th class="amountheader">Amount</th></tr>';
	foreach($_SESSION['online-store']['items'] as $key=>$item){
		$table.='<tr><td class="quantitycell">'.$item['amt'].'</td><td class="descriptioncell"><a href="'.$item['url'].'">'.preg_replace('/<[^>]*>/','',$item['short_desc']).'</td><td class="unitamountcell">'.$csym.($item['cost']/$item['amt']).'</td><td class="amountcell">'.$csym.$item['cost'].'</td></tr>';
	}
	$table.='<tr class="os_basket_totals"><td colspan="3" style="text-align:right">Subtotal</td><td class="totals">'.$csym.$total.'</td></tr>';
	$table.='</table>';
	$smarty->assign('_invoice_table',$table);
	$smarty->assign('_invoicenumber',$id);
	// }
	$invoice=addslashes($smarty->fetch(USERBASE.'ww.cache/online-store/'.$PAGEDATA->id));
	dbQuery("update online_store_orders set invoice='$invoice' where id=$id");
	// }
	// { show PayPal button
	$c.='<p>Your order has been recorded. Please click the button below to go to PayPal for payment. Thank you.</p>';
	$c.='<form method="post" action="https://www.paypal.com/cgi-bin/webscr"><input type="hidden" value="_xclick" name="cmd"/>'
			.'<input type="hidden" value="'.$PAGEDATA->vars['online_stores_paypal_address'].'" name="business"/>'
			.'<input type="hidden" value="Purchase made from '.$_SERVER['HTTP_HOST'].'" name="item_name"/>'
			.'<input type="hidden" value="'.$id.'" name="item_number"/>'
			.'<input type="hidden" value="'.$total.'" name="amount"/>'
			.'<input type="hidden" value="'.$DBVARS['online_store_currency'].'" name="currency_code"/><input type="hidden" value="1" name="no_shipping"/><input type="hidden" value="1" name="no_note"/>'
			.'<input type="hidden" value="http://'.$_SERVER['HTTP_HOST'].'/ww.plugins/online-checkout/verify/paypal.php" name="notify_url"/>'
			.'<input type="hidden" value="IC_Sample" name="bn"/><input type="image" alt="Make payments with payPal - it\'s fast, free and secure!" name="submit" src="https://www.paypal.com/en_US/i/btn/x-click-but23.gif"/><img width="1" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt=""/></form>';
	// }
}
else{
	$c.='<form method="post">';
	$c.=$PAGEDATA->render();
	$c.='<input type="submit" name="action" value="Proceed to Payment" /></form>';
}
