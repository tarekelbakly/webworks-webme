<?php
require_once KFM_BASE_PATH.'/api/api.php';
require_once KFM_BASE_PATH.'/initialise.php';
$plugin=array(
	'name' => 'Image Gallery',
	'admin' => array(
		'page_type' => 'image_gallery_admin_page_form'
	),
	'frontend' => array(
		'page_type' => 'image_gallery_frontend'
	)
);
function image_gallery_get_subdirs($base,$dir){
	$arr=array();
	$D=new DirectoryIterator($base.$dir);
	$ds=array();
	foreach($D as $dname){
		$d=$dname.'';
		if($d{0}=='.')continue;
		if(!is_dir($base.$dir.'/'.$d))continue;
		$ds[]=$d;
	}
	asort($ds);
	foreach($ds as $d){
		$arr[]=$dir.'/'.$d;
		$arr=array_merge($arr,image_gallery_get_subdirs($base,$dir.'/'.$d));
	}
	return $arr;
}
function image_gallery_admin_page_form($page,$vars){
	$gvars=array(
		'image_gallery_directory'=>'/',
		'image_gallery_x'=>3,
		'image_gallery_y'=>2
	);
	foreach($gvars as $n=>$v)if(isset($vars[$n]))$gvars[$n]=$vars[$n];
	$cssurl=false;
	$c='<div class="tabs">';
	// { header
	$c.='<div class="tabPage"><h2>Header</h2>';
	$c.=fckeditor('body',$page['body'],0,$cssurl);
	$c.='</div>';
	// }
	// { gallery details
	$c.='<div class="tabPage"><h2>Gallery Details</h2>';
	$c.='<table><tr><th>Image Directory</th><td><select name="page_vars[image_gallery_directory]"><option value="/">/</option>';
	foreach(image_gallery_get_subdirs(USERBASE.'f','') as $d){
		$c.='<option value="'.htmlspecialchars($d).'"';
		if($d==@$gvars['image_gallery_directory'])$c.=' selected="selected"';
		$c.='>'.htmlspecialchars($d).'</option>';
	}
	$c.='</select></td>';
	$c.='<th>'.__('Columns').'</th><td><input name="page_vars[image_gallery_x]" value="'.(int)$gvars['image_gallery_x'].'" /></td>';
	$c.='<th>'.__('Rows').'</th><td><input name="page_vars[image_gallery_y]" value="'.(int)$gvars['image_gallery_y'].'" /></td>';
	$c.='</tr></table>';
	$c.='</div>';
	// }
	$c.='</div>';
	return $c;
}
function image_gallery_frontend($PAGEDATA){
	require dirname(__FILE__).'/frontend/show.php';
	return image_gallery_show($PAGEDATA);
}
