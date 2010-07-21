<?php
require_once '../common/jslibs.php';
require_once '../ww.incs/basics.php';

header('Cache-Control: max-age=2592000, public');
header('Expires-Active: On');
header('Expires: Fri, 1 Jan 2500 01:01:01 GMT');
header('Pragma:');
header('Content-type: text/javascript; charset=utf-8');

$name=md5_of_dir('./');

$js=cache_load('j','js-'.$name.'-minified');
if($js==false){
	$js=cache_load('j','js-'.$name);
	if($js==false){
		$js.=file_get_contents('jquery-ui/js/jquery-1.4.2.min.js');
		$js.=file_get_contents('jquery-ui/js/jquery-ui-1.8.1.custom.min.js');
		$js.=file_get_contents('json.js');
		$js.=file_get_contents('js.js');
		$js.=file_get_contents('tabs.js');
		$js.=file_get_contents('getoffset.js');
		cache_clear('j');
		cache_save('j','js-'.$name,$js);
	}
	else{
	/*
  	require_once '../ww.incs/class.JavaScriptPacker.php';
  	$packer=new JavaScriptPacker($js,62);
  	$js=$packer->pack();
	*/
	/*
		// install http://www.ypass.net/software/php_jsmin/ for very fast minifying
		if(function_exists('jsmin')){
			$js=jsmin($js);
		}
		else{
			require_once 'kfm/includes/jsmin-1.1.1.php';
			$js=JSMin::minify($js);
		}
	*/
		cache_save('j','js-'.$name.'-minified',$js);
	}
}
echo $js;
