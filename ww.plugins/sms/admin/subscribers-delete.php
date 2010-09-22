<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');
$id=(int)$_REQUEST['id'];
if (!is_int($id)) exit('invalid id');

$addressBooks = dbAll('select id, subscribers from sms_addressbooks');
foreach($addressBooks as $book) {
	$subs = $book['subscribers'];
	$subs = str_replace($id.',', '', $subs);
	if ($subs==$book['subscribers']) {
		continue;
	}
	dbQuery(
		'update sms_addressbooks 
		set subscribers = "'.$subs.'" 
		where id = '.(int)$book['id']
	);
}
dbQuery('delete from sms_subscribers where id='.$id);
echo '{"err":0,id:'.$id.'}';
