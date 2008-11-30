<?php
$time_start=microtime(true);
// { common variables and functions
include_once('common.php');
if(!isset($DBVARS['version']) || $DBVARS['version']<1)redirect('upgrades/upgrade.php');
// }
echo 'ok... tune in for the next exciting episode!';
