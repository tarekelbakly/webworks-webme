<?php

// assume no access
$allowed=false;

// if there's no restriction on this page, then $allowed=true
if(!isset($pagedata->vars['restrict_to_groups']) || $pagedata->vars['restrict_to_groups']=='')$allowed=true;

// if the user is not logged in, $allowed=false
else if(!isset($_SESSION['userdata']['groups']) || !count($_SESSION['userdata']['groups']))$allowed=false;

// if the user is in a group that has permission for this page, $allowed=true
else{
	$gs=json_decode($pagedata->vars['restrict_to_groups']);
	foreach($_SESSION['userdata']['groups'] as $k=>$id)if(isset($gs->$id))$allowed=true;
}
