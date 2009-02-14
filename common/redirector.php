<?php

require '../common.php';

$id=@(int)$_REQUEST['id'];
$type=@$_REQUEST['type'];
$url='/';
switch($type){
	case 'loginpage': // {
		$p=Page::getInstanceByType(3);
		if(!$p)$url='/';
		else $url=$p->getRelativeUrl().'#Login';
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
}
redirect($url);
