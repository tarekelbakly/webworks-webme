<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/common.php';
require_once SCRIPTBASE.'common/kaejax.php';
function ig_getImages($dirId){
	$files=kfm_loadFiles($dirId);
	return $files['files'];
}
kaejax_export('ig_getImages');
kaejax_handle_client_request();
kaejax_show_javascript();
echo file_get_contents('image.gallery.js');
