<?php
	$dir= dirname(__FILE__);
    require_once ($dir.'/QuizSession.php');
	function displayQuizInfo ($name, $topic, $id) {
		$returnString = $returnString.'<td>'.htmlspecialchars($name).'</td>';
		$returnString = $returnString.'<td>'.htmlspecialchars($topic).'</td>';
		$returnString = $returnString.'<td><button type="submit" value="'.htmlspecialchars($id).'" name="take">Take Quiz</button></td>';
		return $returnString;
	}

	function getPageHtml () {
		$displayString= $displayString.'<script src="/j/datatables/media/js/jquery.dataTables.js"></script>';
		$displayString= $displayString.'<link rel="stylesheet" type="text/css" href="/j/datatables/media/css/demo_table.css" />';
		$displayString= $displayString.'<style> * .dataTables_wrapper{clear:none;';
		$displayString= $displayString.'padding:10px;}</style>';
		// {The Script
		$displayString= $displayString.'<script>';
		$displayString= $displayString.'$(document).ready(function(){';
		$displayString= $displayString.'$(\'#quizzesFrontend\').dataTable();';
		$displayString= $displayString.'});';
		$displayString= $displayString.'</script>';
		// }
		$quizzes= dbAll("SELECT DISTINCT quiz_quizzes.id, name, quiz_quizzes.description FROM quiz_quizzes, quiz_questions WHERE quiz_quizzes.id=quiz_questions.quiz_id");
		$displayString= $displayString.'<form method="post">';
		$displayString= $displayString. '<table id="quizzesFrontend" style="width:100% postion:top">';
		$displayString= $displayString.'<thead><tr>';
		$displayString= $displayString.'<th>Name</th>';
		$displayString= $displayString.'<th>Description</th>';
		$displayString= $displayString.'<th>&nbsp</th>';
		$displayString= $displayString.'</tr></thead>';
		$displayString= $displayString.'<tbody>';
		foreach ($quizzes as $quiz) {
			$quizId= $quiz['quiz_quizzes.id'];
			$name = $quiz['name'];
			$topic= $quiz['description'];
			$id=$quiz['id'];
			$displayString= $displayString.'<tr>';
			$displayString= $displayString.displayQuizInfo($name, $topic, $id);
			$displayString= $displayString.'</tr>';
		}
		$displayString= $displayString.'</tbody></table>';
		$displayString= $displayString.'</form>';
		if (isset($_POST['take'])) {
			$id= $_POST['take'];
			$id= addSlashes($id);
		 	$quiz = new QuizSession($id, 10);
		 	$_SESSION['id']=$id;
		 	$quiz->chooseQuestions();
		 	$displayString = $quiz->getQuestionPageHtml();
		 }
		 if (isset($_POST['check'])) {
			$quiz = new QuizSession ($_SESSION['id'], 10);
		 	$displayString= $quiz->checkAnswers($_SESSION['questions'], $_POST);
		 }
		return $displayString;
	}
