<?php
require('../../common.php');
require('../../common/kaejax.php');
function ajax_sortabletable_reorder($parent,$order){
	$x='';
	if(is_array($order))foreach($order as $k=>$v){
		dbQuery('update pages set ord="'.$k.'" where id="'.$v.'"');
		$x.='update pages set ord="'.$k.'" where id="'.$v.'"'."\n";
	}
	return array($parent,0);
}
kaejax_export('ajax_sortabletable_reorder');
kaejax_handle_client_request();
kaejax_show_javascript();
echo file_get_contents('ajax_sortabletable.js');
