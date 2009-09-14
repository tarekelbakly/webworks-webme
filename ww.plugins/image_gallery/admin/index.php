<?php
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
$gvars=array(
	'image_gallery_directory'    =>'/',
	'image_gallery_x'            =>3,
	'image_gallery_y'            =>2,
	'image_gallery_thumbsize'    =>150,
	'image_gallery_captionlength'=>100,
	'image_gallery_hoverphoto'   =>0,
	'image_gallery_type'         =>'ad-gallery'
);
foreach($gvars as $n=>$v)if(isset($vars[$n]))$gvars[$n]=$vars[$n];
$cssurl=false;
$c='<div class="tabs">';
// { header
$c.='<div class="tabPage"><h2>Header</h2>';
$c.=ckeditor('body',$page['body'],0,$cssurl);
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
$c.='<td colspan="2"><a style="background:#ff0;font-weight:bold;color:red;display:block;text-align:center;" href="#page_vars[image_gallery_directory]" onclick="javascript:window.open(\'/j/kfm/?startup_folder=\'+$(\'#image_gallery_directory\').attr(\'value\'),\'kfm\',\'modal,width=800,height=600\');">Manage Images</a></td></tr>';
$c.='<tr><th>'.__('Columns').'</th><td><input name="page_vars[image_gallery_x]" value="'.(int)$gvars['image_gallery_x'].'" /></td>';
// { gallery type
$c.='<th>'.__('Gallery Type').'</th><td><select name="page_vars[image_gallery_type]">';
$types=array('ad-gallery','simple gallery');
foreach($types as $t){
	$c.='<option value="'.$t.'"';
	if(isset($gvars['image_gallery_type']) && $gvars['image_gallery_type']==$t)$c.=' selected="selected"';
	$c.='>'.$t.'</option>';
}
$c.='</select></td></tr>';
// }
$c.='<tr><th>'.__('Rows').'</th><td><input name="page_vars[image_gallery_y]" value="'.(int)$gvars['image_gallery_y'].'" /></td></tr>';
$cl=(int)@$gvars['image_gallery_captionlength'];
$cl=$cl?$cl:100;
$c.='<tr><th>'.__('Caption Length').'</th><td><input name="page_vars[image_gallery_captionlength]" value="'.$cl.'" /></td></tr>';
$ts=(int)@$gvars['image_gallery_thumbsize'];
$ts=$ts?$ts:150;
$c.='<tr><th>'.__('Thumb Size').'</th><td><input name="page_vars[image_gallery_thumbsize]" value="'.$ts.'" /></td></tr>';
$c.='</table>';
$c.='</div>';
// }
$c.='</div>';
