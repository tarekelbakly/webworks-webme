<?php
require 'header.php';

if(!$_SESSION['db_vars']['db_installed']){ // user shouldn't be here
	header('Location: /install/step2.php');
	exit;
}

if(!is_dir('../.private')){ // create config directory
	mkdir('../.private');
	if(!is_dir('../.private')){
		echo '<p><strong>Couldn\'t create /.private directory.</strong> Please either make the web root writable for the web server, or create the /.private directory and make it writable to the web server (then reload this page).</p>';
		exit;
	}
}

$config='<'."?php\n\$DBVARS=array(\n\t'username' => '".addslashes($_SESSION['db_vars']['username'])."',\n\t'password' => '".addslashes($_SESSION['db_vars']['password'])."',\n\t'hostname' => '".addslashes($_SESSION['db_vars']['hostname'])."',\n\t'db_name'  => '".addslashes($_SESSION['db_vars']['db_name'])."'\n);";

file_put_contents('../.private/config.php',$config);

if(!file_exists('../.private/config.php')){
	echo '<p><strong>Could not create /.private/config.php</strong>. Please make /.private/ writable for the web server, then reload this page.</p>';
	exit;
}

echo '<p><strong>Success!</strong> Your WebME installation is complete. Please <a href="/">click here</a> to go to the root of the site.</p>';

require 'footer.php';
