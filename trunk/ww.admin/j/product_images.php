<?php
require_once('../../common.php');
require_once('../../common/kaejax.php');
require_once('../../j/'.FCKEDITOR.'/editor/plugins/kfm/configuration.php');
require_once('../../j/'.FCKEDITOR.'/editor/plugins/kfm/api/api.php');
function pr_get_image_list($dirid=0){
	return kfm_loadFiles($dirid);
}
function pr_remove_image($p,$id){
	if($p)Product::getInstance($p)->set('default_image',0);
	return kfm_api_removeFile($id);
}
function pr_mark_as_default($p,$image){
	global $kfm_session;
	$_SESSION['pr_default_image']=$image;
	if($p)Product::getInstance($p)->set('default_image',$image);
	return kfm_loadFiles($kfm_session->get('cwd_id'));
}
kaejax_export('pr_get_image_list','pr_remove_image','pr_mark_as_default');
kaejax_handle_client_request();
kaejax_show_javascript();
$product_id=(int)getVar('product_id');
$dirid=0;
$defaultimage=0;
if($product_id){
	$r=Product::getInstance($product_id);
	if($r->kfm_directory)$dirid=$r->kfm_directory;
	$name='product'.$product_id;
	$defaultimage=$r->default_image;
}
else $name='producttmp';
if(!$dirid){
	$pdir=kfm_api_getDirectoryId('product_images');
	if(!$pdir)$pdir=kfm_api_createDirectory(1,'product_images');
	$dirid=kfm_api_getDirectoryId('product_images/'.$name);
	if(!$dirid)$dirid=kfm_api_createDirectory($pdir,$name);
	$_SESSION['product_directory']=$dirid;
	$kfm_session->set('cwd_id',$dirid);
}
echo 'var pr_imagedir='.$dirid.';';
echo 'var pr_defaultimage='.$defaultimage.';';
$_SESSION['pr_default_image']=$defaultimage;
echo 'var pr_id='.$product_id.';';
echo file_get_contents('product_images.js');
