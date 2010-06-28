<?php
// {
	function displayQuizInfo ($name, $topic, $id) {
		$returnString = $returnString. htmlspecialchars($name).'<br/>';
		$returnString = $returnString.htmlspecialchars($topic).'<br/>';
		$returnString = $returnString.'<button type="submit" value="'.htmlspecialchars($id).'" name="take">Take Quiz</button>';
		$returnString = $returnString.'<br/>';
		return $returnString;
	}

	function getPageHtml () {
		$dir= dirname(__FILE__);
		$displayString= '<form method="post">';
		//$resultsPerPage = 3;
		//$last;
		//$current = $_REQUEST['current'];
		//if (!isset($current)) {
		//$current=1;
		//$last = getNumberOfPages($resultsPerPage);
		//}
		//elseif ($current<1){
			//$current=1;
		//}
		//elseif ($current>$last) {
			//$current=$last;
		//}
		//$start = ($current-1)*$resultsPerPage;
		//$quizDisplayString;
		$quizzes= dbAll("SELECT DISTINCT quiz_quizzes.id, name, quiz_quizzes.description FROM quiz_quizzes, quiz_questions WHERE quiz_quizzes.id=quiz_questions.quiz_id");
		foreach ($quizzes as $quiz) {
			$quizId= $quiz['quiz_quizzes.id'];
			$name = $quiz['name'];
			$topic= $quiz['description'];
			$id=$quiz['id'];
			$displayString= $displayString.displayQuizInfo($name, $topic, $id);
		}
		$displayString= $displayString.'</form>';
			//$quizDisplayString = $quizDisplayString.'<br/>';
			//if ($current!=1) {
			//	$previous = $current-1;
			//	$quizDisplayString= $quizDisplayString.'<a href={$_SERVER[PHP_SELF]}?current=$previous>Previous</a>';
			//}
			//if ($current!=$last) {
				//$next= $current+1;
				//$quizDisplayString= $quizDisplayString.'<a href={$_SERVER[PHP_SELF]?current=$next>Next</a>';
			//}

		//}
		if (isset($_POST['take'])) {
		 include_once ($dir.'/QuizSession.php');
		 $id = $_POST['take'];
		 $quiz = new QuizSession($id, 10);
		 $_SESSION['id']=$id;
		 $quiz->chooseQuestions();
		 $displayString = $quiz->getQuestionPageHtml();
		 }
		 if (isset($_POST['check'])) {
		 	include_once ($dir.'/display.php');
			$quiz = new QuizSession ($_SESSION['id'], 10);
		 	$displayString= $quiz->checkAnswers($_SESSION['questions'], $_POST);
		 }
		return $displayString;
	}


	function getNumberOfPages ($itemsPerPage) {
		$all = dbAll ("SELECT DISTINCT quiz_quizzes.id, name, quiz_quizzes.topic FROM quiz_quizzes, quiz_questions WHERE quiz_quizzes.id=quiz_questions.quiz_id");
		$numResults= count($all);
		$numPages= ceil($numResults/$resultsPerPage);
		return $numPages;
	}

	
// }

