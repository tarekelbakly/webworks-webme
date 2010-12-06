<?php
echo '<h2>'.__('General').'</h2>';
// { handle actions
if($action=='Save'){
	$DBVARS['site_title']=$_REQUEST['site_title'];
	$DBVARS['site_subtitle']=$_REQUEST['site_subtitle'];
	if(isset($_FILES['site_favicon']) && file_exists($_FILES['site_favicon']['tmp_name'])){
		$tmpname=addslashes($_FILES['site_favicon']['tmp_name']);
		$newdir=USERBASE.'/f/skin_files';
		mkdir(USERBASE.'/f/skin_files');
		`rm -fr "$newdir"/favicon-*`;
		$from=addslashes($_FILES['site_favicon']['tmp_name']);
		$to=addslashes($newdir.'/favicon.ico');
		`convert "$from" -resize 32x32 "to"`;
	}
	if(isset($_FILES['site_logo']) && file_exists($_FILES['site_logo']['tmp_name'])){
		$tmpname=addslashes($_FILES['site_logo']['tmp_name']);
		$newdir=USERBASE.'/f/skin_files';
		mkdir(USERBASE.'/f/skin_files');
		`rm -fr "$newdir"/logo-*`;
		move_uploaded_file($_FILES['site_logo']['tmp_name'], $newdir.'/logo.png');
	}
	$pageLengthLimit = $_REQUEST['site_page_length_limit'];
	if (!empty($pageLengthLimit)&&is_numeric($pageLengthLimit)) {
		$DBVARS['site_page_length_limit'] = $pageLengthLimit;
	}
	elseif (isset($DBVARS['site_page_length_limit'])) {
		unset($DBVARS['site_page_length_limit']);
	}
	config_rewrite();
	echo '<em>'.__('options updated').'</em>';
}
if($action=='remove_logo'){
	unlink(USERBASE.'/f/skin_files/logo.png');
}
// }
// { form
echo '<form method="post" action="siteoptions.php?page=general" enctype="multipart/form-data"><input type="hidden" name="MAX_FILE_SIZE" value="9999999" /><table>';
echo '<tr><th>Website Title</th><td><input name="site_title" value="'.htmlspecialchars($DBVARS['site_title']).'" /></td></tr>';
echo '<tr><th>Website Subtitle</th><td><input name="site_subtitle" value="'.htmlspecialchars($DBVARS['site_subtitle']).'" /></td></tr>';
// { logo
echo '<tr><th>Logo</th><td><input type="file" name="site_logo" /><br />';
if(file_exists(USERBASE.'f/skin_files/logo.png')){
	echo '<img src="/f/skin_files/logo.png?rand='.mt_rand(0,9999).'" /><a href="/ww.admin/siteoptions.php?action=remove_logo" onclick="return confirm(\'are you sure you want to remove the logo?\')" title="remove logo">[x]</a>';
}
echo '</td></tr>';
// }
// { favicon
echo '<tr><th>Favicon</th><td><input type="file" name="site_favicon" /><br />';
if(file_exists(USERBASE.'f/skin_files/favicon.png')){
	echo '<img src="/f/skin_files/favicon.png?rand='.mt_rand(0,9999).'" /><a href="/ww.admin/siteoptions.php?action=remove_favicon" onclick="return confirm(\'are you sure you want to remove the favicon?\')" title="remove favicon">[x]</a>';
}
echo '</td></tr>';
// }
echo '<th>Page Length Limit</th>';
echo '<td><input type="text" name="site_page_length_limit"';
if (isset($DBVARS['site_page_length_limit'])) {
	echo ' value="'.$DBVARS['site_page_length_limit'].'"';
}
echo ' /></td></tr>';
echo '</table><input type="submit" name="action" value="Save" /></form>';
// }
