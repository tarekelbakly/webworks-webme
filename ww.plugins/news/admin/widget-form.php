<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');

if(isset($_REQUEST['id']))$id=(int)$_REQUEST['id'];
else $id=0;
echo '<strong>News Page</strong><br />';
$news_pages=dbAll('select id,name from pages where type="news" order by name');
if(!count($news_pages)){
	echo 'no News pages created. please <a href="/ww.admin/pages.php">create one</a> first.';
	exit;
}
echo '<select name="id">';
foreach($news_pages as $b){
	echo '<option value="'.$b['id'].'"';
	if($id==$b['id'])echo ' selected="selected"';
	echo '>'.htmlspecialchars($b['name']).'</option>';
}
echo '</select>';
?>
