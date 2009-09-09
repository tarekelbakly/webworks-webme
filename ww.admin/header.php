<?
header('Content-type: text/html; Charset=utf-8');
require '../common.php';
// { if not logged in, show login page
if (!is_admin()) {
	include SCRIPTBASE . 'ww.admin/login.php';
	exit;
}
// }
require 'admin_libs.php';
$admin_vars=array();
// { common variables
	foreach(array('action','resize') as $v)$$v=getVar($v);
	foreach(array('show_items','start') as $v)$$v=getVar($v,0);
	$id=(int)@$_REQUEST['id'];
	$plugins_to_load=array('"showhide":1','"tabs":1','"vkfade":1'); // to be used by javascript
// }
?>
<html>
	<head>
		<link rel="stylesheet" href="/ww.admin/theme/admin.css" type="text/css" />
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/ui-lightness/jquery-ui.css" type="text/css" media="all" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
		<script src="/js/"></script>
		<script src="/j/ckeditor/ckeditor.js"></script>
	</head>
	<body>
	<div id="wrapper">
		
<div id="header"> 
  <div id="toprightbox"></div><a href="http://webworks.ie/f/webme-help.pdf" class="ajaxmenu_linkam_help" title="<?php echo __('Click to read a PDF doc, detailing how to get started with WebME').'">'.__('Help'); ?></a><a href="./?logout=1" class="ajaxmenu_linkam_logout" title="<?php echo __('Log out from your account').'">'.__('Logout');?></a>
</div>
		<div id="ajaxmenuam_top" class="adminmenu ajaxmenu menuBarTop"><script type="text/javascript">var pagedata={id:'am_top'};</script><a href="#"><?php echo __('loading. please wait...');?></a></div>
		<div id="main">
