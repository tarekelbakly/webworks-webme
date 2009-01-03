<?php
require '../../common.php';
require BASEDIR.'ww.admin/admin_libs.php';
require BASEDIR.'common/menus.php';
require BASEDIR.'common/kaejax.php';
function ajaxmenu_getChildren($parentid,$currentpage=0){
	global $USERDATA;
	$USERDATA=array('accesspermissions'=>65535);
	$r=array();
	switch(substr($parentid,0,3)){
		case 'am_':{ # admin menu
			switch($parentid){
				case 'am_top':{ # top level menu
					if($USERDATA['accesspermissions']&ACL_PAGES)$r[]=array('id'=>'am_pages','name'=>_('pages'),'link'=>'pages.php');
					if($USERDATA['accesspermissions']&(ACL_PANELS+ACL_FORMS+ACL_MATRICES))$r[]=array('id'=>'am_misc','name'=>_('misc.'),'link'=>'#','numchildren'=>1);
					if($USERDATA['accesspermissions']&ACL_PRODUCTS)$r[]=array('id'=>'am_products','name'=>_('products'),'link'=>'#','numchildren'=>1);
					if($USERDATA['accesspermissions']&(ACL_SMS+ACL_EZINES+ACL_FORMS))$r[]=array('id'=>'am_communication','name'=>_('communication'),'link'=>'#','numchildren'=>1);
					if($USERDATA['accesspermissions']&ACL_USERS)$r[]=array('id'=>'am_users_and_admins','name'=>_('users and admins'),'link'=>'#','numchildren'=>1);
					break;
				}
				case 'am_misc':{
					if($USERDATA['accesspermissions']&ACL_MATRICES)$r[]=array('id'=>'am_tables','name'=>_('databases'),'link'=>'databases.php');
					if($USERDATA['accesspermissions']&ACL_PANELS)$r[]=array('id'=>'am_panels','name'=>_('panels'),'link'=>'panels.php');
					if($USERDATA['accesspermissions']&ACL_ADS)$r[]=array('id'=>'am_ads','name'=>_('ads'),'link'=>'ads.php');
					break;
				}
				case 'am_products':{
					if($USERDATA['accesspermissions']&ACL_PRODUCTS)$r[]=array('id'=>'am_products_orders','name'=>_('orders'),'link'=>'products.php?action=viewOrders&filter=1');
					if($USERDATA['accesspermissions']&ACL_PRODUCTS)$r[]=array('id'=>'am_products_products','name'=>_('products'),'link'=>'products.php?action=showProducts');
					if($USERDATA['accesspermissions']&ACL_PRODUCTS)$r[]=array('id'=>'am_products_categories','name'=>_('categories'),'link'=>'products.php?action=editCategory');
					if($USERDATA['accesspermissions']&ACL_PRODUCTS)$r[]=array('id'=>'am_products_stock_control','name'=>_('stock control'),'link'=>'products.php?action=editStock');
					if($USERDATA['accesspermissions']&ACL_PRODUCTS)$r[]=array('id'=>'am_products_checkouts','name'=>_('checkouts'),'link'=>'products.php?action=editCheckout');
					if($USERDATA['accesspermissions']&ACL_PRODUCTS)$r[]=array('id'=>'am_products_templates','name'=>_('templates'),'link'=>'products.php?action=editType');
					break;
				}
				case 'am_users_and_admins':{
					if($USERDATA['accesspermissions']&ACL_USERS)$r[]=array('id'=>'am_user_accounts','name'=>_('user accounts'),'link'=>'users-accounts.php');
					if($USERDATA['accesspermissions']&ACL_USERS)$r[]=array('id'=>'am_user_groups','name'=>_('user groups'),'link'=>'users-groups.php');
					if($USERDATA['accesspermissions']&ACL_USERS)$r[]=array('id'=>'am_admins','name'=>_('admins'),'link'=>'users-admins.php');
					$r[]=array('id'=>'am_siteoptions','name'=>_('site options'),'link'=>'siteoptions.php');
					break;
				}
				case 'am_communication':{
					if($USERDATA['accesspermissions']&ACL_FORMS)$r[]=array('id'=>'am_forms','name'=>_('forms'),'link'=>'forms.php');
					if($USERDATA['accesspermissions']&ACL_SMS)$r[]=array('id'=>'am_sms','name'=>_('sms messaging'),'link'=>'sms.php');
					if($USERDATA['accesspermissions']&ACL_EZINES)$r[]=array('id'=>'am_ezine','name'=>_('ezines/newsletters'),'link'=>'ezines.php');
				}
			}
		}
	}
	return array($parentid,$r);
}
kaejax_export('ajaxmenu_getChildren');
kaejax_handle_client_request();
kaejax_show_javascript();
echo 'var menu_cache=['.json_encode(ajaxmenu_getChildren('am_top')).'];';
echo file_get_contents('../../ajax/menu.js');
