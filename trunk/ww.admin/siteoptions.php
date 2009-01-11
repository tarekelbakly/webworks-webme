<?php
require 'header.php';
echo '<h1>Site Options</h1>';

echo admin_menu(array(
	'General'=>'siteoptions.php?page=general',
	'Themes'=>'siteoptions.php?page=themes',
	'Localisation'=>'siteoptions.php?page=localisation'
));

$page=admin_verifypage(
	array('general','localisation','themes'),
	'general',
	$_REQUEST['page']
);

echo '<div id="hasleftmenu">';
require 'siteoptions/'.$page.'.php';
echo '</div>';
require 'footer.php';
