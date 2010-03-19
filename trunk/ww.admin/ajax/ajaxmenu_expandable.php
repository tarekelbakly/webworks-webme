<?php
require('../../ww.incs/common.php');
require('../../common/menus.php');
require('../../common/kaejax.php');
require('../pages/pages.funcs.php');
function ajaxmenu_expandable_getChildren($parentid,$currentPage=0){
	global $USERDATA;
	$r=menu_getChildren($parentid,$currentPage,1);
	foreach($r as $k=>$v)$r[$k]['classes'].=allowedToEditPage($r[$k]['id'])?'':' cannotEdit';
	return array($parentid,$r);
}
kaejax_export('ajaxmenu_expandable_getChildren');
kaejax_handle_client_request();
kaejax_show_javascript();
echo file_get_contents('../j/ajaxmenu_expandable.js');
