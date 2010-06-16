<?php
if($version==0){ // rename plugin to "image-gallery"
	dbQuery('update pages set type="image-gallery" where type="image_gallery"');
	$key=array_search('image_gallery',$DBVARS['plugins']);
	unset($DBVARS['plugins'][$key]);
	$DBVARS['plugins'][]='image-gallery';
	config_rewrite();
	header('Location: '.$_SERVER['REQUEST_URI']);
	exit;
}
