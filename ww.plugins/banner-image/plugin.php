<?php
/*
	Webme Banner Plugin
	File: plugin.php
	Developers: Conor Mac Aoidh <http://macaoidh.name/ conor@macaoidh.name>
	            Kae Verens      <http://verens.com/    kae@verens.com>
	report bugs to Kae.
*/
$plugin=array(
	'name' => 'Banners',
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
