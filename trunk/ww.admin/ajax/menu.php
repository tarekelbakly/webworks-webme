<?php
require '../../common.php';
require SCRIPTBASE.'ww.admin/admin_libs.php';
require SCRIPTBASE.'common/menus.php';
require SCRIPTBASE.'common/kaejax.php';
function ajaxmenu_getChildren($parentid,$currentpage=0){
	global $USERDATA,$PLUGINS;
	$r=array();
	switch(substr($parentid,0,3)){
		case 'am_':{ # admin menu
			switch($parentid){
				case 'am_top':{ # top level menu
					$r=$GLOBALS['admin_top_menu'];
					$top=array();
					foreach($PLUGINS as $name=>$vals){
						if(isset($vals['admin']['menu'])){
							$v=$vals['admin']['menu'];
							if($v['top']=='top'){
								$r[]=array('id'=>'am_'.strtolower($v['name']),'name'=>_($v['name']),'link'=>'plugin.php?name='.$v['name']);
							}
							else $top[$v['top']]=true;
						}
					}
					foreach($top as $name=>$v){
						$r[]=array('id'=>'am_'.strtolower($name),'name'=>_($name),'link'=>'javascript:;','numchildren'=>1);
					}
					$cancel_plugins=true;
					break;
				}
				case 'am_misc':{
					if(has_access_permissions(ACL_MATRICES))$r[]=array('id'=>'am_tables','name'=>_('databases'),'link'=>'databases.php');
					if(has_access_permissions(ACL_PANELS))$r[]=array('id'=>'am_panels','name'=>_('panels'),'link'=>'panels.php');
					if(has_access_permissions(ACL_ADS))$r[]=array('id'=>'am_ads','name'=>_('ads'),'link'=>'ads.php');
					$r[]=array('id'=>'am_bookings','name'=>_('bookings'),'link'=>'bookings.php');
					break;
				}
				case 'am_products':{
					if(has_access_permissions(ACL_PRODUCTS))$r[]=array('id'=>'am_products_orders','name'=>_('orders'),'link'=>'products.php?action=viewOrders&filter=1');
					if(has_access_permissions(ACL_PRODUCTS))$r[]=array('id'=>'am_products_products','name'=>_('products'),'link'=>'products.php?action=showProducts');
					if(has_access_permissions(ACL_PRODUCTS))$r[]=array('id'=>'am_products_categories','name'=>_('categories'),'link'=>'products.php?action=editCategory');
					if(has_access_permissions(ACL_PRODUCTS))$r[]=array('id'=>'am_products_stock_control','name'=>_('stock control'),'link'=>'products.php?action=editStock');
					if(has_access_permissions(ACL_PRODUCTS))$r[]=array('id'=>'am_products_checkouts','name'=>_('checkouts'),'link'=>'products.php?action=editCheckout');
					if(has_access_permissions(ACL_PRODUCTS))$r[]=array('id'=>'am_products_templates','name'=>_('templates'),'link'=>'products.php?action=editType');
					break;
				}
				case 'am_users_and_admins':{
					if(has_access_permissions(ACL_USERS))$r[]=array('id'=>'am_user_accounts','name'=>_('user accounts'),'link'=>'users-accounts.php');
					if(has_access_permissions(ACL_USERS))$r[]=array('id'=>'am_user_groups','name'=>_('user groups'),'link'=>'users-groups.php');
					if(has_access_permissions(ACL_USERS))$r[]=array('id'=>'am_admins','name'=>_('admins'),'link'=>'users-admins.php');
					break;
				}
				case 'am_communication':{
					if(has_access_permissions(ACL_FORMS))$r[]=array('id'=>'am_forms','name'=>_('forms'),'link'=>'forms.php');
					if(has_access_permissions(ACL_SMS))$r[]=array('id'=>'am_sms','name'=>_('sms messaging'),'link'=>'sms.php');
					if(has_access_permissions(ACL_EZINES))$r[]=array('id'=>'am_ezine','name'=>_('ezines/newsletters'),'link'=>'ezines.php');
					$r[]=array('id'=>'am_polls','name'=>_('polls'),'link'=>'polls.php');
					break;
				}
				default:// {
					$parent=substr($parentid,3);
					foreach($PLUGINS as $name=>$vals){
						if(isset($vals['admin']['menu'])){
							$v=$vals['admin']['menu'];
							if(strtolower($v['top'])==$parent){
								$r[]=array('id'=>'am_'.$name,'name'=>_($vals['name']),'link'=>'plugin.php?_plugin='.$name);
							}
						}
					}
				// }
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
