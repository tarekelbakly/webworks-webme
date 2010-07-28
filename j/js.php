<?php
require_once '../common/jslibs.php';
require_once '../ww.incs/basics.php';

header('Cache-Control: max-age=2592000, public');
header('Expires-Active: On');
header('Expires: Fri, 1 Jan 2500 01:01:01 GMT');
header('Pragma:');
header('Content-type: text/javascript;'); // charset=utf-8');

$files=array(
	'jquery-ui/js/jquery-1.4.2.min.js',
	'jquery-ui/js/jquery-ui-1.8.2.custom.min.js',
	'jquery.json-2.2.min.js',
	'js.js'
);
if(isset($_REQUEST['extra'])){
	$fs=explode('|',$_REQUEST['extra']);
	array_shift($fs);
	foreach($fs as $f){
		if(strpos($f,'..')!==false)continue;
		$fname=SCRIPTBASE.$f;
		if(!preg_match('/\.js$/',$fname) || !file_exists($fname))continue;
		$files[]=$fname;
	}
}

$latest=0;
foreach($files as $f){
	$mt=filemtime($f);
	if($mt>$latest)$latest=$mt;
}
$mt=filemtime(__FILE__);
if($mt>$latest)$latest=$mt;

$name=md5(join('|',$files));

if(
	file_exists(USERBASE.'/ww.cache/j/js-'.$name)
	&& filemtime(USERBASE.'/ww.cache/j/js-'.$name)<$latest
){
	unlink(USERBASE.'/ww.cache/j/js-'.$name);
}

$js=cache_load('j','js-'.$name);
if($js==false){
	$js='';
//	$i=0;
	foreach($files as $f){
//		$js.="\ndocument.title=".$i.";\n";
		$js.=file_get_contents($f);
//		$i++;
	}
	if(function_exists('jsmin')){
		$js=jsmin($js);
	}
	else{
		require_once 'kfm/includes/jsmin-1.1.1.php';
		$js=JSMin::minify($js);
	}
	cache_save('j','js-'.$name,$js);
}
echo $js;
