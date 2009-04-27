<?php
/*
	Webme Banner Image Plugin v0.1
	File: plugin.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/
$plugin=array(
	'name' => 'Banner Image',
	'admin' => array(
		'menu' => array(
			'top'  => 'Misc'
		)
	),
        'description' => 'Upload banner image to your website.',
	'frontend' => array(
		'template_functions' => array(
			'BANNER' => array(
				'function' => 'showBanner'
			)
		)
	),
	'version' => '2'
);
function showBanner($vars=null){
	include_once SCRIPTBASE.'ww.plugins/banner-image/frontend/banner-image.php';
	return show_banner($vars);
}
