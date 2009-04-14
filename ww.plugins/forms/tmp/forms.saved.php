<?php
include('header.php');
if($USERDATA['accesspermissions']&ACL_FORMS){
	# user has rights for this page
	echo '<h1>Forms - saved data</h1>';
	include('forms/forms.menu.php');
	echo '<div id="hasleftmenu">';
	switch($action){
		case 'delete': {
			include('forms/forms.saved.action.delete.php');
			include('forms/forms.saved.showitems.php');
			break;
		}
		case 'view': {
			include('forms/forms.saved.view.php');
			break;
		}
		default: {
			include('forms/forms.saved.showitems.php');
		}
	}
	echo '</div>';
}else{
	echo '<em>You do not have rights for this area.</em>';
}
include('footer.php');
