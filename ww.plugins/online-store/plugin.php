<?php
/**
	* definition file for Online-Store plugin
	*
	* PHP version 5
	*
	* @category None
	* @package  None
	* @author   Kae Verens <kae@webworks.ie>
	* @license  GPL 2.0
	* @link     None
	*/

// { define $plugin
$plugin=array(
	'name' => 'Online Store',
	'admin' => array(
		'page_type' => 'OnlineStore_adminPageForm'
	),
	'description' => 'Add online-shopping capabilities to a number of other plugins.',
	'frontend' => array(
		'widget' => 'OnlineStore_showBasketWidget',
		'page_type' => 'OnlineStore_frontend'
	),
	'triggers' => array(
		'displaying-pagedata' => 'OnlineStore_pagedata'
	),
	'version' => '6'
);
// }
// { currency symbols
$online_store_currencies=array(
	'EUR'=>array('&euro;','Euro'),
	'GBP'=>array('&pound;','Pound Sterling')
);
// }

/**
	* adds a product to the cart
	*
	* @param float  $cost       cost of the product
	* @param int    $amt        how many to add
	* @param string $short_desc short description of the product
	* @param string $long_desc  long description of the product
	* @param string $md5        a unique key for storing this product in the session
	* @param string $url        URL where the product can be viewed
	*
	* @return null
	*/
function OnlineStore_addToCart(
				$cost=0, $amt=0, $short_desc='',
				$long_desc='', $md5='', $url=''
) {
	// { add item to session
	if (!isset($_SESSION['online-store'])) {
		$_SESSION['online-store']=array('items'=>array(),'total'=>0);
	}
	$item=(isset($_SESSION['online-store']['items'][$md5]))
		?$_SESSION['online-store']['items'][$md5]
		:array('cost'=>0,'amt'=>0,'short_desc'=>$short_desc,
			'long_desc'=>$long_desc,'url'=>$url);
	$item['cost']=$cost;
	$item['amt']+=$amt;
	$item['short_desc']=$short_desc;
	$item['url']=$url;
	$_SESSION['online-store']['items'][$md5]=$item;
	// }
	require dirname(__FILE__).'/libs.php';
	OnlineStore_calculateTotal();
}

/**
	* admin area Page form
	*
	* @param object $page Page array from database
	* @param array  $vars Page's custom variables
	*
	* @return string
	*/
function OnlineStore_adminPageForm($page, $vars) {
	require dirname(__FILE__).'/admin/index.php';
	return $c;
}

/**
	* stub function to load frontend page-type
	*
	* @param object $PAGEDATA the current page
	*
	* @return string
	*/
function OnlineStore_frontend($PAGEDATA) {
	require dirname(__FILE__).'/frontend/index.php';
	return $c;
}

/**
	* return HTML for a PayPal button to pay for the current Online-Store order
	*
	* @param object $PAGEDATA the checkout page
	* @param int    $id       the order ID
	* @param float  $total    the order total
	*
	* @return string
	*/
function OnlineStore_generatePaypalButton($PAGEDATA, $id, $total) {
	global $DBVARS;
	return '<form id="online-store-paypal" method="post" action="https://www.paypal.com'
		.'/cgi-bin/webscr"><input type="hidden" value="_xclick" name="cmd"/>'
		.'<input type="hidden" value="'.$PAGEDATA->vars['online_stores_paypal_address']
		.'" name="business"/>'
		.'<input type="hidden" value="Purchase made from '.$_SERVER['HTTP_HOST']
		.'" name="item_name"/>'
		.'<input type="hidden" value="'.$id.'" name="item_number"/>'
		.'<input type="hidden" value="'.$total.'" name="amount"/>'
		.'<input type="hidden" value="'.$DBVARS['online_store_currency']
		.'" name="currency_code"/><input type="hidden" value="1" name="no_shipping"/>'
		.'<input type="hidden" value="1" name="no_note"/>'
		.'<input type="hidden" value="http://'.$_SERVER['HTTP_HOST']
		.'/ww.plugins/online-store/verify/paypal.php" name="notify_url"/>'
		.'<input type="hidden" value="IC_Sample" name="bn"/><input type="image" alt="Make'
		.' payments with payPal - it\'s fast, free and secure!" name="submit" src="https:'
		.'//www.paypal.com/en_US/i/btn/x-click-but23.gif"/><img width="1" height="1" src='
		.'"https://www.paypal.com/en_US/i/scr/pixel.gif" alt=""/></form>';
}

/**
	* returns currency information to be added to global JS script
	*
	* @return string
	*/
function OnlineStore_pagedata() {
	$currency=$GLOBALS['DBVARS']['online_store_currency'];
	$currency_symbols=array('EUR'=>'€','GBP'=>'£');
	return ',"currency":"'.$currency_symbols[$currency].'"';
}

/**
	* returns a HTML string to show the Online-Store basket
	*
	* @return string
	*/
function OnlineStore_showBasketWidget() {
	global $DBVARS,$online_store_currencies;
	$csym=$online_store_currencies[$DBVARS['online_store_currency']][0];
	$html='<div class="online-store-basket-widget">';
	if (!isset($_SESSION['online-store'])) {
		$_SESSION['online-store']=array('items'=>array(),'total'=>0);
	}
	if (count($_SESSION['online-store']['items'])) {
		$html.='<table>';
		$html.='<tr><th>&nbsp;</th><th>Price</th><th>Amount</th><th>Total</th></tr>';
		foreach ($_SESSION['online-store']['items'] as $md5=>$item) {
			// { name
			$html.='<tr class="os_item_name"><td colspan="4">';
			if ($item['url']) {
				$html.='<a href="'.$item['url'].'">';
			}
			$html.=$item['short_desc'];
			if ($item['url']) {
				$html.='</a>';
			}
			$html.='</td></tr>';
			// }
			$html.='<tr class="os_item_numbers" id="'.$md5.'"><td>&nbsp;</td><td>'
				.$csym.$item['cost'].'</td>';
			// { amount
			$html.='<td class="amt">'.$item['amt'].'</td>';
			// }
			$html.='<td class="item-total">'.$csym.($item['cost']*$item['amt']).'</td></tr>';
		}
		$html.='<tr class="os_total"><th colspan="3">Total</th><td class="total">'
			.$csym.$_SESSION['online-store']['total'].'</td></tr>';
		$html.='</table>';
		$html.='<a href="/common/redirector.php?type=online-store">'
			.'Proceed to Checkout</a>';
	}
	else {
		$html.='<em>empty</em>';
	}
	$html.='</div><script src="/ww.plugins/online-store/j/basket.js"></script>';
	return $html;
}
