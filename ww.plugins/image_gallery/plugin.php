<?php
mail('kae@webworks.ie','image_gallery detected',$_SERVER['HTTP_HOST']);
$kfm_do_not_save_session=true;
require_once KFM_BASE_PATH.'/api/api.php';
require_once KFM_BASE_PATH.'/initialise.php';
$plugin=array(
	'name' => 'Image Gallery',
	'hide_from_admin' => true,
	'admin' => array(
//		'page_type' => 'image_gallery_admin_page_form'
	),
	'description' => 'Allows a directory of images to be shown as a gallery.',
	'frontend' => array(
//		'page_type' => 'image_gallery_frontend'
	),
	'version'=>1
);
/*
function image_gallery_admin_page_form($page,$vars){
	require dirname(__FILE__).'/admin/index.php';
	return $c;
}
function image_gallery_frontend($PAGEDATA){
	require dirname(__FILE__).'/frontend/show.php';
	return image_gallery_show($PAGEDATA);
}
*/
