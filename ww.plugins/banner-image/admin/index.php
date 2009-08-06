<?php
/*
	Webme Banner Image Plugin
	File: admin/index.php
	Developers:  Conor Mac Aoidh  http://macaoidh.name/
							 Kae Verens       http://verens.com/
	Report Bugs: kae@verens.com, conor@macaoidh.name
*/

$banner_image_types=array('jpg','gif','png');
if(isset($_GET['delete_banner']) && (int)$_GET['delete_banner']){
	$id=(int)$_GET['delete_banner'];
	dbQuery("delete from banners_images where id=$id");
	dbQuery("delete from banners_pages where bannerid=$id");
	@unlink(USERBASE.'f/skin_files/banner-image/'.$id.'.png');
	$n=USERBASE.'f/skin_files/banner-image/'.$id.'_*';
	`rm -fr $n`;
	$updated='Banner Deleted';
}
if(isset($_POST['save_banner'])){
	$id=(int)@$_POST['id'];
	$pages=@$_POST['pages_'.$id];
	$sql='set type='.((int)@$_POST['type']).',html="'.addslashes(@$_POST['html_'.$id]).'",pages='.(count($pages)?1:0);
	if($id){
		dbQuery("update banners_images $sql where id=$id");
	}
	else{
		dbQuery("insert into banners_images $sql");
		$id=dbOne('select last_insert_id() as id','id');
	}
	if(isset($_FILES['banner-image']) && file_exists($_FILES['banner-image']['tmp_name'])){
		$tmpname=addslashes($_FILES['banner-image']['tmp_name']);
		$type=preg_replace('/.*\./','',$_FILES['banner-image']['name']);
		if(in_array(strtolower($type),$banner_image_types)){
			$newdir=USERBASE.'f/skin_files/banner-image';
			`convert "$tmpname" "$newdir/$id.$type"`;
		}
	}
	dbQuery("delete from banners_pages where bannerid=$id");
	if(is_array($pages))foreach($pages as $k=>$v)dbQuery('insert into banners_pages set pageid='.((int)$v).",bannerid=$id");
	$updated='Banner Saved';
}

if(isset($updated)) echo '<em>'.$updated.'</em>';
if(!is_dir(USERBASE.'f/skin_files'))mkdir(USERBASE.'f/skin_files');
if(!is_dir(USERBASE.'f/skin_files/banner-image'))mkdir(USERBASE.'f/skin_files/banner-image');
$images=dbAll('select * from banners_images');

$num_images=0;
function banner_image_selectkiddies($i=0,$n=1,$s=array(),$id=0,$prefix=''){
	$q=dbAll('select name,id from pages where parent="'.$i.'" and id!="'.$id.'" order by ord,name');
	if(count($q)<1)return;
	foreach($q as $r){
		if($r['id']!=''){
			echo '<option value="'.$r['id'].'" title="'.htmlspecialchars($r['name']).'"';
			echo(in_array($r['id'],$s))?' selected="selected">':'>';
			for($j=0;$j<$n;$j++)echo '&nbsp;';
			$name=$r['name'];
			echo htmlspecialchars($prefix.$name).'</option>';
			banner_image_selectkiddies($r['id'],$n+1,$s,$id,$name.' > ');
		}
	}
}
function banner_image_getImgHTML($id){
	global $banner_image_types;
	$type='';
	foreach($banner_image_types as $t)if(file_exists(USERBASE.'f/skin_files/banner-image/'.$id.'.'.$t))$type=$t;
	if(!$type)return 'no image uploaded';
	return '<img src="/f/skin_files/banner-image/'.$id.'.'.$type.'" />';
}
function banner_image_drawForm($image=array()){
	if(!count($image))$image=array('id'=>0,'html'=>'','type'=>0);
	global $num_images;
	echo '<form method="post" enctype="multipart/form-data"><input type="hidden" name="id" value="',(int)$image['id'],'" /><table width="90%"><tr>';
	// { image/HTML selection
	echo '<td style="width:80px"><select name="type" id="type_',$num_images,'"><option value="0">Image</option><option value="1"',($image['type']==1?' selected="selected"':''),'>HTML</option></select></td>';
	// }
	// { show image form
	echo '<td><div id="banner_image_img_',$num_images,'" style="display:',($image['type']==1?'none':'block'),'"><input type="file" name="banner-image" /><br />',
		banner_image_getImgHTML($image['id']),'</div>';
	// }
	// { show HTML form
	echo '<div id="banner_image_html_',$num_images,'" style="display:',($image['type']==0?'none':'block'),'">',fckeditor('html_'.$image['id'],$image['html'],0,'',180),'</div></td>';
	// }
	// { what pages should this be applied to
	echo '<td style="width:220px">pages the banner should be active on. select "none" to show on all pages<br /><select name="pages_',$image['id'],'[]" multiple="multiple" style="max-width:200px;height:100px">';
	$ps=dbAll('select * from banners_pages where bannerid='.$image['id']);
	$pages=array();
	foreach($ps as $p)$pages[]=$p['pageid'];
	banner_image_selectkiddies(0,1,$pages);
	echo '</select></td>';
	// }
	// { show submit button and end form
	echo '<td style="width:80px"><input type="submit" name="save_banner" value="',__('Update'),'" /><br /><br /><br /><a href="./plugin.php?_plugin=banner-image&delete_banner='.$image['id'].'" onclick="return confirm(\'are you sure you want to remove this banner?\');" title="remove banner">[x]</a></td></tr></table></form>';
	// }
	$num_images++;
}
foreach($images as $image){
	banner_image_drawForm($image);
}
banner_image_drawForm();
echo '<script type="text/javascript" src="http://inlinemultiselect.googlecode.com/files/jquery.inlinemultiselect.min.js"></script>';
echo '<script src="/ww.plugins/banner-image/j/admin.js"></script>';
