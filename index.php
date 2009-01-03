<?php
// { common variables and functions
include_once('common.php');
if(!isset($DBVARS['version']) || $DBVARS['version']<6)redirect('upgrades/upgrade.php');
// }
echo 'ok... tune in for the next exciting episode!';
