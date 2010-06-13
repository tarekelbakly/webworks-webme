<?php
header('Content-type: text/html; Charset=utf-8');
date_default_timezone_set('Eire');
require '../ww.incs/common.php';
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
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js"></script>
		<script src="/js/"></script>
		<script src="/j/ckeditor/ckeditor.js"></script>
		<script src="/j/datatables/media/js/jquery.dataTables.js"></script>
		<script src="/j/jquery.remoteselectoptions.js"></script>
		<script src="/j/fg.menu/fg.menu.js"></script>
		<script src="/j/cluetip/jquery.cluetip.js"></script>
		<link rel="stylesheet" type="text/css" href="/j/cluetip/jquery.cluetip.css" />
		<link rel="stylesheet" type="text/css" href="/j/datatables/media/css/demo_table.css" />
		<link rel="stylesheet" href="/ww.admin/theme/admin-20100406.css" type="text/css" />
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/south-street/jquery-ui.css" type="text/css" />
<?php
foreach($PLUGINS as $pname=>$p){
	if(file_exists(SCRIPTBASE.'/ww.plugins/'.$pname.'/admin/admin.css'))echo '<link rel="stylesheet" href="/ww.plugins/'.$pname.'/admin/admin.css" type="text/css" />';
}
?>
		<script src="/ww.admin/j/admin.js"></script>
	</head>
	<body<?php
	if(isset($_REQUEST['frontend-admin']))echo ' class="frontend-admin"';
	?>>
		<div id="header"> 
<?php
	// { setup standard menu items
	$menus=array(
		'Pages'=>array(
			'_link'=>'pages.php'
		),
		'Site Options'=>array(
			'General'=> array('_link'=>'siteoptions.php'),
			'Users'  => array('_link'=>'siteoptions.php?page=users'),
			'Themes' => array('_link'=>'siteoptions.php?page=themes'),
			'Plugins'=> array('_link'=>'siteoptions.php?page=plugins')
		)
	);
	// }
	// { add custom items (from plugins)
	foreach($PLUGINS as $pname=>$p){
		if(!isset($p['admin']) || !isset($p['admin']['menu']))continue;
		foreach($p['admin']['menu'] as $name=>$page){
			if(preg_match('/[^a-zA-Z0-9 >]/',$name))continue; # illegal characters in name
			$json='{"'.str_replace('>','":{"',$name).'":{"_link":"plugin.php?_plugin='.$pname.'&amp;_page='.$page.'"}}'.str_repeat('}',substr_count($name,'>'));
			$menus=array_merge_recursive($menus,json_decode($json,true));
		}
	}
	// }
	// { add final items
	$menus['Stats']=    array('_link'=>'/ww.admin/stats.php');
	$menus['View Site']=array( '_link'=>'/', '_target'=>'_blank');
	$menus['Log Out']=  array('_link'=>'/?logout=1');
	// }
	// { display menu as UL list
	function admin_menu_show($items,$name=false,$prefix,$depth=0){
		$target=(isset($items['_target']))?' target="'.$items['_target'].'"':'';
		if(isset($items['_link']))echo '<a href="'.$items['_link'].'"'.$target.'>'.$name.'</a>';
		else if($name!='top')echo '<a href="#'.$prefix.'-'.$name.'">'.$name.'</a>';
		if(count($items)==1 && isset($items['_link']))return;
		if($depth<2)echo '<div id="'.$prefix.'-'.$name.'">';
		echo '<ul>';
		foreach($items as $iname=>$subitems){
			if($iname=='_link')continue;
			echo '<li>';
			admin_menu_show($subitems,$iname,$prefix.'-'.$name,$depth+1);
			echo '</li>';
		}
		echo '</ul>';
		if($depth<2)echo '</div>';
	}
	admin_menu_show($menus,'top','menu');
	// }
?>
		</div>
		<div id="wrapper">
			<div id="main">
