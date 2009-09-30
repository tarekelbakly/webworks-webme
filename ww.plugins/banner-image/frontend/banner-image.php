<?php
/*
	Webme Banner Image Plugin v0.1
	File: frontend/banner-image.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/

function show_banner($vars){
	if(!is_array($vars) && isset($vars->id) && $vars->id){
		$b=dbRow('select * from banners_images where id='.$vars->id);
		if(count($b) && !$b['html']){
			$b['html']=banner_image_getImgHTML($vars->id);
			dbQuery('update banners_pages set html="'.addslashes($b['html']).'" where id='.$vars->id);
		}
	}
	else if($GLOBALS['PAGEDATA']->id){
	echo '<!-- ';
		$b=dbRow('select * from banners_pages,banners_images where pageid='.$GLOBALS['PAGEDATA']->id.' and bannerid=id order by rand() limit 1');
		var_dump($b);
		echo ' -->';
		if(count($b) && !$b['html']){
			$b['html']=banner_image_getImgHTML($b['id']);
			dbQuery('update banners_pages set html="'.addslashes($b['html']).'" where id='.$b['id']);
		}
	}
	if(!isset($b) || !count($b)){
		$b=dbRow('select * from banners_images where !pages order by rand() limit 1');
	}
	if(count($b)){
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
