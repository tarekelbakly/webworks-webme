<?php
$plugin=array(
	'name' => 'Menu',
	'admin' => array(
		'widget' => array(
			'form_url' => '/ww.plugins/menu/admin/widget-form.php',
			'js_include' => '/ww.plugins/menu/j/farbtastic/farbtastic.js',
			'css_include' => '/ww.plugins/menu/j/farbtastic/farbtastic.css'
		)
	),
	'description' => 'Menu widget',
	'frontend' => array(
		'widget' => 'menu_showWidget'
	),
	'version'=>4
);

require_once SCRIPTBASE.'common/menus.php';
function menu_showWidget($vars=null){
	if($vars && $vars->id){
		$vars=dbRow('select * from menus where id='.$vars->id);
	}
	return menu_show_fg($vars);
}
