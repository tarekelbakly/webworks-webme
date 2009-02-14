<?php
include('header.php');
echo '<h1>'.__('Polls').'</h1>';
echo admin_menu(array(
	'New'=>'polls.php?action=newPoll',
	'View All'=>'polls.php',
));

echo '<div id="hasleftmenu">';
$edit=($action==__('editPoll'))?1:0;
switch($action){
	case 'deletePoll': // {
		if($id)include('polls/actions.delete.php');
		include('polls/showitems.php');
		break;
	// }
	case __('Edit Poll'): // {
		include('polls/actions.edit.php');
		include('polls/forms.php');
		break;
	// }
	case __('Create Poll'): // {
		include('polls/actions.new.php');
		include('polls/forms.php');
		break;
	// }
	case 'newPoll':case 'editPoll': // {
		include('polls/forms.php');
		break;
	// }
	default: // {
		include('polls/showitems.php');
	// }
}
echo '</div>';
include('footer.php');
