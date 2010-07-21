<?php
/**
	* Online-Store front-end page type
	*
	* PHP version 5
	*
	* @category None
	* @package  None
	* @author   Kae Verens <kae@webworks.ie>
	* @license  GPL 2.0
	* @link     None
	*/

$c='';
global $DBVARS,$online_store_currencies;
$csym=$online_store_currencies[$DBVARS['online_store_currency']][0];
$submitted=0;
if (isset($_REQUEST['action']) && $_REQUEST['action']) {
	$errors=array();
	// { check for errors in form submission
	$fields=$PAGEDATA->vars['online_stores_fields'];
	if (!$fields) {
		$fields='{}';
	}
	$fields=json_decode($fields);
	foreach ($fields as $name=>$field) {
		if (!$field->show) {
			continue;
		}
		if ($field->required && (!isset($_REQUEST[$name]) || !$_REQUEST[$name])) {
			$errors[]='You must enter the "'.htmlspecialchars($name).'" field.';
		}
	}
	// }
	unset($_REQUEST['action']);
	unset($_REQUEST['page']);
	if (count($errors)) {
		$c.='<div class="errors"><em>'.join('</em><br /><em>', $errors).'</em></div>';
	}
	else {
		$formvals=addslashes(json_encode($_REQUEST));
		$items=addslashes(json_encode($_SESSION['online-store']['items']));
		$total=$_SESSION['online-store']['total'];
		// { save data
		dbQuery(
			'insert into online_store_orders (form_vals,total,items,date_created)'
			." values('$formvals', $total, '$items', now())"
		);
		$id=dbOne('select last_insert_id() as id', 'id');
		// }
		// { generate invoice
		require_once SCRIPTBASE . 'ww.incs/Smarty-2.6.26/libs/Smarty.class.php';
		$smarty = new Smarty;
		$smarty->compile_dir=USERBASE . 'templates_c';
		$smarty->register_function('INVOICETABLE', 'online_store_invoice_table');
		foreach ($_REQUEST as $key=>$val) {
			$smarty->assign($key, $val);
		}
		// { table of items
		$table='<table width="100%"><tr><th class="quantityheader">Quantity</th>'
			.'<th class="descriptionheader">Description</th><th class="unitamountheader">'
			.'Unit Price</th><th class="amountheader">Amount</th></tr>';
		foreach ($_SESSION['online-store']['items'] as $key=>$item) {
			$table.='<tr><td class="quantitycell">'.$item['amt']
				.'</td><td class="descriptioncell"><a href="'.$item['url'].'">'
				.preg_replace('/<[^>]*>/', '', $item['short_desc'])
				.'</td><td class="unitamountcell">'.$csym.sprintf("%.2f", $item['cost'])
				.'</td><td class="amountcell">'.$csym.sprintf("%.2f", $item['cost']*$item['amt'])
				.'</td></tr>';
		}
		$table.='<tr class="os_basket_totals"><td colspan="3" style="text-align:right">'
			.'Subtotal</td><td class="totals amountcell">'.$csym.sprintf("%.2f", $total)
			.'</td></tr>';
		$table.='</table>';
		$smarty->assign('_invoice_table', $table);
		$smarty->assign('_invoicenumber', $id);
		// }
		$invoice=addslashes(
			$smarty->fetch(
				USERBASE.'ww.cache/online-store/'.$PAGEDATA->id
			)
		);
		dbQuery("update online_store_orders set invoice='$invoice' where id=$id");
		// }
		// { show PayPal button
		$c.='<p>Your order has been recorded. Please click the button below to go to '
			.'PayPal for payment. Thank you.</p>';
		$c.=OnlineStore_generatePaypalButton($PAGEDATA, $id, $total);
		// }
		// { unset the shopping cart data
		unset($_SESSION['online-store']);
		// }
		$submitted=1;
	}
}
if (!$submitted) {
	$c.='<form method="post">';
	$c.=$PAGEDATA->render();
	$c.='<input type="submit" name="action" value="Proceed to Payment" /></form>';
}
