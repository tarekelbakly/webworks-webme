<?php
/**
	* definition file for Translate plugin
	*
	* PHP version 5
	*
	* @category None
	* @package  None
	* @author   Kae Verens <kae@webworks.ie>
	* @license  GPL 2.0
	* @link     None
	*/

// { define $plugin
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
// }

/**
	* admin area Page form
	*
	* @param object $page Page array from database
	* @param array  $vars Page's custom variables
	*
	* @return string
	*/
function Translate_admin($page,$vars) {
	require dirname(__FILE__).'/admin/form.php';
	return $c;
}


/**
	* stub function to load frontend page-type
	*
	* @param object $PAGEDATA the current page
	*
	* @return string
	*/
function Translate_frontend($PAGEDATA) {
	require dirname(__FILE__).'/frontend/check.php';
	return $PAGEDATA->render();
}
