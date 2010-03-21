<?php
/*
	Webme Banner Image Plugin v0.1
	File: frontend/banner-image.php
	Developers:
		Conor Mac Aoidh  http://macaoidh.name/
		Kae Verens       http://verens.com/
	Report Bugs: kae@verens.com
*/

function show_banner($vars){
	if(!is_array($vars) && isset($vars->id) && $vars->id){
		$b=dbRow('select * from banners_images where id='.$vars->id);
		if($b && count($b) && !$b['html']){
			$b['html']=banner_image_getImgHTML($vars->id);
			dbQuery('update banners_pages set html="'.addslashes($b['html']).'" where id='.$vars->id);
		}
	}
	else if($GLOBALS['PAGEDATA']->id){
		$b=dbRow('select * from banners_pages,banners_images where pageid='.$GLOBALS['PAGEDATA']->id.' and bannerid=id order by rand() limit 1');
		if($b && count($b) && !$b['html']){
			$b['html']=banner_image_getImgHTML($b['id']);
			dbQuery('update banners_pages set html="'.addslashes($b['html']).'" where id='.$b['id']);
		}
	}
	if(!isset($b) || $b===false || !count($b)){
		$b=dbRow('select * from banners_images where !pages order by rand() limit 1');
	}
	if($b && count($b)){
		$banner=$b['html'];
		if(!$banner)$banner=banner_image_getImgHTML($vars->id);
	}
	if(!$banner){
		if(is_array($vars) && isset($vars['default']) && $vars['default'])$banner=$vars['default'];
		else $banner='';
	}
	if(!$banner)return '';
	return '<style type="text/css">#banner{background:none}</style>'.$banner;
}