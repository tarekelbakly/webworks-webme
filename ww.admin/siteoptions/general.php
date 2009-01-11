<?php
echo '<h2>'.__('General').'</h2>';
// { handle actions
if($action=='Save'){
	$DBVARS['site_title']=$_REQUEST['site_title'];
	$DBVARS['site_subtitle']=$_REQUEST['site_subtitle'];
	config_rewrite();
	echo '<em>'.__('options updated').'</em>';
}
// }
// { form
echo '<form action="siteoptions.php?page=general"><table>';
echo '<tr><th>Website Title</th><td><input name="site_title" value="'.htmlspecialchars($DBVARS['site_title']).'" /></td></tr>';
echo '<tr><th>Website Subtitle</th><td><input name="site_subtitle" value="'.htmlspecialchars($DBVARS['site_subtitle']).'" /></td></tr>';
echo '</table><input type="submit" name="action" value="Save" /></form>';
// }
