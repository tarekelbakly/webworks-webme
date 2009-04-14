<?
	$forms_id=getVar('forms_id');
	$r=dbRow('select * from forms_saved where id="'.$id.'"');
	echo '<h2>Form Entry: '.$r['date_created'].'</h2>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?forms_id='.$forms_id.'&amp;start='.$start.'">return to form entries</a>';
	$r2s=dbAll('select * from forms_saved_values where forms_saved_id='.$id);
	echo '<table id="forms_saved">';
	foreach($r2s as $r2){
		echo '<tr><th>'.htmlspecialchars($r2['name']).'</th><td>'.nl2br(htmlspecialchars($r2['value'])).'</td></tr>';
	}
	echo '</table>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?action=delete&amp;id='.$r['id'].'&amp;start='.$start.'&amp;forms_id='.$forms_id.'" onclick="return confirm(\'Are you sure?\');">delete this entry</a>';
?>
