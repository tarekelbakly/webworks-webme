<?php
  $dir= dirname(__FILE__);
  $isInvalidInput = false;
  echo '<script src="/ww.plugins/quiz/admin/quickDelete.js"></script>';
  echo '<script>';
  echo '$(function(){';
  echo '$("#tabs").tabs({';
	echo 'selected:';
	$action=$_GET['action'];
	if ((strcmp($action, 'editQuestion')==0)
		||(strcmp($action, 'newQuestion')==0)
			||((strcmp($action,'newQuiz')==0)&&isset($_POST['action']))) {
	 			 echo 1;
	}
	else {
	  echo 0;
	}
  echo '}';
  echo ');';
  echo '});';
  echo '$(function () {';
  	echo '$(".deleteLink").click(deleteItem);';
  echo '});';
  echo '</script>';
  echo '<h3>';
  if ($id) {
  	echo 'Edit Quiz';
	$id= addslashes($id);
	$quiz = dbAll ("SELECT * FROM quiz_quizzes WHERE quiz_quizzes.id='$id'");
  }
  else {
  	echo 'New Quiz';
  }
  echo '</h3>';
  echo '<form method="post">';
  echo '<div id="tabs">';
  echo '<ul>';
  	echo '<li><a href="#Overview">Overview</a>';
  	echo '<li><a href="#Questions">Questions</a>';
  echo '</ul>';
  //Main Tab{
  echo '<div id="Overview">';
  echo 'Title   ';
  echo '<input type="text" name="name"';
  if (isset($_POST['name'])) {
    echo ' value="'.stripslashes(htmlspecialchars($_POST['name'])).'"';
  }
  if ($id) {
    echo ' value="';
    foreach ($quiz as $q) {
		echo htmlspecialchars($q['name']);
		break;
    }
	echo '"';
  }
  echo ' />';
  echo '<br/>';
  echo 'Description';
  echo '<input type="text" name="description"';
  if (isset($_POST['description'])) {
    echo ' value="'.stripslashes(htmlspecialchars($_POST['description'])).'"';
  }
  if ($id) {
	echo ' value="';
	foreach ($quiz as $q) {
	  echo htmlspecialchars($q['description']);
	  break;
	}
	echo '"';
  }
  echo '/>';
  if (!isset($_GET['questionid'])&&!(isset($_POST['add']))){
  	echo '<br/>';
	echo '<input type="submit" name="action" value="';
	if ($id) {
		echo 'Edit Quiz';
	}	
	else {
		echo 'Add Quiz';
	}
	echo '"/>';
  }
  echo '</div>';//Ends the main tab}
  //Questions tab {
  echo '<div id="Questions">';
  if ($id) {
	$results=dbAll("SELECT * FROM quiz_questions WHERE quiz_id='".$id."'");
	echo '<ul id="questionLinks">';
	foreach ($results as $result) {
	  $questionID = $result['id'];
	  echo '<li id="'.$questionID.'">'.$result['question'];
	  echo '   <a href="'.$_url.'&amp;action=editQuestion&amp;questionid='.$questionID.'&amp;id='.$id.'">edit</a>';
	  echo '   <a href="#" class="deleteLink" class="deleteLink">x</a></li>';
	}
    echo '</ul>';
	$questionID=null;
	if($questionID||strcmp($action, 'newQuestion')!=0) {
		echo '<input type="button" value="Add New Question" name="add" onClick="parent.location=\''.$_url.'\&amp;action=newQuestion&amp;id='.$id.'\'" />';
	}
  } 
  echo '</div>';
  if (isset($_POST['action'])) {
      if (!empty($_POST['name'])) {
		$quizName=addslashes($_POST['name']);
		$quizTopic=addslashes( $_POST['description']);
		$result= dbAll("SELECT COUNT(id) FROM quiz_quizzes WHERE name = '$quizName'");
		foreach ($result as $r) {
			if (($r['COUNT(id)']>0)&&!$id) {//I have to get the id later so I need a unique name but I don't want the user to have to change the name every time they edit the quiz.
				echo 'Please choose a unique quiz name';
				$isInvalidInput= true;
			}
			else {
				if ($id) {
					dbQuery ("UPDATE quiz_quizzes SET name = '$quizName', description = '$quizTopic' WHERE id = '$id'");
				}
				else {
					dbQuery("INSERT INTO quiz_quizzes(name, description) VALUES('$quizName', '$quizTopic')");
					$result= dbAll("SELECT id FROM quiz_quizzes where name='$quizName'");
					foreach ($result as $r) {
						$id=$r['id'];
					}
				    $addString= addQuestion();
					echo '<script>';
					echo '$("#Questions").append('.json_encode($addString).');';
					echo '</script>';
				}
			}		
		}
  	}
  	else {
		echo "Please give your quiz a name";
		$isInvalidInput= true;
  	}
  }

  if (isset($_POST['questionAction'])) {
		$id = $_POST['quiz_id'];
		$topic= $_POST['topic'];
    	$question=addslashes($_POST['question']);
    	$answers=$_POST['answers'];
    	for($i=0; $i<count($answers); $i++) {
			$answers[$i]=addslashes($answers[$i]);
    	}
    	$correctAnswer = $_POST['isCorrect'];
    	if (empty($question)) {
			echo 'Please type a question';
    	}
    	elseif (empty($answers[0])||empty($answers[1])) {
			echo 'You need to provide at least two possible answers in fields 1 and 2';
    	}
    	elseif (!checkCorrectAnswer($answers, $correctAnswer)) {
			echo 'One of the answers must be marked as correct';
    	}
    	else {//Input is valid
			if (isset($_GET['questionid'])) {
				$questionID=$_GET['questionid'];
				dbQuery ("UPDATE quiz_questions SET question = '$question', topic = '$topic', answer1 = '$answers[0]', answer2 = '$answers[1]', answer3 = '$answers[2]', answer4 = '$answers[3]', correctAnswer = '$correctAnswer' WHERE id = '$questionID'");
			}	
			else {
				dbQuery("INSERT INTO quiz_questions(quiz_id, question, topic, answer1, answer2, answer3, answer4, correctAnswer) VALUES('$id', '$question', '$topic', '$answers[0]', '$answers[1]', '$answers[2]', '$answers[3]', '$correctAnswer')");
			}
    	}
    	$addString= addQuestion();
		echo '<script>';
		echo '$("#Questions").append('.json_encode($addString).');';
		echo '</script>';
  	}
  	echo '</div>';//Ends the tabs div
   	if (isset($_POST['add'])) {
		$questionID=null;
		$addString= addQuestion();
  		echo '<script>';
		echo '$("#Questions").append('.json_encode($addString).');';
		echo '</script>';
  	}
	if ((strcmp($action,'editQuestion')==0)||(strcmp($action, 'newQuestion')==0)) {
		$addString= addQuestion();
		echo '<script>';
		echo '$("#Questions").append('.json_encode($addString).');';
		echo '</script>';
	}
	echo '</form>';//End form

  	function checkCorrectAnswer ($array, $correctAnswer) {
  		//First check that a selection was made
    	$selectionIsValid=true;
   		if (($correctAnswer<0)||($correctAnswer>5)) {
      		$selectionIsValid=false;
    	}
  		// Check that there is an answer at the selection
    	elseif (empty($array[$correctAnswer-1])) {
      		$selectionIsValid=false;
    	}
    	return $selectionIsValid;
  	}

  	function addQuestion () {
     	global $id;
      	global $questionID;
      	global $question;
      	// { Question Tab
      	$returnString= '<div class="tabpage">';
      	$returnString= $returnString.'<h2>';
      	if (isset($_GET['questionid'])) {
  			$questionID= addslashes($_GET['questionid']);
  			$returnString= $returnString.'Edit Question';
			$question= dbAll("SELECT * FROM quiz_questions WHERE id='$questionID'");
      	}
      	else {
  			$returnString= $returnString.'New Question';
      	}
      	$returnString= $returnString.'</h2>';
      	$returnString= $returnString.'<input type="hidden" name="quiz_id" value="';
      	$returnString= $returnString.htmlspecialchars($id).'"/>';
      	if (isset($questionID)){
  			$returnString= $returnString.'<input type="hidden" name="question_id" value="';
  			$returnString= $returnString.htmlspecialchars($questionID).'"';
  			$returnString= $returnString.'"/>';
      	}
      	$returnString= $returnString.'<br/>';
      	$returnString= $returnString.'Question';
      	$returnString= $returnString.pad();
      	$returnString= $returnString.'<input type="text" name="question"';
      	if (isset($_POST['question'])) {
			$returnString= $returnString.'value="'.htmlspecialchars($_POST['question']).'"';
      	}
      	if (isset($questionID)) {
  			$returnString= $returnString.'value="';
			foreach ($question as $q) {
				$returnString= $returnString.htmlspecialchars($q['question']).'"';
			}
      	}
      	$returnString= $returnString.'/>';
      	$returnString= $returnString.pad();
      	$returnString= $returnString.'Topic';
      	$returnString= $returnString.'<input type="text" name="topic"'; 
      	if (isset($_POST['topic'])) {	
			$returnString= $returnString.'value="'.htmlspecialchars($_POST['topic']).'"';
      	}
      	if (isset($questionID)) {
  			$returnString= $returnString.'value="';
  			foreach ($question as $q) {
				$returnString= $returnString.$q['topic'];
  			}
			$returnString= $returnString.'"';
      	}
      	$returnString= $returnString.'/>';
      	$returnString= $returnString.'</div>';
      	// }
      	// { Answers Tab
      	$returnString= $returnString.'<div class="tabpage">';
      	$returnString= $returnString.'<h2>';
      	if (isset($questionID)) {
  			$returnString= $returnString.'Edit Answers';
      	}
      	else {
  			$returnString= $returnString.'New Answers';
      	}
      	$returnString= $returnString.'</h2>';
      	$returnString= $returnString.'<table>';
      	$returnString= $returnString.'<thead>';
      	$returnString= $returnString.'<tr>';
      	$returnString= $returnString.'<th>';
      	$returnString= $returnString.'Possible Answers';
      	$returnString= $returnString.'</th>';
      	$returnString= $returnString.'<th>';
      	$returnString= $returnString.'Correct Answer';
      	$returnString= $returnString.'</th>';
      	$returnString= $returnString.'</tr>';
      	$returnString= $returnString.'</thead>';
      	$returnString= $returnString.'<tbody>';
      	for ($i=0; $i<'4'; $i++) {
			$num=$i+1;
			$returnString= $returnString.'<tr>';
			$returnString= $returnString.addAnswer($num);
			$returnString= $returnString.'</tr>';
      	}
      	$returnString= $returnString.'</tbody>';
      	$returnString= $returnString.'</table>';
      	$returnString= $returnString.'</div>';
      	// }
      	$returnString= $returnString.'<input type="submit" name="questionAction" value="';
      	if (isset($questionID)) {
  			$returnString= $returnString.'Edit';
      	}
      	else {
  			$returnString= $returnString.'Add';
      	}
      	$returnString= $returnString.' Question"/>';
      	$returnString= $returnString.'</div>';//Ends the tabs div*/
      	return $returnString;
    }

	function addAnswer($num) {
  		global $questionID;
		global $question;
		$returnString= '<td>';
		$returnString= $returnString.'<input type="text" name="answers[]"';
		if (isset ($_POST['answers'])) {
	  		$answers = $_POST['answers'];
	  		$i = $num-1;
	  		if (!empty($answers[$i])) {
	    		$returnString= $returnString.' value="'.htmlspecialchars($answers[$i]).'"';
	  		}
		}
		if ($questionID) {
			$key= 'answer'.$num;
			$returnString= $returnString.'value="';
			foreach ($question as $q) {
				$returnString= $returnString.$q[$key];
				$returnString= $returnString.'"';
			}
		}
		$returnString= $returnString.'/>';
		$returnString= $returnString.pad();
		$returnString= $returnString.'</td>';
		$returnString= $returnString.'<td>';
		$returnString= $returnString.'<input type="radio" name="isCorrect" value="'.$num.'"';
		if ($questionID) {
			foreach ($question as $q) {
				$correctAnswer=$q['correctAnswer'];
				if ($num==$correctAnswer) {
					$returnString= $returnString.'checked';
				}
			}
		}
		$returnString= $returnString.'/>';
		$returnString= $returnString.'</td>';
		return $returnString;
	}
   
   	function pad () {
		return '   ';
  	}
