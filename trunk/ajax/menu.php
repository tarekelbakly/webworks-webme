<?php
require '../ww.incs/basics.php';
require '../ww.incs/db.php';
require '../common/menus.php';
require '../common/kaejax.php';
function ajaxmenu_getChildren($parentid,$currentpage=0,$topParent=0,$search_options=0){
	return array($parentid,menu_getChildren($parentid,$currentpage,0,$topParent,$search_options));
}
kaejax_export('ajaxmenu_getChildren');
kaejax_handle_client_request();
kaejax_show_javascript();

$search_options=isset($_REQUEST['search_options'])?$_REQUEST['search_options']:0;
if(!isset($_GET['pageid']))exit;
$md5=md5($_GET['pageid'].'|'.$search_options);
$cache=cache_load('menus',$md5);

ob_start();
if($cache)echo $cache;
else{
	$d='var menu_cache=['.json_encode(ajaxmenu_getChildren(0,$_GET['pageid'],0,$search_options)).'];';
	$p=Page::getInstance($_GET['pageid']);
	$pid=$p->getTopParentId();
	$d.='var currentTop='.$pid.';';
	cache_save('menus',$md5,$d);
	echo $d;
}
echo file_get_contents('menu.js');
ob_show_and_log('menu');
