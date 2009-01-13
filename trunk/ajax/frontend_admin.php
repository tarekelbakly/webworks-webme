<?php
require_once('../common.php');
require_once(SCRIPTBASE.'common/kaejax.php');
if(!is_admin()){
	echo 'no stairway - denied!';
	exit;
}
function frontend_admin_getPageContent($id){
	$page=Page::getInstance($id);
	require '../ww.admin/admin_libs.php';
	$cssurl=fckeditor_generateCSS($id);
	return array('html'=>$page->body,'css'=>$cssurl);;
}
function frontend_admin_save($id,$html){
	$html=addslashes($html);
	dbQuery("UPDATE pages SET body='$html' WHERE id=$id");
	return;
}
kaejax_export('frontend_admin_getPageContent','frontend_admin_save');
kaejax_handle_client_request();
kaejax_show_javascript();
echo file_get_contents('frontend_admin.js');
