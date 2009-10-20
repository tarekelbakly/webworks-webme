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
		),
	),
	'description' => 'HTML snippet or image.',
	'frontend' => array(
		'template_functions' => array(
			'BANNER' => array(
				'function' => 'showBanner'
			)
		),
		'widget' => 'showBanner'
	),
	'version' => '3'
);

$banner_image_types=array('jpg','gif','png');
function banner_image_getImgHTML($id,$hide_message=false){
	global $banner_image_types;
	$type='';
	foreach($banner_image_types as $t)if(file_exists(USERBASE.'f/skin_files/banner-image/'.$id.'.'.$t))$type=$t;
	if(!$type)return $hide_message?'':'no image uploaded';
	return '<img src="/f/skin_files/banner-image/'.$id.'.'.$type.'" />';
}
function showBanner($vars=null){
	include_once SCRIPTBASE.'ww.plugins/banner-image/frontend/banner-image.php';
	return show_banner($vars);
}
