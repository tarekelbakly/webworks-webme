<?php
header('Expires-Active: On');
header('Expires: Fri, 1 Jan 2500 01:01:01 GMT');
header('Pragma:');
header('Content-type: text/css; charset=utf-8');

echo file_get_contents('menus.css');
echo file_get_contents('language_flags.css');
echo file_get_contents('ui.datepicker.css');
echo file_get_contents('forms.css');
echo file_get_contents('comments.css');
echo file_get_contents('contextmenu.css');
echo file_get_contents('lightbox.css');
echo file_get_contents('tabs.css');

if(isset($_GET['skin']) && isset($_GET['variant'])){
	if(strpos($_GET['skin'],'.')!==false || strpos($_GET['variant'],'.')!==false)exit;
	require '../.private/config.php';
	if(isset($DBVARS['theme_dir']))define('THEME_DIR',$DBVARS['theme_dir']);
	else define('THEME_DIR',$_SERVER['DOCUMENT_ROOT'].'/ww.skins');
	$fname=THEME_DIR.'/'.$_GET['skin'].'/cs/'.$_GET['variant'].'.css';
	if(file_exists($fname))echo file_get_contents($fname);
}
