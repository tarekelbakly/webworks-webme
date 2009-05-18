<?php
require '../ww.incs/basics.php';
require '../ww.incs/db.php';

if(!isset($_GET['s']))exit;
$s=addslashes($_GET['s']);
$l=dbOne("select long_url from short_urls where short_url='$s'",'long_url');
if($l)header('Location: '.$l);
else echo 'that url is obsolete, or incorrect';
