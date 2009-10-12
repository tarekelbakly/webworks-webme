<?php

echo '<h2>Privacy</h2>';

echo '<table>';
// { restrict access to members of these group
echo '<tr><th>Page is viewable only by members of these groups:</th><td>';
$rs=dbAll('select * from groups order by name');
$restrict_to=array();
if(isset($page_vars['restrict_to_groups']) && $page_vars['restrict_to_groups']!='')$restrict_to=json_decode($page_vars['restrict_to_groups']);
foreach($rs as $r){
	echo '<input type="checkbox" name="page_vars[restrict_to_groups]['.$r['id'].']"';
	if(isset($restrict_to->$r['id']))echo ' checked="checked"';
	echo ' />'.htmlspecialchars($r['name']).'<br />';
}
echo '</td></tr>';
// }
echo '</table>';
