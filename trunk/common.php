<?php
function redirect($addr){
	echo '<html><head><script type="text/javascript">setTimeout(function(){document.location="'.$addr.'";},10);</script></head><body><noscript>you need javascript to use this site</noscript></body></html>';
	exit;
}
// { load config, or go into setup if it's not set
define('BASEDIR', dirname(__FILE__) . '/');
if (!file_exists(BASEDIR . '.private/config.php')) {
	echo '<html><body><p>No configuration file found</p>';
	if(file_exists('install/index.php'))echo '<p><a href="/install/">Click here to install</a></p>';
	else echo '<p><strong>Installation script also missing...</strong> please contact kae@webworks.ie if you think there\'s a problem.</p>';
	echo '</body></html>';
	exit;
}
require BASEDIR . '.private/config.php';
// }
// { connect to database
require_once 'MDB2.php';
$dsn='mysql://'.$DBVARS['username'].':'.$DBVARS['password'].'@'.$DBVARS['hostname'].'/'.$DBVARS['db_name'];
$db = &MDB2::connect($dsn);
if(Pear::isError($db)){
	echo '<p>Error connecting to database.</p><p>Please make sure the access details are correct, and that the server has the Pear packages MDB2 and MDB2_Driver_mysql installed.</p>';
	exit;
}
$db->setCharset('utf8');
$db->setFetchMode(MDB2_FETCHMODE_ASSOC);
// }
