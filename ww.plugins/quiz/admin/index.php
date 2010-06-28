<?php
	echo admin_menu (
		array(
			'New Quiz' => $_url.'&action=newQuiz'
		)
	);
	echo '<div class="has-left-menu">';
	$dir = dirname(__FILE__);
	switch ($action){
	  case 'editQuestion': // {What to do if the user wants to edit a question
	  	$questionID= $_GET['questionid'];
		require_once $dir.'/form.php';
		require_once $dir.'/formAddQuestion.php';
		break;
	  // }
	  case 'deleteQuestion'://{What to do if the user wants to delete a question
	  	$questionid= $_GET['questionid'];
	  	dbQuery("DELETE FROM quiz_questions WHERE id = '$questionid'");
		$results= dbAll ("SELECT quiz_id FROM quiz_questions WHERE id = '$id'");
		foreach ($results as $result) {
			$id=$result['quiz_id'];
		}
	//}
	  case 'newQuiz':
	  case 'editQuiz':// { What to do if the user want to enter a new quiz or edit a quiz
	    require_once $dir.'/form.php';
	    break;
	  // }
	  case 'deleteQuiz':// { What to do if the user wants to delete a quiz and confirms it
	  	dbQuery("DELETE FROM quiz_quizzes WHERE id = '$id'");
		dbQuery("DELETE FROM quiz_questions WHERE quiz_id = '$id'");
		//Not breaking because I want the quizzes to display
	  // }
		default:// { Display the quizzes
			$quizzes = dbAll("SELECT DISTINCT name, quiz_quizzes.id FROM quiz_quizzes,quiz_questions");
			foreach ($quizzes as $quiz) {
				echo $quiz['name'];
				echo '   <a href= "'.$_url.'&amp;action=editQuiz&amp;id='.$quiz['id'].'">edit</a>';
				echo '   <a href="'.$_url.'&amp;action=deleteQuiz&amp;id='.$quiz['id'].'"'
					.' onclick="return confirm(\'are you sure you want to delete this?\');">x</a><br/>';
			}
		// }
	  }
	  echo '</div>';
