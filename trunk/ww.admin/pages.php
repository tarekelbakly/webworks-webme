<?php
require 'header.php';
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
include('pages/pages.funcs.php');
if(has_access_permissions(ACL_PAGES)){
	# actions
	if($action=='delete' || $action==__('Insert Page Details') || $action==__('Update Page Details')){
		switch($action){
			case 'delete':                  include('pages/pages.action.delete.php'); break;
			case __('Insert Page Details'): include('pages/pages.action.new.php');    break;
			case __('Update Page Details'): include('pages/pages.action.edit.php');   break;
		}
	}
	$edit=($action==__('Insert Page Details') || $action==__('Update Page Details') || $action=='edit')?1:0;
	if($id&&$edit)setAdminVar('pages_viewing',$id);
	else if(!$action){
		$id=getAdminVar('pages_viewing');
		if(!$id)$id=0;
		if($id)$edit=1;
	}
	include('pages/pages.menu.php');
	echo '<div id="pages_main">';
	include('pages/pages.forms.php');
	echo '</div>';
}else{
	echo '<p>'.__('You have no permissions for this page').'</p>';
}
include_once('footer.php');
