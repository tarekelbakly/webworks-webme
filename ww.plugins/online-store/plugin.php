<?php
// { define $plugin
$plugin=array(
	'name' => 'Online Store',
	'admin' => array(
		'page_type' => 'online_store_admin_page_form'
	),
	'description' => 'Add online-shopping capabilities to a number of other plugins.',
	'frontend' => array(
		'widget' => 'online_store_show_basket_widget',
		'page_type' => 'online_store_frontend'
	),
	'triggers' => array(
		'displaying-pagedata' => 'online_store_pagedata'
	),
	'version' => '5'
);
// }
// { currency symbols
$online_store_currencies=array(
	'EUR'=>array('&euro;','Euro'),
	'GBP'=>array('&pound;','Pound Sterling')
);
// }
function online_store_add_to_cart($cost=0,$amt=0,$short_desc='',$long_desc='',$md5='',$url=''){
	// { add item to session
	if(!isset($_SESSION['online-store']))$_SESSION['online-store']=array('items'=>array(),'total'=>0);
	$item=(isset($_SESSION['online-store']['items'][$md5]))?$_SESSION['online-store']['items'][$md5]:array('cost'=>0,'amt'=>0,'short_desc'=>$short_desc,'long_desc'=>$long_desc,'url'=>$url);
	$item['cost']=$cost;
	$item['amt']+=$amt;
	$item['short_desc']=$short_desc;
	$item['url']=$url;
	$_SESSION['online-store']['items'][$md5]=$item;
	// }
	require dirname(__FILE__).'/libs.php';
	online_store_calculate_total();
}
function online_store_admin_page_form($page,$vars){
	require dirname(__FILE__).'/admin/index.php';
	return $c;
}
function online_store_frontend($PAGEDATA){
	require dirname(__FILE__).'/frontend/index.php';
	return $c;
}
function online_store_generate_paypal_button($PAGEDATA,$id,$total){
	global $DBVARS;
	return '<form id="online-store-paypal" method="post" action="https://www.paypal.com/cgi-bin/webscr"><input type="hidden" value="_xclick" name="cmd"/>'
		.'<input type="hidden" value="'.$PAGEDATA->vars['online_stores_paypal_address'].'" name="business"/>'
		.'<input type="hidden" value="Purchase made from '.$_SERVER['HTTP_HOST'].'" name="item_name"/>'
		.'<input type="hidden" value="'.$id.'" name="item_number"/>'
		.'<input type="hidden" value="'.$total.'" name="amount"/>'
		.'<input type="hidden" value="'.$DBVARS['online_store_currency'].'" name="currency_code"/><input type="hidden" value="1" name="no_shipping"/><input type="hidden" value="1" name="no_note"/>'
		.'<input type="hidden" value="http://'.$_SERVER['HTTP_HOST'].'/ww.plugins/online-store/verify/paypal.php" name="notify_url"/>'
		.'<input type="hidden" value="IC_Sample" name="bn"/><input type="image" alt="Make payments with payPal - it\'s fast, free and secure!" name="submit" src="https://www.paypal.com/en_US/i/btn/x-click-but23.gif"/><img width="1" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt=""/></form>';
}
function online_store_pagedata(){
	$currency=$GLOBALS['DBVARS']['online_store_currency'];
	$currency_symbols=array('EUR'=>'€','GBP'=>'£');
	return ',"currency":"'.$currency_symbols[$currency].'"';
}
function online_store_show_basket_widget($vars=null){
	global $DBVARS,$online_store_currencies;
	$csym=$online_store_currencies[$DBVARS['online_store_currency']][0];
	$html='<div class="online-store-basket-widget">';
	if(!isset($_SESSION['online-store']))$_SESSION['online-store']=array('items'=>array(),'total'=>0);
	if(count($_SESSION['online-store']['items'])){
		$html.='<table>';
		$html.='<tr><th>&nbsp;</th><th>Price</th><th>Amount</th><th>Total</th></tr>';
		foreach($_SESSION['online-store']['items'] as $md5=>$item){
			// { name
			$html.='<tr class="os_item_name"><td colspan="4">';
			if($item['url'])$html.='<a href="'.$item['url'].'">';
			$html.=$item['short_desc'];
			if($item['url'])$html.='</a>';
			$html.='</td></tr>';
			// }
			$html.='<tr class="os_item_numbers" id="'.$md5.'"><td>&nbsp;</td><td>'.$csym.$item['cost'].'</td>';
			// { amount
			$html.='<td class="amt">'.$item['amt'].'</td>';
			// }
			$html.='<td class="item-total">'.$csym.($item['cost']*$item['amt']).'</td></tr>';
		}
		$html.='<tr class="os_total"><th colspan="3">Total</th><td class="total">'.$csym.$_SESSION['online-store']['total'].'</td></tr>';
		$html.='</table>';
		$html.='<a href="/common/redirector.php?type=online-store">Proceed to Checkout</a>';
	}
	else $html.='<em>empty</em>';
	$html.='</div><script src="/ww.plugins/online-store/j/basket.js"></script>';
	return $html;
}
