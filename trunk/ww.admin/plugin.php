<?php
require 'header.php';
$pname=$_REQUEST['_plugin'];
$pagename=$_REQUEST['_page'];
if(!isset($PLUGINS[$pname]))die('no plugin of that name ('.$pname.') exists');
if(preg_match('/[^\-a-zA-Z0-9]/',$pagename) || $pagename=='')die('illegal character in page name');
$plugin=$PLUGINS[$pname];
echo '<h1>'.$plugin['name'].'</h1>';
$_url='/ww.admin/plugin.php?_plugin='.$pname.'&amp;_page='.$pagename;
if(!file_exists(SCRIPTBASE.'/ww.plugins/'.$pname.'/admin/'.$pagename.'.php')){
	echo '<em>The <strong>'.htmlspecialchars($pname).'</strong> plugin does not have an admin page named <strong>'.$pagename.'</strong>. Please contact the plugin author.</em>';
}
else require SCRIPTBASE.'/ww.plugins/'.$pname.'/admin/'.$pagename.'.php';
require 'footer.php';
