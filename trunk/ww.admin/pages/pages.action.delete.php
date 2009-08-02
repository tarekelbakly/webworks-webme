<?php
if(allowedToEditPage($id)){
	$r=dbRow("SELECT COUNT(id) AS pagecount FROM pages");
	if($r['pagecount']<2){
		echo '<em>'.__('Cannot delete page - there must always be at least one page.').'</em>';
	}
	else{
		$q=dbQuery('select parent from pages where id="'.$id.'"');
		if($q->rowCount()){
			$r=dbRow('select parent from pages where id="'.$id.'"');
			dbQuery('delete from page_vars where page_id="'.$id.'"');
			dbQuery('delete from pages where id="'.$id.'"');
			dbQuery('update pages set parent="'.$r['parent'].'" where parent="'.$id.'"');
			if(!isset($no_echo_on_success))echo '<em>'.__('A page has been deleted.').'</em>';
			cache_clear('menus');
			cache_clear('pages');
		}
		else{
			echo '<em>'.__('That page does not exist.').'</em>';
		}
	}
}
else{
	echo '<em>'.__('You do not have delete rights for this page.').'</em>';
}
