<?php
header('Expires-Active: On');
header('Expires: Fri, 1 Jan 2500 01:01:01 GMT');
header('Pragma:');
header('Content-type: text/css; charset=utf-8');

echo file_get_contents('menus.css');
echo file_get_contents('language_flags.css');
echo file_get_contents('ui.datepicker.css');
echo file_get_contents('forms.css');
echo file_get_contents('comments.css');
echo file_get_contents('os_basket.css');
echo file_get_contents('contextmenu.css');
