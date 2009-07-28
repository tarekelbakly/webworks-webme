<?php
/*
	Webme News Plugin v0.1
	File: upgrade.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/

require SCRIPTBASE.'ww.incs/db.php';
if($version==0){
	$version='0.1';
}

$DBVARS[$pname.'|version']=$version;
config_rewrite();
?>
