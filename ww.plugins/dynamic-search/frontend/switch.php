<?php
/*
	Webme Dynamic Search Plugin v0.1
	File: frontend/switch.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/

$SS=array();
$q=dbAll('select name,value from site_vars');
foreach($q as $r){
	$SS[$r['name']]=$r['value'];
}

include SCRIPTBASE.'ww.plugins/dynamic-search/frontend/search.php';

$sub=@$_GET['dynamic_search_submit'];

if($sub=='') include SCRIPTBASE.'ww.plugins/dynamic-search/frontend/display.php';
else include SCRIPTBASE.'ww.plugins/dynamic-search/frontend/results.php';
