<?php
require 'header.php';
require 'stats/lib.php';
echo '<h1>'.__('Website Statistics').'</h1>';

echo admin_menu(array(
	'Summary'=>'stats.php?page=summary'
));

echo '<div id="hasleftmenu">';
$page=@$_REQUEST['page'];
switch($page){
	default: // {
		include('stats/summary.php');
	// }
}
echo '</div>';
require 'footer.php';
