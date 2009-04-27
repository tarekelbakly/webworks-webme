<?php
/*
	Webme Banner Image Plugin v0.1
	File: frontend/banner-image.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/

function show_banner($vars){
	global $PAGEDATA;
	if($PAGEDATA->id){
		$b=dbRow('select * from banners_pages,banners_images where pageid='.$PAGEDATA->id.' and pageid=id order by rand() limit 1');
	}
	if(!isset($b) || !count($b))$b=dbRow('select * from banners_images where !pages order by rand() limit 1');
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
