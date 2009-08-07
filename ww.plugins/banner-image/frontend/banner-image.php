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
		$b=dbRow('select * from banners_pages,banners_images where pageid='.$PAGEDATA->id.' and bannerid=id order by rand() limit 1');
	}
	if(!isset($b) || !count($b)){
		$b=dbRow('select * from banners_images where !pages order by rand() limit 1');
	}
	if(count($b)){
		if($b['type']==1){
			$banner=$b['html'];
		}
		else $banner=banner_image_getImgHTML($b['id'],true);
	}
	else{
		if(@$vars['default'])$banner=$vars['default'];
		else $banner='';
	}
	if(!$banner)return '';
	return '<style type="text/css">#banner{background:none}</style>'.$banner;
}
