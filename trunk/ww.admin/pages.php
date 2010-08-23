<?php
require 'header.php';
$id=(int)$_REQUEST['id'];
echo '<h1>'.__('Pages').'</h1>';
echo '<div class="left-menu">';
include 'pages/menu.php';
echo '</div>';
echo '<div class="has-left-menu">'
	.'<iframe id="page-form-wrapper" name="page-form-wrapper" src="pages/form.php?id='.$id.'"></iframe>'
	.'</div>';
echo '<script>window.page_menu_currentpage='.$id.';</script>',
		'<script src="/j/jquery.remoteselectoptions.js"></script>',
		'<style type="text/css">@import "pages/css.css";</style>';
require 'footer.php';
