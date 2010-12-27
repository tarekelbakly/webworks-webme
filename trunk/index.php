<?php
/**
	* front controller for WebME files
	*
	* PHP version 5
	*
	* @category None
	* @package  None
	* @author   Kae Verens <kae@webworks.ie>
	* @license  GPL 2.0
	* @link     http://webworks.ie/
	*/

$ww_startup = microtime(true);
// { common variables and functions
$scripts=array();
$css_urls=array();
$scripts_inline=array();
function WW_addCSS($url){
	global $css_urls;
	if(in_array($url,$css_urls))return;
	$css_urls[]=$url;
}
function WW_getCSS(){
	global $css_urls;
	$url='/css/';
	foreach($css_urls as $s)$url.='|'.$s;
	return '<link rel="stylesheet" type="text/css" href="'.htmlspecialchars($url).'" />';
}
function WW_addScript($url){
	global $scripts;
	if(in_array($url,$scripts))return;
	$scripts[]=$url;
}
function WW_addInlineScript($script){
	global $scripts_inline;
	$script=preg_replace('/\s+/',' ',str_replace(array("\n","\r"),' ',$script));
	if(in_array($script,$scripts_inline))return;
	$scripts_inline[]=$script;
}
function WW_getScripts(){
	global $scripts;
	$url='/js/'.filemtime(SCRIPTBASE.'j/js.js');
	foreach ($scripts as $s) {
		$url.='|'.$s;
	}
	return $url;
}
function WW_getInlineScripts(){
	global $scripts_inline;
	if (!count($scripts_inline)) {
		return '';
	}
	return '<script>'.join('',$scripts_inline).'</script>';
}
require_once 'ww.incs/common.php';
if (isset($https_required) && $https_required && !$_SERVER['HTTPS']) {
	$server=str_replace('www.', '', $_SERVER['HTTP_HOST']);
	redirect('https://www.'.$server.'/');
}
if (!isset($DBVARS['version']) || $DBVARS['version']<31) {
	redirect('/ww.incs/upgrade.php');
}
$id=getVar('pageid', 0);
$page=getVar('page');
// }
// { is this a search?
if ($page=='' && isset($_GET['search']) || isset($_GET['s'])) {
	require_once 'ww.incs/search.php';
	$p=Search_getPage();
	$id=$p->id;
}
// }
// { get current page id
if (!$id) {
	if ($page) {
		if (strpos($page, '&')!==false) {
			$page=preg_replace('/&.*/', '', $page);
		}
		$r=Page::getInstanceByName($page);
		if ($r && isset($r->id)) {
			$id=$r->id;
		}
	}
	if (!$id) {
		$special=1;
		if (isset($_GET['special'])&&$_GET['special']) {
			$special=$_GET['special'];
		}
		if (!$page) {
			$r=Page::getInstanceBySpecial($special);
			if ($r && isset($r->id)) {
				$id=$r->id;
			}
		}
	}
}
// }
// { load page data
if ($id) {
    $PAGEDATA=Page::getInstance($id)->initValues();
}
else{
	if ($page!='') {
		redirect('/');
	}
	echo 'no page loaded. If this is a new site, then please '
		.'<a href="/ww.admin/">log into the admin area</a> and create '
		.'your first page.';
	exit;
}
$c=plugin_trigger('page-object-loaded');
// }
// { main content
// { check if page is protected
$allowed=1;
foreach ($PLUGINS as $p) {
	if (!$allowed) {
		break;
	}
	if (isset($p['frontend']['page_display_test'])) {
		$allowed=$p['frontend']['page_display_test']($PAGEDATA);
	}
}
// }
if (!$allowed) {
	$c.='<h2>Permission Denied</h2><p>This is a protected document.</p>';
	if (isset($_SESSION['userdata'])) {
		$c.='<p>You are not in a user-group which has access to this page. '
			.'If you think you should be, please contact the site administrator.</p>';
	}
	else {
		$c.='<p><strong>If you have a user account, please <a href="/_r?type=loginpage">click here</a> to log in.</strong></p>';
	}
	$c.='<p>If you do not have a user account, but have been supplied with a password for the page, please enter it here and submit the form:</p>'
		.'<form method="post"><input type="password" name="privacy_password" /><input type="submit" /></form>';
}
else if (getVar('webmespecial')=='sitemap') {
	$c.=sitemap('');
}
else {
	switch($PAGEDATA->type){
		case '0': // { normal page
			$c.=$PAGEDATA->render();
		break;
		// }
		case '4': // { sub-page summaries
			require_once 'ww.incs/page.summaries.php';
			$c.=displayPageSummaries($PAGEDATA->id);
		break; // }
		case '5': // { search results
			require_once 'ww.incs/search.php';
			$c.=$PAGEDATA->render().Search_showResults();
		break; // }
		case '9': // { table of contents
			require 'ww.incs/tableofcontents.php';
			$c.=TableOfContents_getContent($PAGEDATA);
		break; // }
		default: // { plugins, and unknown
			$not_found=true;
			if (isset($PLUGINS[$PAGEDATA->type])) {
				$p=$PLUGINS[$PAGEDATA->type];
				if (isset($p['frontend']['page_type'])
					&& function_exists($p['frontend']['page_type'])
				) {
					$c.=$p['frontend']['page_type']($PAGEDATA);
					$not_found=false;
				}
			}
			if ($not_found) {
				$c.='<em>No plugin found to handle page type <strong>'
					.htmlspecialchars($PAGEDATA->type)
					.'</strong>. Is the plugin installed and enabled?</em>';
			}
			// }
	}
	if ($c=='' && !$id) {
		// delete this if it's never called by March 2011
		$c=show404(str_replace('/', ' ', $_SERVER['REQUEST_URI']));
	}
}
if (isset($PLUGINS['comments'])) {
	$c.= plugin_trigger('page-content-created');
}
$pagecontent=$c.'<span class="end-of-page-content"></span>';
// }
// { load page template
if (file_exists(THEME_DIR.'/'.THEME.'/h/'.$PAGEDATA->template.'.html')) {
	$template=THEME_DIR.'/'.THEME.'/h/'.$PAGEDATA->template.'.html';
}
else if (file_exists(THEME_DIR.'/'.THEME.'/h/_default.html')) {
	$template=THEME_DIR.'/'.THEME.'/h/_default.html';
}
else {
	$d=array();
	$dir=new DirectoryIterator(THEME_DIR.'/'.THEME.'/h/');
	foreach ($dir as $f) {
		if ($f->isDot()) {
			continue;
		}
		$n=$f->getFilename();
		if (preg_match('/\.html$/', $n)) {
			$d[]=preg_replace('/\.html$/', '', $n);
		}
	}
	asort($d);
	$template=$d[0];
}
if ($template=='') {
	die('no template created. please create a template first');
}
// }
// { set up smarty
$smarty=smarty_setup(USERBASE.'/ww.cache/pages');
$smarty->template_dir=THEME_DIR.'/'.THEME.'/h/';
$smarty->assign(
	'PAGECONTENT', '<div id="ww-pagecontent">'.$pagecontent.'</div>'
);
$smarty->assign('PAGEDATA', $PAGEDATA);
// }
// { build metadata
// { page title
$title=($PAGEDATA->title!='')
	?$PAGEDATA->title
	:str_replace('www.', '', $_SERVER['HTTP_HOST']).' > '.$PAGEDATA->name;
$c='<title>'.htmlspecialchars($title).'</title>';
// }
// { show stylesheet and javascript links
$c.='WW_CSS_GOES_HERE';
if (isset($DBVARS['theme_variant']) && $DBVARS['theme_variant']) {
	WW_addCSS('/ww.skins/'.$DBVARS['theme'].'/cs/'.$DBVARS['theme_variant'].'.css');
}
$c.='<style type="text/css">.loggedin{display:'
	.(is_logged_in()?'block':'none')
	.'} .loggedinCell{display:'
	.(is_logged_in()?'table-cell':'none')
	.'}</style>';
$c.='<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>'
	.'<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js"></script>'
	.'<script src="WW_SCRIPTS_GO_HERE"></script>';
if(is_admin()){
	WW_addScript('/ww.admin/j/common.js');
}
$tmp='var pagedata={id:'.$PAGEDATA->id.''
	.plugin_trigger('displaying-pagedata')
	.'},'
	.'userdata={isAdmin:'.(is_admin()?1:0);
if (isset($_SESSION['userdata'])
	&& isset($_SESSION['userdata']['discount'])
) {
	$tmp.=',discount:'.(int)$_SESSION['userdata']['discount'];
}
$tmp.='};document.write("<"+"style>'
	.'a.nojs{display:none !important}<"+"/style>");';
array_unshift($scripts_inline,$tmp);
if (is_admin()) {
	WW_addScript('/ww.admin/j/admin-frontend.js');
	WW_addScript('/j/ckeditor/ckeditor.js');
	WW_addScript('/j/ckeditor/adapters/jquery.js');
	WW_addCSS('/ww.admin/theme/admin-frontend.css');
	foreach ($GLOBALS['PLUGINS'] as $p) {
		if (isset($p['frontend']['admin-script'])) {
			WW_addScript($p['frontend']['admin-script']);
		}
	}
}
// }
// { meta tags
$c.='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
if ($PAGEDATA->keywords) {
	$c.='<meta http-equiv="keywords" content="'
		.htmlspecialchars($PAGEDATA->keywords).'" />';
}
if ($PAGEDATA->description) {
	$c.='<meta http-equiv="description" content="'
		.htmlspecialchars($PAGEDATA->description).'"/>';
}
if (isset($PAGEDATA->vars['google-site-verification'])) {
	$c.='<meta name="google-site-verification" content="'
		.htmlspecialchars($PAGEDATA->vars['google-site-verification']).'" />';
}
// }
// { favicon
if (file_exists(USERBASE.'/f/skin_files/favicon.ico')) {
	$c.='<link rel="shortcut icon" href="/f/skin_files/favicon.ico" />';
}
// }
$smarty->assign('METADATA', $c);
// }
// { display the document
ob_start();
if (strpos($template, '/')===false) {
	$template=THEME_DIR.'/'.THEME.'/h/'.$template.'.html';
}
$t=$smarty->fetch($template);
echo str_replace(
	array('WW_SCRIPTS_GO_HERE', 'WW_CSS_GOES_HERE', '</body>'),
	array(WW_getScripts(), WW_getCSS(), WW_getInlineScripts().'</body>'),
	$t
);

header('X-page-generation: '.(microtime(true)-$ww_startup).'s');

ob_show_and_log('page', 'Content-type: text/html; Charset=utf-8');
// }
