<?php
function image_gallery_show($PAGEDATA){
	$vars=$PAGEDATA->vars;
	// {
	global $plugins_to_load;
	$c=$PAGEDATA->body;
	$start=getVar('start');
	if(!$start)$start=0;
	$vars=array_merge(array(
		'image_gallery_directory'=>'/',
		'image_gallery_x'=>3,
		'image_gallery_y'=>2,
		'image_gallery_thumbsize'=>150,
		'image_gallery_captionlength'=>100,
		'image_gallery_hoverphoto'=>0,
		'image_gallery_type'=>'simple gallery'
	),$vars);
	$imagesPerPage=$vars['image_gallery_x']*$vars['image_gallery_y'];
	// }
	$dir_id=kfm_api_getDirectoryId(preg_replace('/^\//','',$vars['image_gallery_directory']));
	$images=kfm_loadFiles($dir_id);
	$images=$images['files'];
	$n=count($images);
	if($n){
		switch($vars['image_gallery_type']){
			case 'ad-gallery':
				require dirname(__FILE__).'/gallery-type-ad.php';
				break;
			default:
				require dirname(__FILE__).'/gallery-type-simple.php';
		}
		return $c;
	}else{
		return $c.'<em>gallery "'.$vars['image_gallery_directory'].'" not found.</em>';
	}
}
