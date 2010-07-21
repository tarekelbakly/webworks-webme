<?php
/**
  *	The QuizSession Class holds the variables and method to deal with quizzes
  *
  * PHP Version 5.2.6
  *
  * It creates a quiz, 
  * displays the questions 
  * and shows the score once the answers have been submitted.
  *
  * @category   Quiz_Plugin
  * @package    Webworks_WebME
  * @subpackage QuizPlugin
  * @author     Belinda Hamilton <bhamilton@webworks.ie>
  * @license    This software is released under GPL version 2
  * @link       www.webworks.ie
*/

	/**
	  * The class
	  *
	*/
	class QuizSession{
		private $_name;
		private $_numQuestions;
		private $_id;
		private $_score;
		private $_numQuestionsToBeAnswered;
		private $_questionsToBeAsked;
		private $_allQuestions;

		function __construct ($num, $questionsToBeAnswered) {
			$this->id= addslashes($num);
			$id= $this->id;
			$this->score= 0;
			$this->numQuestionsToBeAnswered = $questionsToBeAnswered;
			$quizzes= dbAll ("SELECT * FROM quiz_quizzes WHERE id = '".$id."'");
			$rows = dbAll("SELECT * FROM quiz_questions WHERE quiz_id = '$id' AND question IS NOT NULL");
			if (count($rows)!=0) {
			// I want the questions to be in an indexed array
				$this->allQuestions=array();
				$i=0;
				foreach ($rows as $row) {
					$this->allQuestions[$i]=$row;
					$i++;
				}

				$this->numQuestions = count($rows);
				if ($this->numQuestionsToBeAnswered > $this->numQuestions) {
					$this->numQuestionsToBeAnswered = ceil($this->numQuestions/2);//Otherwise if there is only one available question it will be 0.
				}
			}

			else {
				return null;
			}
		}

		function chooseQuestions () {
			global $questionsToBeAsked;
			$numQuestions = $this->numQuestions;
			$allQuestions = $this->allQuestions;
			$questionsToBeAsked=array();
			$indices = array();
			$numQuestionsToBeAnswered = $this->numQuestionsToBeAnswered;
			for ($i=0; $i<$numQuestionsToBeAnswered; $i++) {
				$index=rand(0,($numQuestions-1));
				while (in_array($index, $indices)) {//Don't repeat questions
					$index=rand(0, ($numQuestions-1));
				}
				$indices[$i] = $index;
				$questionsToBeAsked[$i]=$allQuestions[$index];
			}
			$_SESSION['questions']=$questionsToBeAsked;
		}
		function getQuestionPageHtml () {//Displays the questions and possible answers
			global $questionsToBeAsked;
			$quizString='<form method="post"><ol>';
			for ($i=0; $i<count($questionsToBeAsked); $i++) {
				 $quizString = $quizString.'<li>'.$questionsToBeAsked[$i]['question'].'</li>';
				 $quizString= $quizString.'<ul>';
				 for ($j=1; $j<5; $j++) {
				 	$answerNum='answer'.$j;
					$answer=$questionsToBeAsked[$i][$answerNum];
				 	if (!empty($answer)) {
						$quizString= $quizString.'<li><input type="radio" name="'.$questionsToBeAsked[$i]['id'].'" value="'.$j.'"/>'.htmlspecialchars($answer).'</li>';
					}
			    }
				$quizString=$quizString. '</ul>';
			}
			$quizString=$quizString.'</ol><input type="submit" name="check" value="Submit Answers"/></form>';
			return $quizString;
		}

		function checkAnswers ($questions, $answers) { //Checks the Answers
		    $score = $this->score; // I may be changing the object but the score always defaults to 0
		    $numQuestionsToBeAnswered = count($questions);
			$returnString = "";
		    for ($i=0; $i<$numQuestionsToBeAnswered; $i++) {
			   $question= $questions[$i]['question'];
			   $correctAnswer= $questions[$i]['correctAnswer'];
			   $key= $questions[$i]['id'];
			   $answer= $answers[$key];
			   $questionNum= $i+1;
			   $returnString= $returnString.'<b>Question '.$questionNum.'</b><br/>';
			   $returnString= $returnString.'You answered '.$answer.'<br/>';
			   $returnString= $returnString.'The correct answer was '.$correctAnswer.'<br/>';
			   $returnString= $returnString.'<br/>';
			   if($answer==$correctAnswer) {
				  $score++;
			   }
		    }
		    $returnString = $returnString.'You scored '.$score.' out of '.$numQuestionsToBeAnswered;
			$returnString = $returnString.'<form method="post">';
			$returnString = $returnString.'<center>';
			$returnString = $returnString.'<input type="submit" name="action" value="Back to Quizzes"/>';
			$returnString = $returnString.'</center>';
			$returnString = $returnString.'</form>';
			return $returnString;
		}
	}
