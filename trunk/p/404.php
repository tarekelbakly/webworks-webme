<?php
$SCRIPTBASE=$_SERVER['DOCUMENT_ROOT'];
include_once($SCRIPTBASE.'/common.php');
$r=preg_replace('/^\//','',$_SERVER['REQUEST_URI']);
if(preg_match('#^f/.*\.[A-Z]{3}\.([^.]*)$#',$r)){
	echo 'missing image';exit;
}
$r=preg_replace('/\?.*/','',$r);
$r=addslashes(urldecode($r));
if(strlen($r)>1 && strlen($r)-1==strrpos($r,'/')){ // tried to access a page as a directory
	header('Location: /'.preg_replace('/\/$/','',$r));
	exit;
}
$d=Page::getInstanceByName($r);
if($d){
	$id=$d->id;
	header('Status: 301 typo maybe');
	header('Redirect: '.$d->getRelativeURL());
}
else{
	header('Status: 301 no chickens here');
	header('Redirect: /');
}
