<h1>Theme Editor</h1>
<?php

// { menu
echo '<div class="left-menu">';
// { html templates
$d=new DirectoryIterator(THEME_DIR.'/'.THEME.'/h');
$files=array();
foreach($d as $f){
	if($f->isDot())continue;
	$fname=$f->getFileName();
	if(!preg_match('/\.html$/',$fname))continue;
	$files[]=preg_replace('/\.html$/','',$fname);
}
asort($files);
echo '<h2>Templates</h2>';
echo '<ul>';
foreach($files as $file)
	echo '<li><a href="/ww.admin/plugin.php?_plugin=theme-editor&amp;_page=index&amp;name='.urlencode($file).'&amp;type=h">'.htmlspecialchars($file).'</a></li>';
echo '</ul>';
// }
// { CSS files 
$d=new DirectoryIterator(THEME_DIR.'/'.THEME.'/c');
$files=array();
foreach($d as $f){
	if($f->isDot())continue;
	$fname=$f->getFileName();
	if(!preg_match('/\.css$/',$fname))continue;
	$files[]=preg_replace('/\.css$/','',$fname);
}
asort($files);
echo '<h2>CSS</h2>';
echo '<ul>';
foreach($files as $file)
	echo '<li><a href="/ww.admin/plugin.php?_plugin=theme-editor&amp;_page=index&amp;name='.urlencode($file).'&amp;type=c">'.htmlspecialchars($file).'</a></li>';
echo '</ul>';
// }
echo '</div>';
// }
// { content
echo '<div class="has-left-menu">';
if(!isset($_REQUEST['name']) || !isset($_REQUEST['type']) || !in_array($_REQUEST['type'],array('h','c')) || !preg_match('/^[a-z0-9_][^\/]*$/',$_REQUEST['name'])){
	echo '<p>Please choose a file from the left menu.</p>';
}
else{
	$name=$_REQUEST['name'];
	switch($_REQUEST['type']){
		case 'h': // {
			require 'templates.php';
			break;
		// }
		case 'c': // {
			require 'css.php';
			break;
		// }
	}
}
echo '</div>';
// }
