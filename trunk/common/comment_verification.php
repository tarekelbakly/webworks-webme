<?php
require('../common.php');
if(!isset($_GET['hash']) || !isset($_GET['email']))die('missing value in GET string');

$r=dbRow("select * from comments where email='".addslashes($_GET['email'])."' and verificationhash='".addslashes($_GET['hash'])."'");
if(!count($r))die('that hash and email combination does not exist');
setcookie('comment_verification',$_GET['email'].'|'.$_GET['hash'],time()+3600*24*365);
dbQuery("update comments set isvalid=1 where email='".addslashes($_GET['email'])."' and verificationhash='".addslashes($_GET['hash'])."'");
redirect('/index.php?pageid='.$r['objectid']);
