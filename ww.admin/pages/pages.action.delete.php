<?php
if(allowedToEditPage($id)){
	$r=dbRow("SELECT COUNT(id) AS pagecount FROM pages");
	if($r['pagecount']<2){
		echo '<em>'.__('Cannot delete page - there must always be at least one page.').'</em>';
	}
	else{
		$q=dbQuery('select parent from pages where id="'.$id.'"');
		if($q->numRows()){
			$r=$q->fetchRow();
			dbQuery('delete from page_vars where page_id="'.$id.'"');
			dbQuery('delete from pages where id="'.$id.'"');
			dbQuery('update pages set parent="'.$r['parent'].'" where parent="'.$id.'"');
			echo '<em>'.__('A page has been deleted.').'</em>';
		}
		else{
			echo '<em>'.__('That page does not exist.').'</em>';
		}
	}
}
else{
	echo '<em>'.__('You do not have delete rights for this page.').'</em>';
}
