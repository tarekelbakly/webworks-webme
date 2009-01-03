<?
include_once($_SERVER['DOCUMENT_ROOT'].'/.private/config.php');
if(!session_id()){
	if(isset($_GET['cms_session']))session_id($_GET['cms_session']);
	session_start();
}
if(($_SERVER['PHP_SELF']!='/j/fckeditor-2.6.3/editor/plugins/kfm/get.php') && (!isset($kfm_api_auth_override)||!$kfm_api_auth_override) && (!isset($_SESSION['admin'])||$_SESSION['admin']=='')){
	echo 'access denied!';
	exit;
}
