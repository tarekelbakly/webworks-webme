<?php
require 'header.php';
echo '<h1>'.__('Pages').'</h1>';
$parent=(int)@$_REQUEST['parent'];
$msgs='';
include('pages/pages.funcs.php');
if($action=='delete' || $action==__('Insert Page Details') || $action==__('Update Page Details')){
	switch($action){
		case 'delete':                  include('pages/pages.action.delete.php'); break;
		case __('Insert Page Details'): include('pages/pages.action.new.php');    break;
		case __('Update Page Details'): include('pages/pages.action.edit.php');   break;
	}
}
$is_an_update=($action==__('Insert Page Details') || $action==__('Update Page Details'));
$edit=($is_an_update || $action=='edit')?1:0;
echo '<div class="left-menu">';
include 'pages/menu.php';
echo '</div>';
echo '<div class="has-left-menu">';
include('pages/pages.forms.php');
echo '</div>';
echo '<script>window.page_menu_currentpage='.$id.';</script>',
		'<script src="/j/jquery.remoteselectoptions.js"></script>',
		'<style type="text/css">@import "pages/css.css";</style>';
require 'footer.php';
