<?php
  echo '<form method="post">';
  // { Question Tab
  echo '<div class="tabpage">';
  echo '<h2>';
  if (isset($_GET['questionid'])) {
  	$questionID= addslashes($_GET['questionid']);
  	echo 'Edit Question';
	$question= dbAll("SELECT * FROM quiz_questions WHERE id='$questionID'");
  }
  else {
  	echo 'New Question';
  }
  echo '</h2>';
  echo '<input type="hidden" name="quiz_id" value="';
  echo htmlspecialchars($id).'"/>';
  if (isset($questionID)){
  	echo '<input type="hidden" name="question_id" value="';
  	echo htmlspecialchars($questionID).'"';
  	echo '"/>';
  }
  echo '<br/>';
  echo 'Question';
  pad();
  echo '<input type="text" name="question"';
  if (isset($_POST['question'])) {
	echo 'value="'.htmlspecialchars($_POST['question']).'"';
  }
  if (isset($questionID)) {
  	echo 'value="';
	foreach ($question as $q) {
		echo htmlspecialchars($q['question']).'"';
	}
  }
  echo '/>';
  pad();
  echo 'Topic';
  echo '<input type="text" name="topic"'; 
  if (isset($_POST['topic'])) {
	echo 'value="'.htmlspecialchars($_POST['topic']).'"';
  }
  if (isset($questionID)) {
  	echo 'value="';
  	foreach ($question as $q) {
		echo $q['topic'];
  	}
	echo '"';
  }
  echo '/>';
  echo '</div>';
  // }
  // { Answers Tab
  echo '<div class="tabpage">';
  echo '<h2>';
  if (isset($questionID)) {
  	echo 'Edit Answers';
  }
  else {
  	echo 'New Answers';
  }
  echo '</h2>';
  for ($i=0; $i<'4'; $i++) {
	$num=$i+1;
	echo 'Possible Answer '.$num.' ';
	addAnswer($num);
	echo '<br/>';
  }	 
  echo '</div>';
  // }
  echo '<input type="submit" name="questionAction" value="';
  if (isset($questionID)) {
  	echo 'Edit';
  }
  else {
  	echo 'Add';
  }
  echo ' Question"/>';
  echo '</div>';//Ends the tabs div
  echo '</form>';
 
  function addAnswer($num) {
  	global $questionID;
	global $question;
	echo '<input type="text" name="answers[]"';
	if (isset ($_POST['answers'])) {
	  $answers = $_POST['answers'];
	  $i = $num-1;
	  if (!empty($answers[$i])) {
	    echo ' value="'.htmlspecialchars($answers[$i]).'"';
	  }
	}
	if ($questionID) {
		$key= 'answer'.$num;
		echo 'value="';
		foreach ($question as $q) {
			echo $q[$key];
			echo '"';
		}
	}
	echo '/>';
	pad();
	echo '<input type="radio" name="isCorrect" value="'.$num.'"';
	if ($questionID) {
		foreach ($question as $q) {
			$correctAnswer=$q['correctAnswer'];
			if ($num==$correctAnswer)
				echo 'checked';
			}
		}
		echo '/>';
  } 

  function pad () {
	echo '   ';
  }
