<?php
function admin_can_create_top_pages(){
	return has_page_permissions(1024);
}
function config_rewrite(){
	global $DBVARS;
	$config='<'."?php
\$DBVARS=array(
	'username'     => '".addslashes($DBVARS['username'])."',
	'password'     => '".addslashes($DBVARS['password'])."',
	'hostname'     => '".addslashes($DBVARS['hostname'])."',
	'db_name'      => '".addslashes($DBVARS['db_name'])."',
	'theme'        => '".addslashes($DBVARS['theme'])."',
	'site_title'   => '".addslashes($DBVARS['site_title'])."',
	'site_subtitle'=> '".addslashes($DBVARS['site_subtitle'])."',
	'version'      => ".((int)$DBVARS['version']).",
	'userbase'     => '".addslashes($DBVARS['userbase'])."'
);";
	file_put_contents(SCRIPTBASE . '.private/config.php',$config);
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
if(isset($DBVARS['userbase']))define('USERBASE', $DBVARS['userbase']);
else define('USERBASE', $_SERVER['DOCUMENT_ROOT']);
