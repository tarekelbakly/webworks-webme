<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin()) {
	die('access denied');
}
if(!isset($_REQUEST['questionid']) || !is_numeric($_REQUEST['questionid'])) {
	exit;
}
$questionID= $_REQUEST['questionid'];
dbQuery ("DELETE FROM quiz_questions WHERE id = '$questionID'");
if(dbOne("SELECT id FROM quiz_questions where id = '$questionID'",'id')){
	echo '{"status":0}';
}
else {
	echo '{"id":'.$questionID.',"status":1}';
}

