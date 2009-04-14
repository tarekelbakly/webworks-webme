<?php
if($id){
	if(dbQuery('select id from forms_saved where id="'.$id.'"')->numRows()){
		dbQuery('delete from forms_saved where id="'.$id.'"');
		dbQuery('delete from forms_saved_values where forms_saved_id="'.$id.'"');
		echo '<em>Saved Form deleted</em>';
	}
	else echo '<em>That saved form no longer exists.</em>';
}
