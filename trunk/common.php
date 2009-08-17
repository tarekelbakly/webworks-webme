<?php
require dirname(__FILE__).'/ww.incs/basics.php';
function date_m2h($d, $type = 'date') {
	$date = preg_replace('/[- :]/', ' ', $d);
	$date = explode(' ', $date);
	if ($type == 'date') {
		if( isset($_SESSION['__webme_language']) && $_SESSION['__webme_language'] == 'fr' )return $date[2].'.'.$date[1].'.'.$date[0];
		return @date('l jS F, Y', mktime(0, 0, 0, $date[1], $date[2], $date[0]));
	}
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
function html_fixImageResizes($src){
	// checks for image resizes done with HTML parameters or inline CSS
	//   and redirects those images to pre-resized versions held elsewhere

	preg_match_all('/<img [^>]*>/im',$src,$matches);
	if(!count($matches))return $src;
	foreach($matches[0] as $match){
		$width=0;
		$height=0;
		if(preg_match('#width="[0-9]*"#i',$match) && preg_match('/height="[0-9]*"/i',$match)){
			$width=preg_replace('#.*width="([0-9]*)".*#i','\1',$match);
			$height=preg_replace('#.*height="([0-9]*)".*#i','\1',$match);
		}
		else if(preg_match('/style="[^"]*width: *[0-9]*px/i',$match) && preg_match('/style="[^"]*height: *[0-9]*px/i',$match)){
			$width=preg_replace('#.*style="[^"]*width: *([0-9]*)px.*#i','\1',$match);
			$height=preg_replace('#.*style="[^"]*height: *([0-9]*)px.*#i','\1',$match);
		}
		if(!$width || !$height)continue;
		$imgsrc=preg_replace('#.*src="([^"]*)".*#i','\1',$match);

		// get absolute address of img (naive, but will work for most cases)
		if(!preg_match('/^http/i',$imgsrc))$imgsrc=preg_replace('#^/*#','http://'.$_SERVER['HTTP_HOST'].'/',$imgsrc);

		list($x,$y)=getimagesize($imgsrc);
		if(!$x || !$y || ($x==$width && $y==$height))continue;

		// create address of resized image and update HTML
		$dir=md5($imgsrc);
		$newURL=WORKURL_IMAGERESIZES.$dir.'/'.$width.'x'.$height.'.jpg';
		$newImgHTML=preg_replace('/(.*src=")[^"]*(".*)/i',"$1$newURL$2",$match);
		$src=str_replace($match,$newImgHTML,$src);

		// create cached image
		$imgdir=WORKDIR_IMAGERESIZES.$dir;
		@mkdir(WORKDIR_IMAGERESIZES);
		@mkdir($imgdir);
		$imgfile=$imgdir.'/'.$width.'x'.$height.'.jpg';
		if(file_exists($imgfile))continue;
		$str='convert "'.addslashes($imgsrc).'" -geometry '.$width.'x'.$height.' "'.$imgfile.'"';
		exec($str);
	}

	return $src;
}
function inc_common($f) {
	include_once SCRIPTBASE . 'common/' . $f;
}
function redirect($addr){
	header('Location: '.$addr);
	echo '<html><head><script type="text/javascript">setTimeout(function(){document.location="'.$addr.'";},10);</script></head><body><noscript>you need javascript to use this site</noscript></body></html>';
	exit;
}
function config_rewrite(){
	global $DBVARS;
	$tmparr=$DBVARS;
	$tmparr['plugins']=join(',',$DBVARS['plugins']);
	$tmparr2=array();
	foreach($tmparr as $name=>$val)$tmparr2[]='\''.addslashes($name).'\'=>\''.addslashes($val).'\'';
	$config="<?php\n\$DBVARS=array(\n	".join(",\n	",$tmparr2)."\n);";
	file_put_contents(CONFIG_FILE,$config);
}
function sanitise_html($html) {
	$html = preg_replace('/<font([^>]*)>/', '<span\1>', $html);
	$html = preg_replace('/<([^>]*)color="([^"]*)"([^>]*)>/', '<\1style="color:\2"\3>', $html);
	$html = str_replace('</font>', '</span>', $html);
	$html = html_fixImageResizes($html);
	return $html;
}
function webmeMail($from, $to, $subject, $message, $files = false) {
	inc_common('mail.php');
	send_mail($from, $to, $subject, $message, $files);
}
$is_admin = 0;
$sitedomain=str_replace('www.','',$_SERVER['HTTP_HOST']);
if(strpos($_SERVER['REQUEST_URI'],'ww.admin/')!==false){
	require_once SCRIPTBASE . 'j/'.FCKEDITOR.'/editor/plugins/kfm/api/api.php';
	require_once SCRIPTBASE . 'j/'.FCKEDITOR.'/editor/plugins/kfm/initialise.php';
}
// { quick-build similar functions
	$arr = array(array('eventCalendarDisplay', 'funcs.events.php', 'ww_eventCalendarDisplay'), array('panelDisplay', 'funcs.panels.php', 'ww_panelDisplay'), array('imageDisplay', 'funcs.image.display.php', 'func_image_display'), array('menuDisplay', 'menus.php', 'ww_menuDisplay'), array('scrollingEventsDisplay', 'funcs.events.php', 'ww_scrollingEventsDisplay'), array('scrollingNewsDisplay', 'funcs.news.php', 'ww_scrollingNewsDisplay'), array('show404', '404.php', 'ww_show404'), array('showNews', 'funcs.news.php', 'ww_showNews'), array('showProductListing', 'products.php', 'ww_showProductListing'), array('showSearchResults', 'funcs.search.php', 'ww_showSearchResults'), array('sitemap', 'sitemap.php', 'ww_showSitemap'), array('webmeParse', 'funcs.textfilter.php', 'textObjectsFilter'));
	foreach ($arr as $a) {
		eval('function ' . $a[0] . '($a=0){inc_common("' . $a[1] . '");return ' . $a[2] . '($a);}');
	}
// }
// { user authentication
if(isset($_REQUEST['action']) && $_REQUEST['action']==__('login')){
	// { variables
	$email=$_REQUEST['email'];
	$password=$_REQUEST['password'];
	// }
	$r=dbRow('select * from user_accounts where email="'.$email.'" and password=md5("'.$password.'")');
	if($r && count($r)){
		// { update session variables
		$r['password']=$password;
		$_SESSION['userdata']=$r;
		$r['password'] = $_SESSION['userdata']['password'];
		$_SESSION['userdata'] = $r;
		// }
		// { groups
		$USERGROUPS = array();
		$rs = dbAll("select id,name from users_groups,groups where id=groups_id and user_accounts_id=" . $_SESSION['userdata']['id']);
		if($rs)foreach($rs as $r){
			$USERGROUPS[$r['name']] = 1;
		}
		$_SESSION['userdata']['groups']=$USERGROUPS;
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
if(isset($_REQUEST['logout']))unset($_SESSION['userdata']);
// }
// { set/get webme language settings
if(getVar('__webme_language')){
	$_SESSION['webme_language']=addslashes(getVar('__webme_language'));
}
if(!isset($_SESSION['webme_language']) || strpos($_SESSION['webme_language'],'_'!==false)){
	if ($handle = opendir(SCRIPTBASE.'ww.lang')) {
		$files = array('en');
			while(false!==($file = readdir($handle))){
				if(substr($file,0,1)=='.')continue;
				if (is_dir(SCRIPTBASE.'ww.lang/'.$file))$files[] = $file;
			}
		closedir($handle);
		sort($files);
		$available_languages = array();
		foreach($files as $f)$available_languages[] = $f;
	} else {
		echo 'error: missing language files';
		exit;
	}
	$ls=array();
  if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))$_SERVER['HTTP_ACCEPT_LANGUAGE'] = '';
  $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
  foreach($langs as $lang)if (in_array(preg_replace('/;.*/','',trim($lang)), $available_languages)) {
	$_SESSION['webme_language'] = preg_replace('/;.*/','',trim($lang));
	break;
  }
	if(!isset($_SESSION['webme_language']) || !in_array($_SESSION['webme_language'],array('de','en','fr','es')))$_SESSION['webme_language']='en';
}
if($_SESSION['webme_language']=='')$_SESSION['webme_language']='en';
function __setLocale($locale){
	$_SESSION['webme_language']=$locale;
	if(!setLocale(LC_ALL,$_SESSION['webme_language'])){
		preg_match_all("/[^|\w]".$_SESSION['webme_language'].'.*/',`locale -a`,$matches);
		if(!count($matches[0]))die('no locale info for "'.$_SESSION['webme_language'].'"');
		$_SESSION['webme_language']=trim($matches[0][0]);
		foreach($matches[0] as $m)if(preg_match('/utf8/',$m)){
			$_SESSION['webme_language']=trim($m);
			break;
		}
		setLocale(LC_ALL,$_SESSION['webme_language']);
		if(strpos($_SESSION['webme_language'],'_')!==false)$_SESSION['webme_language']=preg_replace('/_.*/','',$_SESSION['webme_language']);
	}
	bindtextdomain('default',SCRIPTBASE.'ww.lang/');
	textdomain('default');
}
__setLocale($_SESSION['webme_language']);
// }
