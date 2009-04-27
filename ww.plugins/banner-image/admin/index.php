<?php
/*
	Webme Banner Image Plugin
	File: admin/index.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/

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
	$sql='set type='.((int)@$_POST['type']).',html="'.addslashes(@$_POST['html']).'"';
	if((int)@$_POST['id']){
		$id=(int)@$_POST['id'];
		dbQuery("update banners_images $sql where id=$id");
	}
	else{
		dbQuery("insert into banners_images $sql");
		$id=dbOne('select last_insert_id() as id','id');
	}
	if(isset($_FILES['banner-image']) && file_exists($_FILES['banner-image']['tmp_name'])){
		$tmpname=addslashes($_FILES['banner-image']['tmp_name']);
		$newdir=USERBASE.'f/skin_files/banner-image';
		`convert "$tmpname" "$newdir/$id.png"`;
	}
	$updated='Banner Saved';
}

if(isset($updated)) echo '<em>'.$updated.'</em>';
if(!is_dir(USERBASE.'f/skin_files/banner-image'))mkdir(USERBASE.'f/skin_files/banner-image');
$images=dbAll('select * from banners_images');

$num_images=0;
function banner_image_drawForm($image=array()){
	if(!count($image))$image=array('id'=>0,'html'=>'','type'=>0);
	global $num_images;
	echo '<form method="post" enctype="multipart/form-data"><input type="hidden" name="id" value="',(int)$image['id'],'" /><table width="100%"><tr>';
	echo '<td><select name="type" id="type_',$num_images,'"><option value="0">Image</option><option value="1"',($image['type']==1?' selected="selected"':''),'>HTML</option></select></td>';
	// { show image form
	echo '<td><div id="banner_image_img_',$num_images,'" style="display:',($image['type']==1?'none':'block'),'"><input type="file" name="banner-image" /><br />',
		($image['id']?'<img src="/f/skin_files/banner-image/'.$image['id'].'.png" />':''),'</div>';
	// }
	// { show HTML form
	echo '<div id="banner_image_html_',$num_images,'" style="display:',($image['type']==0?'none':'block'),'">',fckeditor('html',$image['html'],0,'',180),'</div></td>';
	// }
	echo '<td><input type="submit" name="save_banner" value="',__('Update'),'" /><a href="./plugin.php?_plugin=banner-image&delete_banner='.$image['id'].'" onclick="return confirm(\'are you sure you want to remove this banner?\');" title="remove banner">[x]</a></td></tr></table></form>';
	$num_images++;
}
foreach($images as $image){
	banner_image_drawForm($image);
}
banner_image_drawForm();
echo '<script src="/ww.plugins/banner-image/j/admin.js"></script>';
