<?php
$plugin=array(
	'name' => 'Translate',
	'admin' => array(
		'page_type' => 'Translate_admin'
	),
	'description' => 'Use Google Translate to automatically translate pages.',
	'frontend' => array(
		'page_type' => 'Translate_frontend'
	)
);
function Translate_admin($page,$vars){
	require dirname(__FILE__).'/admin/form.php';
	return $c;
}
function Translate_frontend($PAGEDATA){
	require dirname(__FILE__).'/frontend/check.php';
	return $PAGEDATA->render();
}
