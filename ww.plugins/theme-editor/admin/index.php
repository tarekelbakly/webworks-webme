<h1>Theme Editor</h1>
<?php

function recurse_copy($src,$dst) {
	$dir = opendir($src);
	@mkdir($dst);
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if ( is_dir($src . '/' . $file) ) {
				recurse_copy($src . '/' . $file,$dst . '/' . $file);
			}
			else {
				copy($src . '/' . $file,$dst . '/' . $file);
			}
		}
	}
	closedir($dir);
} 
if(isset($_REQUEST['other']) && $_REQUEST['other']=='restore'){
	global $DBVARS;
	if(is_dir($DBVARS['theme_dir'].'/'.$DBVARS['theme'])){
		recurse_copy($DBVARS['theme_dir'].'/'.$DBVARS['theme'],$DBVARS['theme_dir_personal'].'/'.$DBVARS['theme']);
		cache_clear('pages');
	}
}

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
// { other
echo '<h2>other</h2><ul>';
echo '<li><a href="/ww.admin/plugin.php?_plugin=theme-editor&amp;_page=index&amp;other=restore" onclick="return confirm(\'This will overwrite your local changes by restoring the original version of the theme.\');">restore</a></li>';
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
