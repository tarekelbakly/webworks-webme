<?php
$plugin=array(
	'name' => 'Image Transitions',
	'admin' => array(
		'widget' => array(
			'form_url'   => '/ww.plugins/image-transition/admin/widget-form.php',
			'js_include' => '/ww.plugins/image-transition/admin/widget.js'
		)
	),
	'description' => 'Add an image to a panel which transitions to other specified images.',
	'frontend' => array(
		'widget' => 'showImageTransition'
	),
	'version' => '2'
);
function showImageTransition($vars=null){
	include_once SCRIPTBASE.'ww.plugins/image-transition/frontend/index.php';
	return show_image_transition($vars);
}
