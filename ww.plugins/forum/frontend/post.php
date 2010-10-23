<?php
/**
  * post messages to a forum
  *
  * PHP Version 5
  *
  * @category   Whatever
  * @package    WebworksWebme
  * @subpackage Forum
  * @author     Kae Verens <kae@webworks.ie>
  * @license    GPL Version 2
  * @link       www.webworks.ie
 */

require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if (!isset($_SESSION['userdata']) || !$_SESSION['userdata']['id']) {
	exit;
}

$title=$_REQUEST['title'];
$body=$_REQUEST['body'];
$forum_id=(int)$_REQUEST['forum_id'];
$thread_id=(int)$_REQUEST['thread_id'];

$errs=array();

if (!$body) {
	$errs[]='no post body supplied';
}
if (!$forum_id) {
	$errs[]='no forum selected';
}
else {
	$forum=dbRow('select * from forums where id='.$forum_id);
	if (!$forum || !count($forum)) {
		$errs[]='forum does not exist';
	}
	else {
		if ($thread_id) {
			$title='';
			$thread=dbRow(
				'select * from forums_threads where id='
				.$thread_id.' and forum_id='.$forum_id
			);
			if (!$thread || !count($thread)) {
				$errs[]='thread does not exist or doesn\'t belong to that forum';
			}
		}
		else {
			if (!$title) {
				$errs[]='no thread title supplied';
			}
		}
	}
}

if (count($errs)) {
	echo json_encode(
		array(
			'errors'=>$errs
		)
	);
	exit;
}

if (!$thread_id) {
	dbQuery(
		'insert into forums_threads values(0,'
		.$forum_id.',0,"'.addslashes($title).'",'
		.$_SESSION['userdata']['id'].',now(),0,now(),0)'
	);
	$thread_id=dbLastInsertId();
}

dbQuery(
	'insert into forums_posts values(0,'.$thread_id.','
	.$_SESSION['userdata']['id'].',now(),"'
	.addslashes($body).'")'
);
$post_id=dbLastInsertId();

dbQuery(
	'update forums_threads set num_posts=num_posts+1,'
	.'last_post_date=now(),last_post_by='.$_SESSION['userdata']['id']
	.' where id='.$thread_id
);

echo json_encode(
	array(
		'forum_id'=>$forum_id,
		'thread_id'=>$thread_id,
		'post_id'=>$post_id
	)
);
