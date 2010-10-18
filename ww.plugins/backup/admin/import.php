<?php

$errors=array();
if (isset($_POST['action']) && $_POST['action'] == 'submit') {
	$tmpdir='/tmp/webmeBackup-import-'.md5($_SERVER['HTTP_HOST'].microtime(true));
	mkdir($tmpdir);
	$uname=$_FILES['file']['tmp_name'];
	$password=addslashes($_POST['password']);
	`cd $tmpdir && unzip -P "$password" $uname`;
	if(!file_exists( $tmpdir.'/site' )) {
		echo '<em>unzipping failed. incorrect password?</em>';
	}
	else {
		$udir=USERBASE;

		echo 'extracting files...<br />';
		`cd $udir && rm -rf f && unzip $tmpdir/site/files.zip`;

		echo 'extracting themes...<br />';
		`cd $udir && rm -rf f && unzip $tmpdir/site/theme.zip`;

		echo 'extracting database...<br />';
		$dbbackup=json_decode(file_get_contents($tmpdir.'/site/db.json'));
		foreach($dbbackup as $name=>$vals){
			dbQuery('delete from '.addslashes($name));
			foreach ($vals as $row) {
				$parts=array();
				foreach($row as $key=>$val){
					$parts[]='`'.addslashes($key).'` = "'.addslashes($val).'" ';
				}
				$query='insert into `'.addslashes($name).'` set '.join(',', $parts);
				dbQuery($query);
			}
		}

		echo 'importing config file...<br />';
		$config=json_decode(file_get_contents($tmpdir.'/site/config.json'),true);
		$config['username']=$DBVARS['username'];
		$config['password']=$DBVARS['password'];
		$config['hostname']=$DBVARS['hostname'];
		$config['db_name']=$DBVARS['db_name'];
		$config['userbase']=$DBVARS['userbase'];
		$config['theme_dir']=$DBVARS['theme_dir'];
		$config['theme_dir_personal']=$DBVARS['theme_dir_personal'];
		$config['plugins']=(isset($config['plugins']) && $config['plugins']!='')?explode(',',$config['plugins']):array();
		$DBVARS=$config;
		config_rewrite();

		echo 'done<img style="width:1px;height:1px" src="./" /><p>Import completed.</p>';
		return;
	}
}

if(count($errors)){
	echo '<em>'.join('<br />',$errors).'</em>';
}
echo '<em>NOTE: uploading a backup will OVERWRITE your present website.</em>'
	.'<p>Please only upload if you are certain you need to!</p>'
	.'<p>Seriously! Back away now if you are AT ALL unsure of this.</p>'
	.'<form action="/ww.admin/plugin.php?_plugin=backup&amp;_page=import"'
	.' method="post" enctype="multipart/form-data" /><table>'
	.'<tr><th>Backup file</th><td><input type="file" name="file" /></td></tr>'
	.'<tr><th>Password</th><td><input name="password" /></td></tr>'
	.'<tr><th colspan="2"><input type="submit" name="action" value="submit" />'
	.'</td></tr>'
	.'</table></form>';
