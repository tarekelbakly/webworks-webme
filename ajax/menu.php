<?php
require_once('../common.php');
require_once('../common/menus.php');
require_once('../common/kaejax.php');
function ajaxmenu_getChildren($parentid,$currentpage=0,$topParent=0,$search_options=0){
	return array($parentid,menu_getChildren($parentid,$currentpage,0,$topParent,$search_options));
}
kaejax_export('ajaxmenu_getChildren');
kaejax_handle_client_request();
kaejax_show_javascript();
echo 'var menu_cache=['.json_encode(ajaxmenu_getChildren(0,$_GET['pageid'],0,$_REQUEST['search_options'])).'];';
$p=Page::getInstance($_GET['pageid']);
$pid=$p->getTopParentId();
if($pid!=2 && $pid!=3 && $pid!=17 && $pid!=32 && $pid!=33 && $pid!=34)$pid=2;
echo 'var currentTop='.$pid.';';
echo file_get_contents('menu.js');
