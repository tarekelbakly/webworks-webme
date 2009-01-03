<?php
include('header.php');
echo '<h1>'.__('Pages').'</h1>';
/*
	PAGES PERMISSIONS:
	1: insert
	2: edit
	4: delete
	8: create/delete in some
	16: edit some
	32: panels
	64: shortcuts
	128: specials
	256: **unused**
	512: htmlinput
*/
$pagetypes=array(
	array(0,__('normal'),0),
	array(7,__('news'),512),
	array(2,__('events calendar'),512),
	array(5,__('search results'),0),
	array(4,__('blog index'),0),
	array(3,__('user login/registration'),0),
	array(8,__('products'),16384),
	array(9,__('table of contents'),0),
	array(10,__('online store checkout'),0)
);
include('pages/pages.funcs.php');
# actions
if($action=='delete' || $action==__('Insert Page Details') || $action==__('Update Page Details')){
	switch($action){
		case 'delete':                  include('pages/pages.action.delete.php'); break;
		case __('Insert Page Details'): include('pages/pages.action.new.php');    break;
		case __('Update Page Details'): include('pages/pages.action.edit.php');   break;
	}
	include_once('../common/funcs.blogs.php');
	rebuild_parent_rsses($id);
}
$edit=($action==__('Insert Page Details') || $action==__('Update Page Details') || $action=='edit')?1:0;
if(isset($_REQUEST['id']) && $_REQUEST['id'] && $edit){
	$id=$_REQUEST['id'];
	setAdminVar('pages_viewing',$id);
}
else if(!$action){
	$id=getAdminVar('pages_viewing');
	if(!$id)$id=0;
	if($id)$edit=1;
}
include('pages/pages.menu.php');
echo '<div id="pages_main">';
include('pages/pages.forms.php');
echo '</div>';
include_once('footer.php');
