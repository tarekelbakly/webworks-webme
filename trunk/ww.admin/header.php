<?
header('Content-type: text/html; Charset=utf-8');
require '../common.php';
// { if not logged in, show login page
if (!is_admin()) {
	include BASEDIR . 'ww.admin/login.php';
	exit;
}
// }
require 'admin_libs.php';
require BASEDIR.'j/'.FCKEDITOR.'/fckeditor.php';
$admin_vars=array();
// { common variables
	foreach(array('action','resize') as $v)$$v=getVar($v);
	foreach(array('id','show_items','start') as $v)$$v=getVar($v,0);
	$plugins_to_load=array('"showhide":1','"tabs":1','"vkfade":1'); // to be used by javascript
// }
?>
<html>
	<head>
		<link rel="stylesheet" href="/ww.admin/theme/admin.css" type="text/css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
		<script type="text/javascript" src="/js/"></script>
		<script type="text/javascript" src="/j/codepress-0.9.6/codepress.js"></script>
	</head>
	<body>
	<div id="wrapper">
		
<div id="header"> 
  <div id="toprightbox"></div><a href="http://webme.eu/f/fckeditor/File/ww.admin/webme-help.pdf" class="ajaxmenu_linkam_help" title="<?php echo __('Click to read a PDF doc, detailing how to get started with WebME').'">'.__('Help'); ?></a><a href="./?logout=1" class="ajaxmenu_linkam_logout" title="<?php echo __('Log out from your account').'">'.__('Logout');?></a>
</div>
		<div id="ajaxmenuam_top" class="adminmenu ajaxmenu menuBarTop"><script type="text/javascript">var pagedata={id:'am_top'};</script><a href="#"><?php echo __('loading. please wait...');?></a></div>
		<div id="main">
