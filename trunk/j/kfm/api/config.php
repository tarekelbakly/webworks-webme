<?php
if(!defined('START_TIME'))define('START_TIME',microtime(true));
$ignore_webme_plugins=true;
include_once $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
$kfm_userfiles_address=$DBVARS['userbase'].'/f/';
if(!session_id()){
	if(isset($_GET['cms_session']))session_id($_GET['cms_session']);
	session_start();
}
if($_SERVER['PHP_SELF']!='/j/kfm/get.php' && (!isset($kfm_api_auth_override)||!$kfm_api_auth_override) && !is_admin()){
	echo 'access denied!';
	exit;
}
if($_SERVER['PHP_SELF']=='/j/kfm/get.php'){
	$kfm_do_not_save_session=true;
}
$kfm_api_auth_override=true;
$kfm->defaultSetting('theme', 'default');
$kfm->defaultSetting('file_handler','return');
$kfm->defaultSetting('file_url','filename');
$kfm->defaultSetting('return_file_id_to_cms',$kfm_return_file_id_to_cms);
