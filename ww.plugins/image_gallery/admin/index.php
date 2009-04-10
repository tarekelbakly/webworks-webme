<?php
echo admin_menu(array(
	'New'=>$_url.'&action=newPoll',
	'View All'=>$_url
));

echo '<div id="hasleftmenu">';
$edit=($action==__('editPoll'))?1:0;
$dir=dirname(__FILE__);
switch($action){
	case 'deletePoll': // {
		if($id)include($dir.'/actions.delete.php');
		include($dir.'/showitems.php');
		break;
	// }
	case __('Edit Poll'): // {
		include($dir.'/actions.edit.php');
		include($dir.'/forms.php');
		break;
	// }
	case __('Create Poll'): // {
		include($dir.'/actions.new.php');
		include($dir.'/forms.php');
		break;
	// }
	case 'newPoll':case 'editPoll': // {
		include($dir.'/forms.php');
		break;
	// }
	default: // {
		include($dir.'/showitems.php');
	// }
}
echo '</div>';
