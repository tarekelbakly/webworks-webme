<?php
$plugin=array(
	'name' => 'Form',
	'admin' => array(
		'page_type' => 'form_admin_page_form'
	),
	'frontend' => array(
		'page_type' => 'form_frontend'
	),
	'version' => 3
);
function form_admin_page_form($page,$vars){
	$edit=$GLOBALS['is_an_update'];
	$id=$page['id'];
	$c='';
	if($edit)require dirname(__FILE__).'/admin/save.php';
	require dirname(__FILE__).'/admin/form.php';
	return $c;
}
function form_frontend($PAGEDATA){
	require dirname(__FILE__).'/frontend/show.php';
	return $PAGEDATA->body . form_show($PAGEDATA->dbVals,$PAGEDATA->vars);
}
