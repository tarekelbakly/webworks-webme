<?php

$languages=array(
	''=>' -- choose -- ',
	'en'=>'English',
	'da'=>'Danish',
	'fr'=>'French',
	'de'=>'German',
	'es'=>'Spanish'
);

foreach($languages as $k=>$v)echo '<option value="'.$k.'">'.$v.'</option>';
