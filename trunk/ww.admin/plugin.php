<?php
require 'header.php';
$pname=$_REQUEST['_plugin'];
if(strpos('/',$pname)!==false)die('not allowed use the character "/" in a plugin name');
if(!isset($PLUGINS[$pname]))die('no plugin of that name ('.$pname.') exists');
$plugin=$PLUGINS[$pname];
echo '<h1>'.__($plugin['name']).'</h1>';
$_url='/ww.admin/plugin.php?_plugin='.$pname;
if(!file_exists(SCRIPTBASE.'/ww.plugins/'.$pname.'/admin/index.php')){
	echo '<em>The <strong>'.htmlspecialchars($pname).'</strong> plugin does not have an admin page. Please contact the plugin author.</em>';
}
else require SCRIPTBASE.'/ww.plugins/'.$pname.'/admin/index.php';
require 'footer.php';
