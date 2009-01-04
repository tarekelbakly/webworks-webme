<?php
header('Cache-Control: max-age=2592000');
header('Expires-Active: On');
header('Expires: Fri, 1 Jan 2500 01:01:01 GMT');
header('Pragma:');
header('Content-type: text/javascript; charset=utf-8');

session_start();

require '../common/jslibs.php';
require '../common/webme_specific.php';
$name=md5_of_dir('./');
if(!is_dir('../f/.files/j'))mkdir('../f/.files/j');
//if(!isset($_GET['admin']) && file_exists('../f/.files/j/'.$name))readfile('../f/.files/j/'.$name);
else{
	$js=file_get_contents('mootools.v1.11.js');
	$js.=file_get_contents('jquery.accordion.js').';';
//	$js.=file_get_contents('jquery.lazyload.mini.js');
	$js.=file_get_contents('json.js');
	$js.=file_get_contents('js.js');
	$js.=file_get_contents('tabs.js');
	$js.=file_get_contents('addrow.js');
	$js.=file_get_contents('formhide.js');
	$js.=file_get_contents('getels.js');
	$js.=file_get_contents('getmouseat.js');
	$js.=file_get_contents('getoffset.js');
	
	{ # browser-specific functions
		if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))$_SERVER['HTTP_ACCEPT_LANGUAGE']='';
		require_once('Net/UserAgent/Detect.php');
		$browser=new Net_UserAgent_Detect();
		$os=$browser->getOSString();
		$isIE       =$browser->isIE();
		if($isIE){
			$js.=file_get_contents('getwindowscrollat.ie.js');
			$js.=file_get_contents('getwindowsize.ie.js');
			$js.=file_get_contents('newel.ie.js');
			$js.=file_get_contents('newform.ie.js');
			$js.=file_get_contents('xmlhttprequest.ie.js');
		}
		else{
			$js.=file_get_contents('getwindowscrollat.js');
			$js.=file_get_contents('getwindowsize.js');
			$js.=file_get_contents('newel.js');
			$js.=file_get_contents('newform.js');
		}
	}
	
	if(is_admin()){
		$js.=file_get_contents('../ww.admin/j/common.js');
		$js.=file_get_contents('notice.js');
		$js.=file_get_contents('fckeditor-2.6.3/fckeditor.js');
	}
	
/* get rid of browser-dependent code above before uncommenting this
  if(isset($_REQUEST['minify'])){
    require '../common/jsmin-1.1.1.php';
    $js=JSMin::minify($js);
    file_put_contents('../f/.files/j/'.$name,$js);
    delete_old_md5s('../f/.files/j/');
    exit;
  }
  else{
    $js.="setTimeout(function(){var a=document.createElement('img');a.src='/j/js.php?minify=1';a.style.display='none';document.body.appendChild(a);},5000);";
  }
*/

	echo $js;
}
