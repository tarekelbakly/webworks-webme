<?php
// { common variables and functions
include_once('common.php');
if(isset($https_required) && $https_required && !$_SERVER['HTTPS']){
	$server=str_replace('www.','',$_SERVER['HTTP_HOST']);
	if($server=='inkjet.ie')header('Location: https://'.$server.'/');
	else header('Location: https://www.'.$server.'/');
	exit;
}
if(!isset($DBVARS['version']) || $DBVARS['version']<18)redirect('upgrades/upgrade.php');
$id=getVar('pageid',0);
$plugins_to_load=array(); // to be used by javascript
if(is_admin())$plugins_to_load[]='"frontend_admin":1';
$page=getVar('page');
// }
// { specials
if(isset($_GET['wwSpecial'])){
	switch($_GET['wwSpecial']){
		case 'productsearch':{
			if(isset($_POST['producttype'])&&$_POST['producttype']){
				$r=ProductType::getInstanceByName($_POST['producttype']);
				if($r){
					$r2=dbRow("select page_id from page_vars where name='product_type' and value='".$r->id."'");
					if(count($r2))$id=$r2['page_id'];
				}
			}
			else{
				$r2=dbRow("select page_id from page_vars where name='product_type' and value='0'");
				if(count($r2))$id=$r2['page_id'];
			}
			if(!$id){
				$r=Page::getInstanceByType(8);
				if($r)$id=$r->id;
			}
			break;
		}
		default:{
			echo 'unknown wwSpecial';
			exit;
		}
	}
}
else if($page=='' && isset($_GET['search'])){
	$p=Page::getInstanceByType(5);
	if(!$p || !isset($p->id)){
		dbQuery('insert into pages set cdate=now(),edate=now(),name="__search",body="",type=5,special=2,ord=5000');
		$p=Page::getInstanceByType(5);
	}
	$id=$p->id;
}
// }
// { get current page id
if(!$id){
	if($page){
		if(ereg('&',$page))$page=preg_replace('/&.*/','',$page);
		$r=Page::getInstanceByName($page);
		if($r)$id=$r->id;
	}
	if(!$id){
		$special=1;
		if(isset($_GET['special'])&&$_GET['special'])$special=$_GET['special'];
		if(!$page){
			$r=Page::getInstanceBySpecial($special);
			if($r && isset($r->id))$id=$r->id;
		}
	}
}
// }
// { load page data
if($id){
    $PAGEDATA=Page::getInstance($id)->initValues();
}
else{
	echo 'no page loaded. If this is a new site, then please <a href="/ww.admin/">log into the admin area</a> and create your first page.';
	exit;
}
// }
// { main content
$c='';
$permissions=array();
$p=cache_load('pages','permissions_'.$PAGEDATA->id);
if($p===false){
	$p=dbRow("select value from permissions where type=1 and id='".$PAGEDATA->id."'");
	cache_save('pages','permissions_'.$PAGEDATA->id,$p);
}
if(count($p)){
	$allowed=0;
	$lines=explode("\n",$p['value']);
	if($lines[2]&4)$allowed=1;
	else{ # usergroups
		$g=explode(',',$lines[1]);
		foreach($g as $p){
			$p=explode('=',$p);
			if(isset($USERGROUPS[$p[0]]) && $p[1]&4)$allowed=1;
		}
	}
}else $allowed=1;
if(!$allowed){
	$c.='<h2>Permission Denied</h2><p>This is a protected document. To view it, you must first log in.</p>';
	$p=Page::getInstanceByType(3);
	if($p)$c.='<p>Click <a href="'.$p->getRelativeURL().'">here</a> to log in.</p>';
}
else if(getVar('webmespecial')=='sitemap')$c.=sitemap('');
else{
	if($PAGEDATA->htmlheader!='')$c.=$PAGEDATA->htmlheader;
	switch($PAGEDATA->type){
		case 0: // { normal page
			$c.=webmeParse($PAGEDATA->body);
			break;
		// }
		case 2: // { events
			$c.='<div id="events_'.$PAGEDATA->id.'" class="events">please wait - loading...</div>';
			$plugins_to_load[]='"eventcalendar":1';
			break;
		// }
		case 3: // { user login/registration
			include_once(SCRIPTBASE.'common/user.login.and.registration.php');
			$c.=userloginandregistrationDisplay();
			break;
		// }
		case 4: // { sub-page summaries
			include_once('common/page.summaries.php');
			$c.=displayPageSummaries($PAGEDATA->id);
			break;
		// }
		case 5: // { search results
			$c.=webmeParse($PAGEDATA->body.showSearchResults());
			break;
		// }
		case 7: // { news
			$c.=showNews($PAGEDATA->id);
			break;
		// }
		case 8: // { product listing
			$c.=webmeParse(showProductListing($PAGEDATA->id));
			break;
		// }
		case 9: // { table of contents
			$kids=Pages::getInstancesByParent($PAGEDATA->id);
			$c.=webmeParse($PAGEDATA->body);
			if(!count($kids->pages))$c.='<em>no sub-pages</em>';
			else{
				$c.='<ul class="subpages">';
				foreach($kids->pages as $kid){
					$c.='<li><a href="'.$kid->getRelativeURL().'">'.htmlspecialchars($kid->name).'</a></li>';
				}
				$c.='</ul>';
			}
			break;
		// }
		case 10: // { online store checkout
			require_once 'common/online_stores.php';
			$c.=webmeParse($PAGEDATA->body);
			$c.=osCheckoutDisplay();
			break;
		// }
		default: { # unknown
			$c.='<em>this page type ('.$PAGEDATA->type.') not handled yet</em>'.webmeParse($PAGEDATA->body);
			break;
		}
	}
	if($c==''&&!$id)$c=show404(str_replace('/',' ',$_SERVER['REQUEST_URI']));
}
if($PAGEDATA->special&64)$c.='<div id="webmeComments"></div>';
// { show any error messages that turned up
if(isset($_SESSION['msgs_errors'])){
	$c.='<script type="text/javascript">alert('.json_encode($_SESSION['msgs_errors']).'.join("\n"));</script>';
	unset($_SESSION['msgs_errors']);
}
// }
$pagecontent=$c;
// }
// { load page template
if(file_exists(THEME_DIR.'/'.THEME.'/h/'.$PAGEDATA->template.'.html')){
	$template=THEME_DIR.'/'.THEME.'/h/'.$PAGEDATA->template.'.html';
}
else{
	$ex='ls '.THEME_DIR.'/'.THEME.'/h/*html';
	$d=`$ex`;
	$d=explode("\n",$d);
	$template=$d[0];
}
if($template=='')die('no template created. please create a template first');
// }
require SCRIPTBASE . 'common/templates.php';

ob_start();
show_page($template,$pagecontent,$PAGEDATA);
ob_show_and_log('page');
