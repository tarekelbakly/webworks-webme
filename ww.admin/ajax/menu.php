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
					$r[]=array('id'=>'am_pages','name'=>_('pages'),'link'=>'pages.php');
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
					$r[]=array('id'=>'am_siteoptions','name'=>_('site options'),'link'=>'siteoptions.php');
					$r[]=array('id'=>'am_stats','name'=>_('stats'),'link'=>'stats.php');
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
