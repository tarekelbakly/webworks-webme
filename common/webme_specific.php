<?php
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
if(isset($DBVARS['userbase']))define('USERBASE', $DBVARS['userbase']);
else define('USERBASE', $_SERVER['DOCUMENT_ROOT']);
