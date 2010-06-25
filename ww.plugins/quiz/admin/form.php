<?php
  $dir= dirname(__FILE__);
  $isInvalidInput = false;
  echo '<script>';
	echo '$(document).ready(function() {';
		echo '$("#tabs").tabs();';
	echo '});';
  echo '</script>';
  echo '<div class="has-left-menu">';
  echo '<h3>';
  if (!$id)
  	$id = $_POST['id'];
  if ($id) {
  	echo 'Edit Quiz';
	$quiz = dbAll ("SELECT * FROM quiz_quizzes, quiz_questions WHERE quiz_quizzes.id='$id' AND quiz_questions.quiz_id ='$id'");
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
    echo '</div>';//Ends the tabpage div}
  //Questions tab {
    echo '<div id="Questions">';
      if ($id) {
	$results=dbAll("SELECT * FROM quiz_questions WHERE quiz_id='".$id."'");
	echo '<ul>';
	foreach ($results as $result) {
	  $questionID = $result['id'];
	  echo '<li>'.$result['question'].'   <a href="'.$_url.'&amp;action=deleteQuestion&amp;questionID='.$questionID.'">x</a></li>';
	}
      echo '</ul>';
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
				}
				include ($dir.'/formAddQuestion.php');
			}
		}
		
      }
      else {
		echo "Please give your quiz a name";
		$isInvalidInput= true;
      }
    }

    if (isset($_POST['addQuestion'])) {
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
		include ($dir.'/formAddQuestion.php');
      }
      elseif (empty($answers[0])||empty($answers[1])) {
		echo 'You need to provide at least two possible answers in fields 1 and 2';
		include ($dir.'/formAddQuestion.php');
      }
      elseif (!checkCorrectAnswer($answers, $correctAnswer)) {
		echo 'One of the answers must be marked as correct';
		include ($dir.'/formAddQuestion.php');
      }
      else {//Input is valid
		dbQuery("INSERT INTO quiz_questions(quiz_id, question, topic, answer1, answer2, answer3, answer4, correctAnswer) VALUES('$id', '$question', '$topic', '$answers[0]', '$answers[1]', '$answers[2]', '$answers[3]', '$correctAnswer')");
		include ($dir.'/formAddQuestion.php'); 
      }
      
    }//
  echo '</div>';//Ends the tabs div
  if (!(isset($_POST['action']))||$isInvalidInput){
  	if (!isset($_POST['addQuestion'])){
    	echo '<input type="submit" name="action" value="';
		if ($id) {
			echo 'Edit Quiz';
		}
		else {
			echo 'Add Quiz';
		}
		echo'"/>';
	}
	if ($id && !isset($_POST['addQuestion'])) {
		echo '<input type="submit" name="add" value="Add Question"/>';
	}
  }
  if (isset($_POST['add'])) {
  	include ($dir.'/formAddQuestion.php');
  }
  echo '</form>';//End form
  echo '</div>';

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
