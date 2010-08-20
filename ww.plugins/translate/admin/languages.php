<?php

$languages=array(
	''=>' -- choose -- ',
	'en'=>'English',
	'da'=>'Danish',
	'fr'=>'French',
	'de'=>'German',
	'ga'=>'Irish',
	'es'=>'Spanish'
);

foreach($languages as $k=>$v)echo '<option value="'.$k.'">'.$v.'</option>';
