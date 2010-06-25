<?php //{
	$plugin = array ('name' =>'Quizzes',
	'hide_from_admin' => true,
			 'admin'=>array(
					'menu'=> array ('Misc>Quiz'=>'index'),
					'page_type' => 'quiz'
					),
			  'frontend' => array (
			  	'widget' => 'quiz_display',
				'page_type' => 'quiz_display_page'
			),
			'description'=>'Create a quiz with this plugin',
			'version'=>3
		);

	function quiz_display() {
		echo 'Quizzes';
		echo '<br/>';
		$result = dbAll("SELECT DISTINCT quiz_quizzes.id, name, quiz_quizzes.topic FROM quiz_quizzes, quiz_questions WHERE quiz_quizzes.id=quiz_questions.quiz_id LIMIT 1");
		foreach ($result as $quiz) {
			echo $quiz['name'];
			echo '<br/>';
			echo $quiz['topic'];
			echo '<br/>';
		}
		echo '<form method="post">';
		echo '<input type="submit" name="take" value= "Take this Quiz">';
		echo '<input type="button" name="view" value="View all Quizzes" onClick="location.href=\'http://webworks-webme/quiz\'"/>';
		echo'</form>';
	}
	
	function quiz_display_page () {
		include (__DIR__.'/frontend/display.php');
		return getPageHtml();
	}

// }

