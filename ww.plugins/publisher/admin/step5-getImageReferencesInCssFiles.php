<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if (!is_admin()) {
	die('access denied');
}

$base=USERBASE.'/ww.cache/publisher';

$images=array();

$files=new DirectoryIterator($base.'/css');
foreach ($files as $file) {
	if ($file->isDot() || $file->isDir()) {
		continue;
	}
	$f=file_get_contents($base.'/css/'.$file->getFilename());
	// { get list of image files
	preg_match_all('/\([\'"]?([^\'"\)]*\.(jpg|gif|jpeg|png))[\'"]?\)/', $f, $matches);
	foreach ($matches[1] as $m) {
		$images[]=array($m, str_replace('/', '@', $m));
	}
	// }
}

file_put_contents($base.'/tmp/cssimages.json', json_encode($images));
