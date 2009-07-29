<?php
/*
        Webme News Plugin v0.1
        File: plugin.php
        Developer: Conor Mac Aoidh <http://macaoidh.name>
        Report Bugs: <conor@macaoidh.name>
*/

$plugin=array(
	'name' => 'News',
	'admin' => array(
		'page_type' => 'news_admin'
	),
	'description' => 'Allows news items to be created and displayed on a page..',
	'frontend' => array(
		'page_type' => 'news_front'
	),
	'version'=>1
);

function news_admin(){
	require SCRIPTBASE.'ww.plugins/news/admin/display.php';
	return $html;
}

function news_front(){
	require SCRIPTBASE.'ww.plugins/news/frontend/display.php';
	return $html;
}
