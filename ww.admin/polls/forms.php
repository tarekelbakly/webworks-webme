<?php
$translation=0;
echo '<h3>'.($id?__('Edit Poll'):__('New Poll')).'</h3>';
if($id){
	$data=dbRow('select * from poll where id='.$id);
}
else $data=array('name'=>'','enabled'=>1,'body'=>'');
echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post">';
if($id)echo '<input type="hidden" name="id" value="'.$id.'" />';
echo '<div class="tabs">';
// { main details
	echo '<div class="tabPage"><h2>'.__('Main').'</h2>';
	echo '<table class="poll_creation_table" style="width:100%">';
	echo '<tr><th>'.__('Name').'</th><td><input name="name" value="'.htmlspecialchars($data['name']).'" /></td>';
	echo '<th>'.__('Enabled').'</th><td><select name="enabled"><option value="1">Yes</option><option value="0"';
	if($data['enabled']==0)echo ' selected="selected"';
	echo '">No</option></select></td></tr>';
	echo '<tr><th>Question</th><td colspan="3"><textarea class="fckeditor" name="body">'.htmlspecialchars($data['body']).'</textarea></td></tr>';
	echo '</table></div>';
// }
// { answers
	echo '<div class="tabPage"><h2>'.__('Answers').'</h2><table id="poll_answers" width="100%">';
	echo '<tr><th>Answer</th><th>Votes so far</th><th><a href="javascript:add_answer_row()">add answer</a></th></tr>';
	if($id){
		$answers=dbAll("select * from poll_answer where poll_id=$id order by num");
		foreach($answers as $answer){
			echo '<tr><td><input class="large" name="answers[]" value="'.htmlspecialchars($answer['answer']).'" /></td><td colspan="2">todo</td></tr>';
		}
	}
	echo '<tr><td><input class="large" name="answers[]" /></td><td colspan="2">&nbsp;</td></tr>';
	echo '</table>';
	echo '<script type="text/javascript">function add_answer_row(){$("#poll_answers > tbody").append("<tr><td><input class=\"large\" name=\"answers[]\" /></td><td colspan=\"2\">&nbsp;</td></tr>");}</script>';
	echo '</div>';
// }
echo '</div><input type="submit" name="action" value="'.($id?__('Edit Poll'):__('Create Poll')).'" /></form>';
