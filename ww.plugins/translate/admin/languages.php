<?php
/**
	* language list for Translate plugin
	*
	* PHP version 5
	*
	* @category None
	* @package  None
	* @author   Kae Verens <kae@webworks.ie>
	* @license  GPL 2.0
	* @link     None
	*/

$languages=array(
	''=>' -- choose -- ',
	'en'=>'English',
	'da'=>'Danish',
	'fr'=>'French',
	'de'=>'German',
	'ga'=>'Irish',
	'es'=>'Spanish'
);

foreach ($languages as $k=>$v) {
	echo '<option value="'.$k.'">'.$v.'</option>';
}
