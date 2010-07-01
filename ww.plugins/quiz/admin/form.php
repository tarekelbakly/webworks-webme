<?php
  $dir= dirname(__FILE__);
  $isInvalidInput = false;
  echo '<script>';
	echo '$(document).ready(function() {';
		echo '$("#tabs").tabs();';
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
		echo'"/>';
	}
	echo '</div>';//Ends the tabpage div}
  	//Questions tab {
    echo '<div id="Questions">';
    if ($id) {
		$results=dbAll("SELECT * FROM quiz_questions WHERE quiz_id='".$id."'");
		echo '<ul>';
		foreach ($results as $result) {
	  		$questionID = $result['id'];
	  		echo '<li>'.$result['question'];
	  		echo '   <a href="'.$_url.'&amp;action=editQuestion&amp;questionid='.$questionID.'&amp;id='.$id.'">edit</a>';
	  		echo '   <a href="'.$_url.'&amp;action=deleteQuestion&amp;questionid='.$questionID.'&amp;id='.$id.'"'
				.' onclick="return confirm (\'Are you sure you want to delete this?\');">x</a></li>';
		}
    	echo '</ul>';
		$questionID=null;
		if($questionID||!isset($_POST['add'])) {
			echo '<input type="submit" value="Add New Question" name="add"/>';
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
						require_once $dir.'/formAddQuestion.php';
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
			echo '<script>';
			echo '#Questions.append($question);';
			echo '</script>';
			if (isset($_GET['questionid'])) {
				$questionID=$_GET['questionid'];
				dbQuery ("UPDATE quiz_questions SET question = '$question', topic = '$topic', answer1 = '$answers[0]', answer2 = '$answers[1]', answer3 = '$answers[2]', answer4 = '$answers[3]', correctAnswer = '$correctAnswer' WHERE id = '$questionID'");
			}	
			else {
				dbQuery("INSERT INTO quiz_questions(quiz_id, question, topic, answer1, answer2, answer3, answer4, correctAnswer) VALUES('$id', '$question', '$topic', '$answers[0]', '$answers[1]', '$answers[2]', '$answers[3]', '$correctAnswer')");
			}
    	}
    	require_once $dir.'/formAddQuestion.php';  
  	}
  	echo '</div>';//Ends the tabs div
   	if (isset($_POST['add'])) {
		$_GET['questionid']= null;
  		require_once ($dir.'/formAddQuestion.php');
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
