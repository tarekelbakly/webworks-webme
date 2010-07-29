<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');

if(!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id']))exit;
if(!isset($_REQUEST['name']) || $_REQUEST['name']=='')exit;

include 'libs.php';

dbQuery('update products_categories set name="'.addslashes($_REQUEST['name']).'",enabled="'.((int)$_REQUEST['enabled']).'" where id='.$_REQUEST['id']);

$pageid	
	= dbOne(
		'select page_id 
		from page_vars 
		where name=\'products_category_to_show\' and value='.$_REQUEST['id'],
		'page_id'
	);
if ($pageid) {
	dbQuery('update pages set special = special|2 where id='.$pageid);
}

$data=products_categories_get_data($_REQUEST['id']);

echo json_encode($data);
