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
$pagetypes=array(
	array(0,'normal',0),
	array(4,'page summaries',0),
	array(5,'search results',0),
	array(9,'table of contents',0)
);
$admin_top_menu=array(
	array('id'=>'am_pages','name'=>'pages','link'=>'pages.php'),
	array('id'=>'am_siteoptions','name'=>_('site options'),'link'=>'siteoptions.php'),
	array('id'=>'am_stats','name'=>_('stats'),'link'=>'stats.php')
);
