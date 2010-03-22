<?php
// { common variables and functions
include_once('ww.incs/common.php');
if(isset($https_required) && $https_required && !$_SERVER['HTTPS']){
	$server=str_replace('www.','',$_SERVER['HTTP_HOST']);
	header('Location: https://www.'.$server.'/');
	exit;
}
if(!isset($DBVARS['version']) || $DBVARS['version']<27)redirect('upgrades/upgrade.php');
$id=getVar('pageid',0);
$plugins_to_load=array(); // to be used by javascript
$page=getVar('page');
// }
// { specials
if($page=='' && isset($_GET['search'])){
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
		if($r && isset($r->id))$id=$r->id;
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
	if($page!='')redirect('/');
	echo 'no page loaded. If this is a new site, then please <a href="/ww.admin/">log into the admin area</a> and create your first page.';
	exit;
}
// }
// { main content
$c='';
// { check if page is protected
$allowed=1;
foreach($PLUGINS as $p){
	if(!$allowed)continue;
	if(isset($p['frontend']['page_display_test'])){
		$allowed=$p['frontend']['page_display_test']($PAGEDATA);
	}
}
// }
if(!$allowed){
	$c.='<h2>Permission Denied</h2><p>This is a protected document.</p>';
	if(isset($_SESSION['userdata'])){
		$c.='<p>You are not in a user-group which has access to this page. If you think you should be, please contact the site administrator.</p>';
	}
	else $c.='<p>Click <a href="/common/redirector.php?type=loginpage">here</a> to log in.</p>';
}
else if(getVar('webmespecial')=='sitemap')$c.=sitemap('');
else{
	switch($PAGEDATA->type){
		case '0': // { normal page
			$c.=webmeParse($PAGEDATA->body);
			break;
		// }
		case '2': // { events
			$c.='<div id="events_'.$PAGEDATA->id.'" class="events">please wait - loading...</div>';
			$plugins_to_load[]='"eventcalendar":1';
			break;
		// }
		case '3': // { user login/registration
			include_once(SCRIPTBASE.'common/user.login.and.registration.php');
			$c.=userloginandregistrationDisplay();
			break;
		// }
		case '4': // { sub-page summaries
			include_once('common/page.summaries.php');
			$c.=displayPageSummaries($PAGEDATA->id);
			break;
		// }
		case '5': // { search results
			$c.=webmeParse($PAGEDATA->body.showSearchResults());
			break;
		// }
		case '7': // { news
			$c.=showNews($PAGEDATA->id);
			break;
		// }
		case '8': // { product listing
			$c.=webmeParse(showProductListing($PAGEDATA->id));
			break;
		// }
		case '9': // { table of contents
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
		case '10': // { online store checkout
			require_once 'common/online_stores.php';
			$c.=webmeParse($PAGEDATA->body);
			$c.=osCheckoutDisplay();
			break;
		// }
		case '11': // { bookings
			require 'common/bookings.php';
			$c.=webmeParse($PAGEDATA->body);
			$c.=bookings_show();
			break;
		// }
		default: // { plugins, and unknown
			$not_found=true;
			if(isset($PLUGINS[$PAGEDATA->type])){
				$p=$PLUGINS[$PAGEDATA->type];
				if(isset($p['frontend']['page_type']) && function_exists($p['frontend']['page_type'])){
					$c.=$p['frontend']['page_type']($PAGEDATA);
					$not_found=false;
				}
			}
			if($not_found)$c.='<em>No plugin found to handle page type <strong>'.htmlspecialchars($PAGEDATA->type).'</strong>. Is the plugin installed and enabled?</em>';
			break;
		// }
	}
	if($c==''&&!$id)$c=show404(str_replace('/',' ',$_SERVER['REQUEST_URI']));
}
if($PAGEDATA->special&64)$c.='<div id="webmeComments"></div>';
$pagecontent=$c;
// }
// { load page template
if(file_exists(THEME_DIR.'/'.THEME.'/h/'.$PAGEDATA->template.'.html')){
	$template=THEME_DIR.'/'.THEME.'/h/'.$PAGEDATA->template.'.html';
}
else{
	$d=array();
	$dir=new DirectoryIterator(THEME_DIR.'/'.THEME.'/h/');
	foreach($dir as $f){
		if($f->isDot())continue;
		$n=$f->getFilename();
		if(strpos($n,'.')===0)continue;
		if(preg_match('/\.html$/',$n))$d[]=preg_replace('/\.html$/','',$n);
	}
	asort($d);
	$template=$d[0];
}
if($template=='')die('no template created. please create a template first');
// }
require SCRIPTBASE . 'common/templates.php';

ob_start();
show_page($template,$pagecontent,$PAGEDATA);
ob_show_and_log('page');
