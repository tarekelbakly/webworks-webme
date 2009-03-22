<?php
echo '<h2>'.__('General').'</h2>';
// { handle actions
if($action=='Save'){
	$DBVARS['site_title']=$_REQUEST['site_title'];
	$DBVARS['site_subtitle']=$_REQUEST['site_subtitle'];
	if(isset($_FILES['site_logo']) && file_exists($_FILES['site_logo']['tmp_name'])){
		$tmpname=addslashes($_FILES['site_logo']['tmp_name']);
		$newname=USERBASE.'/f/skin_files/logo.png';
		mkdir(USERBASE.'/f/skin_files');
		`convert "$tmpname" -geometry 320x320 "$newname"`;
	}
	config_rewrite();
	echo '<em>'.__('options updated').'</em>';
}
// }
// { form
echo '<form method="post" action="siteoptions.php?page=general" enctype="multipart/form-data"><input type="hidden" name="MAX_FILE_SIZE" value="9999999" /><table>';
echo '<tr><th>Website Title</th><td><input name="site_title" value="'.htmlspecialchars($DBVARS['site_title']).'" /></td></tr>';
echo '<tr><th>Website Subtitle</th><td><input name="site_subtitle" value="'.htmlspecialchars($DBVARS['site_subtitle']).'" /></td></tr>';
echo '<tr><th>Logo</th><td><input type="file" name="site_logo" />',file_exists(USERBASE.'f/skin_files/logo.png')?'<img src="/f/skin_files/logo.png" />':'','</td></tr>';
echo '</table><input type="submit" name="action" value="Save" /></form>';
// }
