<?php
$plugin=array(
	'name'=>'Panels',
	'description'=>'Allows content sections to be displayed throughout the site.',
	'admin'=>array(
		'menu'=>array(
			'top'=>'Misc'
		)
	),
	'frontend'=>array(
		'template_functions'=>array(
			'PANEL'=>array(
				'function' => 'showPanel'
			)
		)
	),
	'version'=>1
);
function showPanel($vars){
	$p=dbRow('select body from panels where name="'.addslashes(@$vars['name']).'" limit 1');
	if(!count($p)){
		return '<em>error - panel <strong>'.htmlspecialchars(@$vars['name']).'</strong> does not exist.</em>';
	}
	return $p['body'];
}
