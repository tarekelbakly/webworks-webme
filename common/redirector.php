<?php

require '../common.php';

$id=@(int)$_REQUEST['id'];
$type=@$_REQUEST['type'];
$url='/';
switch($type){
	case 'loginpage': // {
		$p=Page::getInstanceByType('privacy');
		if(!$p)$url='/';
		else $url=$p->getRelativeUrl();
		if(isset($_REQUEST['login_referer']))$url.='?login_referer='.urlencode($_REQUEST['login_referer']);
		$url.='#Login';
		break;
	// }
	case 'product': // {
		$p=Product::getInstance($id);
		if($p)$url=$p->getPageURL();
		break;
	// }
	case 'product_category': // {
		$p=ProductCategory::getInstance($id);
		if($p)$url=$p->getPageURL();
		break;
	// }
	case 'os_wishlist': // {
		$p=Page::getInstanceByType(3);
		if(!$p)$url='/';
		else $url=$p->getRelativeUrl().'#os_wishlist';
		break;
	// }
	default: // {
		$p=Page::getInstanceByType($type);
		if(!$p)$url='/';
		else $url=$p->getRelativeUrl();
	// }
}
redirect($url);