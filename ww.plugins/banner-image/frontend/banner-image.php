<?php
/*
	Webme Banner Image Plugin v0.1
	File: frontend/banner-image.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/

function show_banner(){
	if(file_exists(USERBASE.'f/skin_files/banner.png')) $banner='<img src="/f/skin_files/banner.png"/>';
	else $banner='';
	return $banner;
}
?>
