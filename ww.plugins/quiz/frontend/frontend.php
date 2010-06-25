<?php

	$quizzes= dbAll("SELECT * FROM quiz_quizzes");
	
	foreach ($quizzes as $quiz) {
		$name = $quiz['name'];
		$topic= $quiz['topic'];
		displayQuizInfo ($name, $topic);
	}

	function displayQuizInfo ($name, $topic) {
		echo htmlspecialchars($name).'<br/>';
		echo htmlspecialchars($topic).'<br/>';
		echo '<input type="submit" name="takeQuiz", value="Take Quiz" />';
		echo '<br/>';
	}
