<?php
echo '<h2>'.__('Themes').'</h2>';
// { handle actions
if($action=='set_theme'){
	if(is_dir(USERBASE . 'ww.skins/' . $_REQUEST['theme'])){
		$DBVARS['theme']=$_REQUEST['theme'];
		$_SESSION['viewing_skin']=$DBVARS['theme'];
		config_rewrite();
	}
}
// }
// { samples
	$dir=new DirectoryIterator(USERBASE . 'ww.skins');
	$themes_found=0;
	foreach($dir as $file){
		if(strpos($file,'.')===0)continue;
		$themes_found++;
		echo '<div style="width:250px;text-align:center;border:1px solid #000;margin:5px;height:200px;float:left;';
		if($file==$DBVARS['theme'])echo 'background:#ff0;';
		echo '"><a href="siteoptions.php?page=themes&amp;action=set_theme&amp;theme='.htmlspecialchars($file).'">';
		if(file_exists(USERBASE . 'ww.skins/' . $file . '/screenshot.png'))echo '<img src="/ww.skins/'.htmlspecialchars($file).'/screenshot.png" />';
		echo htmlspecialchars($file);
		echo '</a></div>';
	}
	if($themes_found==0){
		echo '<em>No themes found. Download a theme and unzip it into the /ww.skins/ directory.</em>';
	}
// }
