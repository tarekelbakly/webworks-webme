<?php 
// {
	$plugin = array (
		'name' =>'Quizzes',
		'hide_from_admin' => true,
		'admin'=>array(
			'menu'=> array ('Misc>Quiz'=>'index'),
			'page_type' => 'quiz'
		),
		'frontend' => array (
			'page_type' => 'quiz_display_page'
		),
		'description'=>'Create a quiz with this plugin',
		'version'=>3
	);

	
	function quiz_display_page () {
		$dir= dirname(__FILE__);
		include ($dir'/frontend/display.php');
		return getPageHtml();
	}

// }

