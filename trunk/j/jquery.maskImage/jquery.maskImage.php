<?php
if (!isset($_REQUEST['src'])) {
	echo 'failed at hurdle 0';
	exit;
}
$src=$_REQUEST['src'];
$mask=$_REQUEST['mask'];

if (!$src || !$mask || strpos($src, '..')!==false || strpos($mask, '..')!==false) {
	echo 'failed at hurdle 1';
	exit;
}

require_once $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if ($src{0}=='/') {
	$src=USERBASE.$src;
}
else {
	$src=dirname(__FILE__).'/'.$src;
}
if ($mask{0}=='/') {
	$mask=USERBASE.$mask;
}
else {
	$mask=dirname(__FILE__).'/'.$mask;
}

if (!file_exists($src) || !file_exists($mask)) {
	echo 'failed at hurdle 2';
	exit;
}

$md5=md5(print_r($_REQUEST, true));
$cache='cache';
if (file_exists('cache/'.$md5.'.png')) {
	header('Content-Type: image/png');
	readfile('cache/'.$md5.'.png');
}

$src=new Imagick($src);
$mask=new Imagick($mask);
$src->setImageMatte(1);
$src->compositeImage($mask, Imagick::COMPOSITE_DSTIN, 0, 0);

$src->writeImage('cache/'.$md5.'.png');

header('Content-Type: image/png');
echo $src;
