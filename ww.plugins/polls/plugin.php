<?php
$plugin=array(
	'name' => 'Polls',
	'admin' => array(
		'menu' => array(
			'top'  => 'Communication'
		)
	),
	'frontend' => array(
		'template_functions' => array(
			'POLL' => array(
				'file' => 'polls.php',
				'function' => 'pollDisplay'
			)
		)
	)
);
function pollDisplay(){
	include_once SCRIPTBASE . 'ww.plugins/polls/frontend/polls.php';
	return poll_display();
}
