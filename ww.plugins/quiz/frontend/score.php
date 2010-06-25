<?php
function getScore () {
  include_once (__DIR__.'/quiz.php');	
  var_dump ($quiz);
  $quiz->checkAnswers($_POST);
  echo 'a';
  return;
}

getScore();
