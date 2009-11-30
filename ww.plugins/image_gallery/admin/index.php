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
// { online store
if(isset($GLOBALS['PLUGINS']['online-store'])){
	$c.='<div class="tabPage"><h2>Online Store</h2><table>';
	// { for sale
	$c.='<tr><th>Are these images for sale?</th><td><select name="page_vars[image_gallery_forsale]"><option value="">No</option>';
	$c.='<option value="yes"';
	if(isset($vars['image_gallery_forsale']) && $vars['image_gallery_forsale']=='yes')$c.=' selected="selected"';
	$c.='>Yes</option></td></tr>';
	// }
	// { prices
	$c.='<tr><th>Prices</th><td>';
	$ps=array();
	for($i=0;isset($vars['image_gallery_prices_'.$i]);++$i){
		$price=preg_replace('/[^0-9.]/','',$vars['image_gallery_prices_'.$i]);
		if(!((float)$price))continue;
		$ps[]=array('description'=>$vars['image_gallery_pricedescs_'.$i],'price'=>(float)$price);
	}
	for($cnt=0;isset($ps[$cnt]);++$cnt){
		$c.='<input class="medium" name="page_vars[image_gallery_pricedescs_'.$cnt.']" value="'.htmlspecialchars($ps[$cnt]['description']).'" />'
				.'<input class="ig_price small" name="page_vars[image_gallery_prices_'.$cnt.']" value="'.$ps[$cnt]['price'].'" /><br />';
	}
	$c.='<input class="medium" name="page_vars[image_gallery_pricedescs_'.$cnt.']" value="description" /><input class="ig_price small" name="page_vars[image_gallery_prices_'.$cnt.']" value="0" /><br />';
	$c.='<a id="ig_prices_more" href="javascript:image_gallery_add_price()">[more]</a></td></tr>';
	// }
	$c.='</table><script>var ig_price_count='.$cnt.';</script></div>';
}
// }
$c.='</div>';
$c.='<script src="/ww.plugins/image_gallery/j/admin.js"></script>';
