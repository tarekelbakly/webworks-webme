<?php
// var_dump($_REQUEST);
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');
if(isset($_REQUEST['get_content_snippet'])){
	$id=(int)$_REQUEST['get_content_snippet'];
	$r=dbRow('select * from content_snippets where id='.$id);
	if(!$r){
		echo '{"id":0,"content":[{"html":""}]}';
	}
	else{
		$r['content']=json_decode($r['content']);
		echo json_encode($r);
	}
	exit;
}
if(isset($_REQUEST['action']) && $_REQUEST['action']=='save'){
	$id=(int)$_REQUEST['id'];
	$id_was=$id;
	$html=addslashes(utf8_decode($_REQUEST['html']));
	$sql="content_snippets set content='$html'";
	$sql.=',accordion="'.(int)$_REQUEST['accordion'].'"';
	$sql.=',accordion_direction="'.(int)$_REQUEST['accordion_dir'].'"';
	$sql.=',images_directory="'.addslashes($_REQUEST['accordion_images']).'"';
	if($id){
		$sql="update $sql where id=$id";
		dbQuery($sql);
	}
	else{
		$sql="insert into $sql";
		dbQuery($sql);
		$id=dbOne('select last_insert_id() as id','id');
	}
	cache_clear('content_snippets');
	$ret=array('id'=>$id,'id_was'=>$id_was);
	echo json_encode($ret);
	exit;
}

if(isset($_REQUEST['id']))$id=(int)$_REQUEST['id'];
else $id=0;
echo '<a href="javascript:;" id="content_snippet_editlink_'
	.$id.'" class="content_snippet_editlink">view or edit snippet</a>';
?>
