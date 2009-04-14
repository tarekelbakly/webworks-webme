<?php
if(!$start)$start=0;
$forms_id=getVar('forms_id',0);
echo '<h3>Existing Forms</h3>';

$end=dbQuery('select id from forms')->numRows();
$q=dbAll('select date_created,id from forms_saved where forms_id='.$forms_id.' order by date_created desc limit '.$start.',20');
if(count($q)){
		echo '<table><tr><th>Date Received</th>';
		if($start)echo '|<a href="'.$PHP_SELF.'?show_items=true&amp;start='.(($start>19)?$start-20:0).'">Prev</a>|';
		if($start+20<=$end)echo	'|<a href="'.$PHP_SELF.'?show_items=true&amp;start='.($start+20).'">Next</a>|';
		echo '</th></tr>';
		foreach($q as $r){
			echo '<tr>';
			echo '<td>'.$r['date_created'].'</td>';
			echo '<td>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?action=view&amp;id='.$r['id'].'&amp;start='.$start.'&amp;forms_id='.$forms_id.'">view</a> ';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?action=delete&amp;id='.$r['id'].'&amp;start='.$start.'&amp;forms_id='.$forms_id.'" onclick="return confirm(\'Are you sure?\');">x</a>';
			echo '</td></tr>';
		}
		echo '</table>';
}
else{
		echo '<em>none yet</em>';
}
