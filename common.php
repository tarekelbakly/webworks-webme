<?php
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
