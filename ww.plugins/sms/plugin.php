<?php
/**
	* definition file for SMS plugin
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
	'name' => 'SMS',
	'admin' => array(
		'menu' => array(
			'Communication>SMS'=> 'dashboard'
		),
		'widget' => array(
			'form_url' => '/ww.plugins/sms/admin/widget-form.php'
		)
	),
	'frontend' => array(
		'widget' => 'sms_showWidget'
	),
	'description' => 'Add SMS capabilities to your site, using the textr.mobi service.',
	'version' => 1
);
// }

function sms_showWidget($vars){
	require SCRIPTBASE.'ww.plugins/sms/frontend/widget.php';
	return $html;
}
