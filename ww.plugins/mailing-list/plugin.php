<?php
/*
	Webme Mailing List Plugin v0.2
	File: plugin.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/
$plugin=array(
	'name' => 'Mailing List',
	'admin' => array(
		'menu' => array(
			'top'  => 'Communication'
		)
	),
        'description' => 'Collect and manage emails in a mailing list. Also send emails to that list.',
	'frontend' => array(
		'template_functions' => array(
			'MAILING_LIST' => array(
				'function' => 'showForm'
			)
		)
	),
	'version' => '0.2'
);
function showForm(){
	include_once SCRIPTBASE . 'ww.plugins/mailing-list/frontend/mailing-list.php';
	return show_form();
}
