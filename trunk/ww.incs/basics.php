<?php
date_default_timezone_set('Eire');
session_start();
require 'Log.php';
if(!defined('START_TIME'))define('START_TIME',microtime(true));
function __() {
	$str = gettext(func_get_arg(0));
	for($i = func_num_args()-1 ; $i ; --$i){
		$s=func_get_arg($i);
		$str=str_replace('%'.$i,$s,$str);
	}
	return $str;  
}
if(!function_exists('__autoload')){
	function __autoload($name) {
		require $name . '.php';
	}
}
function cache_clear($type){
	if(!is_dir(USERBASE.'/ww.cache/'.$type))return;
	$d=new DirectoryIterator(USERBASE.'/ww.cache/'.$type);
	foreach($d as $f){
		$f=$f->getFilename();
		if($f=='.' || $f=='..')continue;
		unlink(USERBASE.'/ww.cache/'.$type.'/'.$f);
	}
}
function cache_load($type,$md5){
	if(file_exists(USERBASE.'/ww.cache/'.$type.'/'.$md5)){
		return json_decode(file_get_contents(USERBASE.'/ww.cache/'.$type.'/'.$md5), true);
	}
	return false;
}
function cache_save($type,$md5,$vals){
	if(!is_dir(USERBASE.'/ww.cache/'.$type))mkdir(USERBASE.'/ww.cache/'.$type,0777,true);
	file_put_contents(USERBASE.'/ww.cache/'.$type.'/'.$md5, json_encode($vals));
}
function dbAll($query,$key='') {
	$q = dbQuery($query);
	$results=array();
	while($r=$q->fetch(PDO::FETCH_ASSOC))$results[]=$r;
	if(!$key)return $results;
	$arr=array();
	foreach($results as $r)$arr[$r[$key]]=$r;
	return $arr;
}
function dbInit(){
	if(isset($GLOBALS['db']))return $GLOBALS['db'];
	global $DBVARS;
	$db=new PDO('mysql:host='.$DBVARS['hostname'].';dbname='.$DBVARS['db_name'],$DBVARS['username'],$DBVARS['password']);
	$db->query('SET NAMES utf8');
	$db->num_queries=0;
	$GLOBALS['db']=$db;
	return $db;
}
function dbOne($query, $field='') {
	$r = dbRow($query);
	return $r[$field];
}
function dbQuery($query){
	$db=dbInit();
	$q=$db->query($query);
	$db->num_queries++;
	return $q;
}
function dbRow($query) {
	$q = dbQuery($query);
	return $q->fetch(PDO::FETCH_ASSOC);
}
function ob_show_and_log($type,$header=''){
	$log = &Log::singleton('file',USERBASE.'/log.txt',$type,array('locking'=>true,'timeFormat'=>'%Y-%m-%d %H:%M:%S'));
	$length=ob_get_length();
	$num_queries=isset($GLOBALS['db'])?$GLOBALS['db']->num_queries:0;
	switch($type){
		case 'design_file': // {
			$location=$_SERVER['REQUEST_URI'];
			break;
		// }
		case 'file': // {
			$location=$_SERVER['REQUEST_URI'];
			break;
		// }
		case 'menu': // {
			$location='menu';
			break;
		// }
		case 'page': // {
			$location=$GLOBALS['PAGEDATA']->id.'|'.$GLOBALS['PAGEDATA']->getRelativeUrl();
			break;
		// }
		default: // {
			$location='unknown_type_'.$type;
		//}
	}
	$log->log(
		$_SERVER['REMOTE_ADDR']
		.'	'.$location
		.'	'.@$_SERVER['HTTP_USER_AGENT']
		.'	'.@$_SERVER['HTTP_REFERER']
		.'	'.memory_get_peak_usage()
		.'	'.$length
		.'	'.(microtime(true)-START_TIME)
		.'	'.$num_queries
	);
	if($header)header($header);
	ob_flush();
}
function admin_can_create_top_pages(){
	return has_page_permissions(1024);
}
function is_admin(){
	return (isset($_SESSION['userdata']) && isset($_SESSION['userdata']['groups']['administrators']));
}
function is_logged_in(){
	return isset($_SESSION['userdata']);
}
function get_userid(){
	return $_SESSION['userdata']['id'];
}
function has_page_permissions($val){
	return true;
}
function has_access_permissions($val){
	return true;
}
define('SCRIPTBASE', $_SERVER['DOCUMENT_ROOT'] . '/');
if (!file_exists(SCRIPTBASE . '.private/config.php')) {
	echo '<html><body><p>No configuration file found</p>';
	if(file_exists('install/index.php'))echo '<p><a href="/install/index.php">Click here to install</a></p>';
	else echo '<p><strong>Installation script also missing...</strong> please contact kae@webworks.ie if you think there\'s a problem.</p>';
	echo '</body></html>';
	exit;
}
require SCRIPTBASE . '.private/config.php';
if(isset($DBVARS['userbase']))define('USERBASE', $DBVARS['userbase']);
else define('USERBASE', $_SERVER['DOCUMENT_ROOT']);
// { built-in page types
$pagetypes=array(
	array(0,'normal',0),
	array(4,'page summaries',0),
	array(5,'search results',0),
	array(9,'table of contents',0)
);
// }
$admin_top_menu=array(
	array('id'=>'am_pages','name'=>'pages','link'=>'pages.php'),
	array('id'=>'am_siteoptions','name'=>_('site options'),'link'=>'siteoptions.php'),
	array('id'=>'am_stats','name'=>_('stats'),'link'=>'stats.php')
);
$DBVARS['plugins']=(isset($DBVARS['plugins']) && $DBVARS['plugins']!='')?explode(',',$DBVARS['plugins']):array();
if(!defined('CONFIG_FILE'))define('CONFIG_FILE',SCRIPTBASE.'.private/config.php');
define('WORKDIR_IMAGERESIZES', USERBASE.'/f/.files/image_resizes/');
define('WORKURL_IMAGERESIZES', '/f/.files/image_resizes/');
define('CKEDITOR','ckeditor');
if(!defined('KFM_BASE_PATH'))define('KFM_BASE_PATH', SCRIPTBASE.'j/kfm/');
set_include_path(SCRIPTBASE.'ww.php_classes'.PATH_SEPARATOR.KFM_BASE_PATH.'classes'.PATH_SEPARATOR.get_include_path());
// { theme variables
if(isset($DBVARS['theme_dir']))define('THEME_DIR',$DBVARS['theme_dir']);
else define('THEME_DIR',SCRIPTBASE.'ww.skins');
if(isset($DBVARS['theme']) && $DBVARS['theme'])define('THEME',$DBVARS['theme']);
else{
	$dir=new DirectoryIterator(THEME_DIR);
	$themes_found=0;
	$DBVARS['theme']='.default';
	foreach($dir as $file){
		if(strpos($file,'.')===0)continue;
		$DBVARS['theme']=$file;
		break;
	}
	define('THEME',$DBVARS['theme']);
}
// }
$PLUGINS=array();
#echo "load ww.incs/basics.php7 ".(microtime(true)-START_TIME).'<br />';
if(!isset($ignore_webme_plugins)){
	foreach($DBVARS['plugins'] as $pname){
		if(strpos('/',$pname)!==false)continue;
	#echo "load ww.incs/basics.php8 ".(microtime(true)-START_TIME).'<br />';
		require SCRIPTBASE . 'ww.plugins/'.$pname.'/plugin.php';
	#echo "load ww.incs/basics.php9 ".(microtime(true)-START_TIME).'<br />';
		if(@$plugin['version'] && (@$DBVARS[$pname.'|version']!=$plugin['version'])){
			$version=(int)@$DBVARS[$pname.'|version'];
			require SCRIPTBASE . 'ww.plugins/'.$pname.'/upgrade.php';
			header('Location: '.$_SERVER['REQUEST_URI']);
			exit;
		}
		$PLUGINS[$pname]=$plugin;
	}
}
#echo "load ww.incs/basics.php ".(microtime(true)-START_TIME).'<br />';