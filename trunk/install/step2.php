<?php
require 'header.php';

if(!$_SESSION['db_vars']['passed']){ // user shouldn't be here
	header('Location: /install/step1.php');
	exit;
}

// DB installation goes here...

$_SESSION['db_vars']['db_installed']=1;
echo '<script type="text/javascript">document.location="/install/step3.php";</script>';
echo '<p>Database installed. Please <a href="step3.php">click here to proceed</a>.</p>';
