<?php
	echo admin_menu (array(
			  	 'New Quiz' => $_url.'&action=new'
				)
			);
	$dir = dirname(__FILE__);
	switch ($action){
	  case 'deleteQuestion'://{
	  	dbQuery("DELETE FROM quiz_questions WHERE id = '$questionID'");
		$results= dbAll ("SELECT quiz_id FROM quiz_questions WHERE id = '$id'");
		foreach ($results as $result) {
			$id=$result['quiz_id'];
		}
		echo '<form>';
		echo '<input type="hidden" name="id" value="'.$id.'"/>';
		echo '</form>';
	//}
	  case 'new':
	  case 'edit'://{
	    include ($dir.'/form.php');
	    break;
	  //}
	  case 'delete'://{
	  	dbQuery("DELETE FROM quiz_quizzes WHERE id = '$id'");
		dbQuery("DELETE FROM quiz_questions WHERE quiz_id = '$id'");
		//Not breaking because I want the quizzes to display
	  //}
		default:
			$quizzes = dbAll("SELECT DISTINCT name, quiz_quizzes.id FROM quiz_quizzes,quiz_questions WHERE quiz_quizzes.id=quiz_questions.quiz_id");
			foreach ($quizzes as $quiz) {
				echo $quiz['name'].'   <a href= "'.$_url.'&amp;action=edit&amp;id='.$quiz['id'].'">edit</a>   <a href="'.$_url.'&amp;action=delete&amp;id='.$quiz['id'].'">   x</a><br/>';
			}
		//}
	  }
?>	
