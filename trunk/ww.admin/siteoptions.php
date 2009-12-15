<?php
require 'header.php';
echo '<h1>Site Options</h1>';

echo admin_menu(array(
	'General'=>'siteoptions.php?page=general',
	'Users'=>'siteoptions.php?page=users',
	'Themes'=>'siteoptions.php?page=themes',
	'Plugins'=>'siteoptions.php?page=plugins'
));

$page=admin_verifypage(
	array('general','users','themes','plugins'),
	'general',
	@$_REQUEST['page']
);

echo '<div id="hasleftmenu">';
require 'siteoptions/'.$page.'.php';
echo '</div>';
require 'footer.php';