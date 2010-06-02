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
if($page=='' && isset($_GET['search']) || isset($_GET['s'])){
	if(isset($_GET['s']))$_GET['search']=$_GET['s'];
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
#echo "check if page is set ".(microtime(true)-START_TIME).'<br />';
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
			$c.=$PAGEDATA->render();
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
			$c.=$PAGEDATA->render().showSearchResults();
			break;
		// }
		case '9': // { table of contents
			$kids=Pages::getInstancesByParent($PAGEDATA->id);
			$c.=$PAGEDATA->render();
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
else if(file_exists(THEME_DIR.'/'.THEME.'/h/_default.html')){
	$template=THEME_DIR.'/'.THEME.'/h/_default.html';
}
else{
	$d=array();
	$dir=new DirectoryIterator(THEME_DIR.'/'.THEME.'/h/');
	foreach($dir as $f){
		if($f->isDot())continue;
		$n=$f->getFilename();
		if(preg_match('/\.html$/',$n))$d[]=preg_replace('/\.html$/','',$n);
	}
	asort($d);
	$template=$d[0];
}
if($template=='')die('no template created. please create a template first');
// }
function template_get_metadata($template,$PAGEDATA){
	global $DBVARS;
	// { page title
	$title=($PAGEDATA->title!='')?$PAGEDATA->title:str_replace('www.','',$_SERVER['HTTP_HOST']).' > '.$PAGEDATA->name;
	$c='<title>'.htmlspecialchars($title).'</title>';
	// }
	// { generate plugins list for those that were not already figured out (this should be optimised as soon as possible)
	if(strpos($template,'class="tabs"')!==false)$GLOBALS['plugins_to_load'][]='"tabs":1';
	if(strpos($template,'class="showhide"')!==false)$GLOBALS['plugins_to_load'][]='"showhide":1';
	if(strpos($template,'class="fontsize_controls"')!==false)$GLOBALS['plugins_to_load'][]='"fontsize_controls":1';
	if(strpos($template,'class="imagefader"')!==false)$GLOBALS['plugins_to_load'][]='"imagefader":1';
	if(strpos($template,'class="inputdate"')!==false)$GLOBALS['plugins_to_load'][]='"inputdate":1';
	if(strpos($template,'class="removeRowIfEmpty"')!==false)$GLOBALS['plugins_to_load'][]='"removeRowIfEmpty":1';
	if(strpos($template,'class="sc_ssearch"')!==false)$GLOBALS['plugins_to_load'][]='"sc_ssearch":1';
	if(strpos($template,'class="scrollingEvents"')!==false)$GLOBALS['plugins_to_load'][]='"scrollingEvents":1';
	if(strpos($template,'class="scrollingNews"')!==false)$GLOBALS['plugins_to_load'][]='"scrollingNews":1';
	// }
	// { show stylesheet and javascript links
	if($DBVARS['theme_variant'])$c.='<link rel="stylesheet" href="/css/'.$DBVARS['theme'].'/'.$DBVARS['theme_variant'].'" type="text/css" />';
	else $c.='<link rel="stylesheet" href="/css" type="text/css" />';
	$c.='<style type="text/css">.loggedin{display:'.(is_logged_in()?'block':'none').'} .loggedinCell{display:'.(is_logged_in()?'table-cell':'none').'}</style>';
	$c.='<script type="text/javascript" src="/j/jquery-ui/js/jquery-1.4.2.min.js"></script><script src="/j/jquery-ui/js/jquery-ui-1.8.1.custom.min.js"></script>';
	$c.='<link rel="stylesheet" href="/j/jquery-ui/css/smoothness/jquery-ui-1.8.1.custom.css" />';
	$c.='<script type="text/javascript" src="/js/'.filemtime(SCRIPTBASE.'j/js.js').'"></script>';
	$c.='<script type="text/javascript">var pagedata={id:'.$PAGEDATA->id.',url:"'.$PAGEDATA->getRelativeURL().'",country:"'.(isset($_SESSION['os_country'])?$_SESSION['os_country']:'').'"},';
	$c.='userdata={isAdmin:'.(is_admin()?1:0);
	if(isset($_SESSION['userdata']) && isset($_SESSION['userdata']['discount']))$c.=',discount:'.(int)$_SESSION['userdata']['discount'];
	$c.='},';
	$c.='plugins_to_load={'.join(',',$GLOBALS['plugins_to_load']).'};';
	$c.='document.write("<"+"style type=\'text/css\'>a.nojs{display:none !important}<"+"/style>");';
	$c.='</script>';
	if(is_admin()){
		$c.='<script src="/ww.admin/j/admin-frontend.js"></script><link rel="stylesheet" href="/ww.admin/theme/admin-frontend.css" />';
	}
	// }
	// { meta tags
	$c.='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	if($PAGEDATA->keywords)$c.='<meta http-equiv="keywords" content="'.$PAGEDATA->keywords.'" />';
	if($PAGEDATA->description)$c.='<meta http-equiv="description" content="'.$PAGEDATA->description.'" />';
	if(isset($PAGEDATA->vars['google-site-verification']))$c.='<meta name="google-site-verification" content="'.htmlspecialchars($PAGEDATA->vars['google-site-verification']).'" />';
	// }
	return $c;
}
function logoDisplay($vars){
	$vars=array_merge(array('width'=>64,'height'=>64),$vars);
	if(!file_exists(USERBASE.'/f/skin_files/logo.png'))return '';
	$x=(int)$vars['width'];
	$y=(int)$vars['height'];
	$geometry=$x.'x'.$y;
	$image_file=USERBASE.'/f/skin_files/logo-'.$geometry.'.png';
	if(!file_exists($image_file)){
		$from=addslashes(USERBASE.'/f/skin_files/logo.png');
		$to=addslashes($image_file);
		`convert $from -geometry $geometry $to`;
	}
	return '<img src="/f/skin_files/logo-'.$geometry.'.png" />';
}
function show_page_breadcrumbs($id=0) {
	if($id)$page=Page::getInstance($id);
	else $page=$GLOBALS['PAGEDATA'];
	$c=$page->parent ? show_page_breadcrumbs($page->parent) . ' &raquo; ' : '';
	return $c . '<a href="' . $page->getRelativeURL() . '" title="' . htmlspecialchars($page->title) . '">' . htmlspecialchars($page->name) . '</a>';
}

$smarty=smarty_setup();
$smarty->compile_dir=USERBASE . '/ww.cache/pages';
$smarty->template_dir=THEME_DIR.'/'.THEME.'/h/';
// { some straight replaces
$smarty->assign('PAGECONTENT',$pagecontent);
$smarty->assign('PAGEDATA',$PAGEDATA);
$smarty->assign('METADATA',template_get_metadata($template,$PAGEDATA));
// { display the document
ob_start();
if(strpos($template,'/')===false)$template=THEME_DIR.'/'.THEME.'/h/'.$template.'.html';
$smarty->display($template);
ob_show_and_log('page','Content-type: text/html; Charset=utf-8');
// }
