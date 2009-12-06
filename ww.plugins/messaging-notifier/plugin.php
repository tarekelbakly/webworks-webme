<?php
$plugin=array(
	'name' => 'Messaging Notifier',
	'admin' => array(
#		'page_type' => 'online_store_admin_page_form'
		'widget' => array(
			'form_url' => '/ww.plugins/messaging-notifier/admin/widget-form.php'
		)
	),
	'description' => 'Show a list of recent tweets',
	'frontend' => array(
		'widget' => 'messaging_notifier_show_widget',
//		'page_type' => 'online_store_frontend'
	),
	'version' => '3'
);
function messaging_notifier_show_widget($vars){
	include_once SCRIPTBASE.'ww.plugins/messaging-notifier/frontend/index.php';
	return show_messaging_notifier($vars);
}
