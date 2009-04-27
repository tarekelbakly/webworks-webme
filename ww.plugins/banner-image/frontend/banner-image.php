<?php
/*
	Webme Banner Image Plugin v0.1
	File: frontend/banner-image.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/

function show_banner($vars){
	$b=dbRow('select * from banners_images order by rand() limit 1');
	if(count($b)){
		if($b['type']==1){
			$banner=$b['html'];
		}
		else $banner='<img src="/f/skin_files/banner-image/'.$b['id'].'.png" />';
	}
	else{
		if(@$vars['default'])$banner=$vars['default'];
		else $banner='';
	}
	return $banner;
}
