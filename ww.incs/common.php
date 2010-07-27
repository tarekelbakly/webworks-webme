<?php
require_once dirname(__FILE__).'/basics.php';
require_once SCRIPTBASE . 'ww.incs/Smarty-2.6.26/libs/Smarty.class.php';
function date_m2h($d, $type = 'date') {
	$date = preg_replace('/[- :]/', ' ', $d);
	$date = explode(' ', $date);
	if ($type == 'date') return @date('l jS F, Y', mktime(0, 0, 0, $date[1], $date[2], $date[0]));
	if ($type == 'shortdate') return @date('D jS M, Y', mktime(0, 0, 0, $date[1], $date[2], $date[0]));
	return @date(DATE_RFC822, mktime($date[5], $date[4], $date[3], $date[1], $date[2], $date[0]));
}
function getVar($v, $d = '') {
	if (isset($_GLOBAL[$v])) return $_GLOBAL[$v];
	if (isset($_SESSION[$v])) return $_SESSION[$v];
	if (isset($_COOKIE[$v])) return $_COOKIE[$v];
	if (isset($_GET[$v])) return $_GET[$v];
	if (isset($_POST[$v])) return $_POST[$v];
	if (isset($_FILES[$v])) return $_FILES[$v];
	if (isset($_SESSION['userdata'][$v]) && $v != 'password') return $_SESSION['userdata'][$v];
	if (isset($_SESSION['forms'][$v])) return $_SESSION['forms'][$v];
	if ($v != strtolower($v)) return getVar(strtolower($v), $d);
	return $d;
}
function inc_common($f) {
	include_once SCRIPTBASE . 'common/' . $f;
}
function redirect($addr){
	header('Location: '.$addr);
	echo '<html><head><script type="text/javascript">setTimeout(function(){document.location="'.$addr.'";},10);</script></head><body><noscript>you need javascript to use this site</noscript></body></html>';
	exit;
}
function webmeMail($from, $to, $subject, $message, $files = false) {
	inc_common('mail.php');
	send_mail($from, $to, $subject, $message, $files);
}
$is_admin = 0;
$sitedomain=str_replace('www.','',$_SERVER['HTTP_HOST']);
if(strpos($_SERVER['REQUEST_URI'],'ww.admin/')!==false){
	require_once SCRIPTBASE . 'j/kfm/api/api.php';
	require_once SCRIPTBASE . 'j/kfm/initialise.php';
}
function eventCalendarDisplay($a=0){
	include_once SCRIPTBASE . 'common/funcs.events.php';
	return ww_eventCalendarDisplay($a);
}
function panelDisplay($a=0){
	include_once SCRIPTBASE . 'common/funcs.panels.php';
	return ww_panelDisplay($a);
}
function imageDisplay($a=0){
	include_once SCRIPTBASE . 'common/funcs.image.display.php';
	return func_image_display($a);
}
function menuDisplay($a=0){
	require_once SCRIPTBASE . 'common/menus.php';
	return menu_show($a);
}
function show404($a=0){
	include_once SCRIPTBASE . 'common/404.php';
	return ww_show404($a);
}
function showSearchResults($a=0){
	include_once SCRIPTBASE . 'common/funcs.search.php';
	return ww_showSearchResults($a);
}
function sitemap($a=0){
	include_once SCRIPTBASE . 'common/sitemap.php';
	return ww_showSitemap($a);
}
function smarty_setup(){
	global $DBVARS,$PLUGINS;
	$smarty = new Smarty;
	$smarty->left_delimiter = '{{';
	$smarty->right_delimiter = '}}';
	$smarty->assign('WEBSITE_TITLE',htmlspecialchars($DBVARS['site_title']));
	$smarty->assign('WEBSITE_SUBTITLE',htmlspecialchars($DBVARS['site_subtitle']));
	$smarty->register_function('BREADCRUMBS','WW_getPageBreadcrumbs');
	$smarty->register_function('LOGO', 'logoDisplay');
	$smarty->register_function('MENU', 'menuDisplay');
	$smarty->register_function('nuMENU', 'menu_show_fg');
	foreach($PLUGINS as $pname=>$plugin){
		if(isset($plugin['frontend']['template_functions'])){
			foreach($plugin['frontend']['template_functions'] as $fname=>$vals){
				$smarty->register_function($fname,$vals['function']);
			}
		}
	}
	return $smarty;
}
// { user authentication
if(isset($_REQUEST['action']) && $_REQUEST['action']==__('login')){
	// { variables
	$email=$_REQUEST['email'];
	$password=$_REQUEST['password'];
	// }
	$r=dbRow('select * from user_accounts where email="'.addslashes($email).'" and password=md5("'.$password.'")');
	if($r && count($r)){
		// { update session variables
		$r['password']=$password;
		$_SESSION['userdata']=$r;
		$r['password'] = $_SESSION['userdata']['password'];
		$_SESSION['userdata'] = $r;
		// }
		// { redirect if applicable
		$redirect_url='';
		if(isset($_POST['login_referer']) && strpos($_POST['login_referer'],'/')===0){
			$redirect_url=$_POST['login_referer'];
		}
		else if(isset($PAGEDATA) && $PAGEDATA->vars['userlogin_redirect_to']){
			$p=Page::getInstance($PAGEDATA->vars['userlogin_redirect_to']);
			$redirect_url=$p->getRelativeUrl();
		}
		if($redirect_url!='')redirect($redirect_url);
		// }
	}
}
if(isset($_SESSION['userdata']['id']) && !isset($_SESSION['userdata']['groups'])){
	// { groups
	$USERGROUPS = array();
	$rs = dbAll("select id,name from users_groups,groups where id=groups_id and user_accounts_id=" . $_SESSION['userdata']['id']);
	if($rs)foreach($rs as $r){
		$USERGROUPS[$r['name']] = $r['id'];
	}
	$_SESSION['userdata']['groups']=$USERGROUPS;
	// }
}
if(isset($_REQUEST['logout']))unset($_SESSION['userdata']);
// }
function menu_build_fg($parentid,$depth,$options){
	$PARENTDATA=Page::getInstance($parentid);
	$PARENTDATA->initValues();
	// { menu order
	$order='ord,name';
	if(isset($PARENTDATA->vars['order_of_sub_pages'])){
		switch($PARENTDATA->vars['order_of_sub_pages']){
			case 1: // { alphabetical
				$order='name';
				if($PARENTDATA->vars['order_of_sub_pages_dir'])$order.=' desc';
				break;
			// }
			case 2: // { associated_date
				$order='associated_date';
				if($PARENTDATA->vars['order_of_sub_pages_dir'])$order.=' desc';
				$order.=',name';
				break;
			// }
			default: // { by admin order
				$order='ord';
				if($PARENTDATA->vars['order_of_sub_pages_dir'])$order.=' desc';
				$order.=',name';
			// }
		}
	}
	// }
	$rs=dbAll("select id,name,type from pages where parent='".$parentid."' and !(special&2) order by $order");
	if($rs===false || !count($rs))return '';

	$items=array();
	foreach($rs as $r){
		$item='<li>';
		$page=Page::getInstance($r['id']);
		$item.='<a class="menu-fg" href="'.$page->getRelativeUrl().'">'.htmlspecialchars($page->name).'</a>';
		$item.=menu_build_fg($r['id'],$depth+1,$options);
		$item.='</li>';
		$items[]=$item;
	}
	$options['columns']=(int)$options['columns'];

	// return top-level menu
	if(!$depth)return '<ul>'.join('',$items).'</ul>';

	$s='';
	if($options['background'])$s.='background:'.$options['background'].';';
	if($options['opacity'])$s.='opacity:'.$options['opacity'].';';
	if($s){
		$s=' style="'.$s.'"';
	}

	// return 1-column sub-menu
	if($options['columns']<2)return '<ul'.$s.'>'.join('',$items).'</ul>';

	// return multi-column submenu
	$items_count=count($items);
	$items_per_column=ceil($items_count/$options['columns']);
	$c='<table'.$s.'><tr><td><ul>';
	for($i=1;$i<$items_count+1;++$i){
		$c.=$items[$i-1];
		if($i!=$items_count && !($i%$items_per_column))$c.='</ul></td><td><ul>';
	}
	$c.='</ul></td></tr></table>';
	return $c;
}
function menu_show_fg($opts){
	$md5=md5('menu_fg|'.print_r($opts,true));
	$cache=cache_load('menus',$md5);
	if($cache)return $cache;

	$options=array(
		'direction' => 0,  // 0: horizontal, 1: vertical
		'parent'    => 0,  // top-level
		'background'=> '', // sub-menu background colour
		'columns'   => 1,  // for wide drop-down sub-menus
		'opacity'   => 0   // opacity of the sub-menu
	);
	foreach($opts as $k=>$v){
		if(isset($options[$k]))$options[$k]=$v;
	}
	if(!is_numeric($options['parent'])){
		$r=Page::getInstanceByName($options['parent']);
		if($r)$options['parent']=$r->id;
	}
	if(is_numeric($options['direction'])){
		if($options['direction']=='0')$options['direction']='horizontal';
		else $options['direction']='vertical';
	}
	WW_addScript('/j/fg.menu/fg.menu.js');
	WW_addCSS('/j/fg.menu/fg.menu.css');
	$items=array();
	$menuid=$GLOBALS['fg_menus']++;
	$md5=md5($options['parent'].'|0|'.json_encode($options));
	$html=cache_load('pages','fgmenu-'.$md5);
	if($html===false){
		$html=menu_build_fg($options['parent'],0,$options);
		cache_save('pages','fgmenu-'.$md5,$html);
	}
	$c.='<div class="menu-fg menu-fg-'.$options['direction'].'" id="menu-fg-'.$menuid.'">'.$html.'</div>';
	if($options['direction']=='vertical'){
		$posopts="positionOpts: { posX: 'left', posY: 'top',
			offsetX: 40, offsetY: 10, directionH: 'right', directionV: 'down',
			detectH: true, detectV: true, linkToFront: false },";
	}
	else{
		$posopts='';
	}
	$c.="<script>
jQuery.fn.outer = function() {
  return $( $('<div></div>').html(this.clone()) ).html();

}
$(function(){
	$('#menu-fg-$menuid>ul>li>a').each(function(){
		if(!$(this).next().length)return; // empty
		$(this).menu({
			content:$(this).next().outer(),
			choose:function(ev,ui){
				document.location=ui.item[0].childNodes(0).href;
			},
			$posopts
			flyOut:true
		});
	});
	$('.menu-fg>ul>li').addClass('fg-menu-top-level');
});
</script>";
	return $c;
	cache_save('menus',$md5,$c);
	return $c;
}
$fg_menus=0;
