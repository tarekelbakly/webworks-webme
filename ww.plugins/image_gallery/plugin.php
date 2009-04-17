<?php
require_once KFM_BASE_PATH.'/api/api.php';
require_once KFM_BASE_PATH.'/initialise.php';
$plugin=array(
	'name' => 'Image Gallery',
	'admin' => array(
		'page_type' => 'image_gallery_admin_page_form'
	),
	'description' => 'Allows a directory of images to be shown as a gallery.',
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
		'image_gallery_directory'    =>'/',
		'image_gallery_x'            =>3,
		'image_gallery_y'            =>2,
		'image_gallery_thumbsize'    =>150,
		'image_gallery_captionlength'=>100,
		'image_gallery_hoverphoto'   =>0
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
	$c.='<table><tr><th>Image Directory</th><td><select id="image_gallery_directory" name="page_vars[image_gallery_directory]"><option value="/">/</option>';
	foreach(image_gallery_get_subdirs(USERBASE.'f','') as $d){
		$c.='<option value="'.htmlspecialchars($d).'"';
		if($d==@$gvars['image_gallery_directory'])$c.=' selected="selected"';
		$c.='>'.htmlspecialchars($d).'</option>';
	}
	$c.='</select></td>';
	$c.='<td colspan="2"><a style="background:#ff0;font-weight:bold;color:red;display:block;text-align:center;" href="#page_vars[image_gallery_directory]" onclick="javascript:window.open(\'/j/'.FCKEDITOR.'/editor/plugins/kfm/?startup_folder=\'+$(\'#image_gallery_directory\').attr(\'value\'),\'kfm\',\'modal,width=800,height=600\');">Manage Images</a></td></tr>';
	$c.='<tr><th>'.__('Columns').'</th><td><input name="page_vars[image_gallery_x]" value="'.(int)$gvars['image_gallery_x'].'" /></td>';
	$c.='<th>'.__('Rows').'</th><td><input name="page_vars[image_gallery_y]" value="'.(int)$gvars['image_gallery_y'].'" /></td></tr>';
	$cl=(int)@$gvars['image_gallery_captionlength'];
	$cl=$cl?$cl:100;
	$c.='<tr><th>'.__('Caption Length').'</th><td><input name="page_vars[image_gallery_captionlength]" value="'.$cl.'" /></td>';
	$ts=(int)@$gvars['image_gallery_thumbsize'];
	$ts=$ts?$ts:150;
	$c.='<th>'.__('Thumb Size').'</th><td><input name="page_vars[image_gallery_thumbsize]" value="'.$ts.'" /></td></tr>';
	$c.='</table>';
	$c.='</div>';
	// }
	$c.='</div>';
	return $c;
}
function image_gallery_frontend($PAGEDATA){
	require dirname(__FILE__).'/frontend/show.php';
	return image_gallery_show($PAGEDATA);
}
