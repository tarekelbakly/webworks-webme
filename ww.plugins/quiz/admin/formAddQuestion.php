<?php
  echo '<form method="post">';
  // { Question Tab
  $result = dbAll("SELECT id FROM quiz_quizzes WHERE name='$quizName';");
  foreach ($result as $r) {
	$id = $r['id'];
  }
  echo '<div class="tabpage">';
  echo '<h2>New Question</h2>';
  echo '<input type="hidden" name="quiz_id" value="';
  echo $id.'"';
  echo '<input type="hidden" name="question_id" value="';
  echo $_GET['questionid'].'"';
  echo '"/>';
  echo '<br/>';
  echo 'Question';
  pad();
  echo '<input type="text" name="question"';
  if (isset($_POST['question'])) {
	echo 'value="'.htmlspecialchars($_POST['question']).'"';
  }
  echo '/>';
  echo '<input type="text" name="topic"'; 
  if (isset($_POST['topic'])) {
	echo 'value="'.htmlspecialchars($_POST['topic']).'"';
  }
  echo '/>';
  echo '</div>';
  // }
  // { Answers Tab
  echo '<div class="tabpage">';
  echo '<h2>New Answers</h2>';
  for ($i=0; $i<'4'; $i++) {
	$num=$i+1;
	echo 'Possible Answer '.$num.' ';
	addAnswer($num);
	echo '<br/>';
  }	 
  echo '</div>';
  // }
  echo '<input type="submit" name="addQuestion" value="Add Question"/>';
  echo '</div>';//Ends the tabs div
  echo '</form>';
 
  function addAnswer($num) {
	echo '<input type="text" name="answers[]"';
	if (isset ($_POST['answers'])) {
	  $answers = $_POST['answers'];
	  $i = $num-1;
	  if (!empty($answers[$i])) {
	    echo ' value="'.htmlspecialchars($answers[$i]).'"';
	  }
	}
	  echo '/>';
	pad();
	echo '<input type="radio" name="isCorrect" value="'.$num.'"/>';
 } 

  function pad () {
	echo '   ';
 }
