<?php
require 'header.php';
echo '<h1>'.__('Pages').'</h1>';
$parent=(int)@$_REQUEST['parent'];
$msgs='';
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
	$is_an_update=($action==__('Insert Page Details') || $action==__('Update Page Details'));
	$edit=($is_an_update || $action=='edit')?1:0;
	if($id&&$edit)setAdminVar('pages_viewing',$id);
	else if(!$action){
		$id=getAdminVar('pages_viewing');
		if(!$id)$id=0;
		if($id)$edit=1;
	}
#	echo '<div style="width:21%;float:left" id="page_menu"></div>';
	echo '<div class="left-menu">';
	include 'pages/menu.php';
	echo '</div>';
	echo '<div class="has-left-menu">';
	include('pages/pages.forms.php');
	echo '</div>';
}else{
	echo '<p>'.__('You have no permissions for this page').'</p>';
}
echo '<script>window.page_menu_currentpage='.$id.';</script>',
		'<script src="/j/jquery.remoteselectoptions.js"></script>',
#		'<script src="http://verens.com/demos/nested-sortables/ui.sortable.js"></script>',
#		'<script src="/ww.admin/pages/pages.js"></script>',
		'<style type="text/css">@import "pages/css.css";</style>';
require 'footer.php';
