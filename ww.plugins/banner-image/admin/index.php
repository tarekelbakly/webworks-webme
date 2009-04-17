<?php
/*
	Webme Banner Image Plugin v0.1
	File: admin/index.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/

if($_POST['delete_banner']){
	$file=USERBASE.'f/skin_files/banner.png';
	if(file_exists($file)) unlink($file);
	$updated='Image Deleted';
}
if($_POST['save_banner']){
        if(isset($_FILES['banner-image']) && file_exists($_FILES['banner-image']['tmp_name'])){
                $tmpname=addslashes($_FILES['banner-image']['tmp_name']);
                $newdir=USERBASE.'f/skin_files';
                mkdir(USERBASE.'f/skin_files');
                `rm -fr "$newdir"/banner* ; convert "$tmpname" "$newdir/banner.png"`;
        }
	else $updated='error';
	$updated='Image Saved';
}

if(isset($updated)) echo '<em>'.$updated.'</em>';

if(file_exists(USERBASE.'f/skin_files/banner.png')) $banner='<img src="/f/skin_files/banner.png?'.mt_rand().'"/>';

echo '
<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="MAX_FILE_SIZE" value="9999999" />
	<table>
		<tr><th>Banner:</th><td><input type="file" name="banner-image" /><br />';

if($banner) echo $banner;

echo '</td>

		<td style="color:blue">Upload a banner image that will appear in your banner area.</td></tr>
		<tr><td><input type="submit" name="save_banner" value="Save" /></td><td></td><td><input type="submit" value="Remove Image" name="delete_banner"/></td></tr>
	</table>
</form>';

