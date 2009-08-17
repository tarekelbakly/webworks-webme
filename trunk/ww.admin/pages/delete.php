<?php
$id=(int)$_REQUEST['id'];
if(!$id)exit;
require '../../ww.incs/basics.php';
require 'pages.funcs.php';
$no_echo_on_success=true;
require 'pages.action.delete.php';
