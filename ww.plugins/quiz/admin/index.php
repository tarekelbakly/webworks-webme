<?php
	$menuItems= array ();
	$quizzes= dbAll("SELECT DISTINCT name, quiz_quizzes.id FROM quiz_quizzes,quiz_questions");
	$pageQuizzes= dbAll("SELECT name,id from quiz_quizzes LIMIT 0,15");
	foreach ($pageQuizzes as $quiz) {
		$menuItems[$quiz['name']]= $_url.'&amp;action=editQuiz&amp;id='.$quiz['id'];
	}
	$menuItems['New Quiz']= $_url.'&amp;action=newQuiz';
	if (count($quizzes)>count($pageQuizzes)){
		$menuItems['More Quizzes']= $_url;
	}
	echo admin_menu (
		$menuItems
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
			// {The javascript to display the table
			echo '<script>';
			echo '$(document).ready(function(){';
			echo '$(\'#quizTable\').dataTable();';
			echo '});';
			echo '</script>';
			// }
			// { The quiz Table
			echo '<div id="quiz-table-wrapper" style="width:400px"><table id="quizTable" style="width:100%">';
			echo '<thead>';
			echo '<tr>';
			echo '<th>Name</th>';
			echo '<th>&nbsp;</th>';
			echo '<th>&nbsp</th>';
			echo '</tr></thead>';
			echo '<tbody>';
			foreach ($quizzes as $quiz) {
				echo '<tr>';
				echo '<td>'.htmlspecialchars($quiz['name']).'</td>';
				$quiz['id']=addslashes($quiz['id']);
				echo '<td><a href= "'.$_url.'&amp;action=editQuiz&amp;id='.$quiz['id'].'">edit</a></td>';
				echo '<td><a href="'.$_url.'&amp;action=deleteQuiz&amp;id='.$quiz['id'].'"'
					.' onclick="return confirm(\'are you sure you want to delete this?\');">x</a></td>';
				echo '</tr>';
			}
			echo '</tbody></table></div>';
			// }
		// }
	}
	echo '</div>';
