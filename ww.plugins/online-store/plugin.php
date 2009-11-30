<?php
$plugin=array(
	'name' => 'Online Store',
	'admin' => array(
		'page_type' => 'online_store_admin_page_form'
#		'widget' => array(
#			'form_url' => '/ww.plugins/online-store/admin/widget-form.php'
#		)
	),
	'description' => 'Add online-shopping capabilities to a number of other plugins.',
	'frontend' => array(
		'widget' => 'online_store_show_basket_widget',
		'page_type' => 'online_store_frontend'
	),
	'version' => '3'
);
function online_store_frontend($PAGEDATA){
	require dirname(__FILE__).'/frontend/index.php';
	return $c;
}
function online_store_admin_page_form($page,$vars){
	require dirname(__FILE__).'/admin/index.php';
	return $c;
}
function online_store_show_basket_widget($vars=null){
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
			$html.='<tr class="os_item_numbers" id="'.$md5.'"><td>&nbsp;</td><td>€'.$item['cost'].'</td>';
			// { amount
			$html.='<td class="amt">'.$item['amt'].'</td>';
			// }
			$html.='<td class="item-total">€'.($item['cost']*$item['amt']).'</td></tr>';
		}
		$html.='<tr class="os_total"><th colspan="3">Total</th><td class="total">€'.$_SESSION['online-store']['total'].'</td></tr>';
		$html.='</table>';
		$html.='<a href="/common/redirector.php?type=online-store">Proceed to Checkout</a>';
	}
	else $html.='<em>empty</em>';
	$html.='</div><script src="/ww.plugins/online-store/j/basket.js"></script>';
	return $html;
}
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
