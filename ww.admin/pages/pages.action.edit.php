<?php
function recordTranslation($name,$value){
	global $id;
	if(!$name || !$value)return;
	$name=addslashes($name);
	$value=addslashes($value);
	dbQuery("INSERT INTO translations SET object_type='page',object_id=$id,lang='".addslashes($_SESSION['editing_language'])."',name='$name',value='$value'");
}
function recursively_update_page_templates($id,$template){
	$pages=Pages::getInstancesByParent($id);
	$ids=array();
	foreach($pages->pages as $page){
		$ids[]=$page->id;
		recursively_update_page_templates($page->id,$template);
	}
	if(!count($ids))return;
	dbQuery('update pages set template="'.addslashes($template).'" where id in ('.join(',',$ids).')');
}
if(allowedToEditPage($id)){
	include 'pages/pages.action.common.php';
	$pid=(int)$_REQUEST['parent'];
	$l=dbRow("SELECT * FROM site_vars WHERE name='languages'");
	if($l['value'])$langs=json_decode($l['value']);
	else $langs=array();
	$translation=0;
	if(count($langs)>1){
		if(!isset($_SESSION['editing_language']))$_SESSION['editing_language']=$langs[0]->iso;
		if($langs[0]->iso!=$_SESSION['editing_language'])$translation=1;
	}
	// {
	$keywords=$_REQUEST['keywords'];
	$description=$_REQUEST['description'];
	$title=$_REQUEST['title'];
	$importance=(float)$_REQUEST['importance'];
	$name=$_REQUEST['name'];
	if($importance<0)$importance=0;
	if($importance>1)$importance=1;
	$template=getVar('template');
	$body=str_replace(
		array(
			'<u />',"</ul>\r\n<ul>",'<u></u>',' align="center"','margin: 0cm 0cm 0pt;',
			'class="Bodytext"','<span>&nbsp; </span>',' lang="EN-US"',' lang="EN-IE"',' class="MsoNormal"',
			'style=""','bgstyle="color:','bgcolor="',' lang="EN-GB"'),
		array(
			'','','','','margin:0;',
			'','','&nbsp;','','','',
			'','style="background:','style="background:',''),
		$_REQUEST['body']
	);
	$body=preg_replace('#</?([ovw]|st1):[^>]*>#','',$body);
	$body=sanitise_html($body);
	// { check that name is not duplicate of existing page
	if(dbQuery('select id from pages where name="'.addslashes($name).'" and parent='.$pid.' and id!="'.$_POST['id'].'"')->numRows()){
		$i=2;
		while(dbQuery('select id from pages where name="'.addslashes($name.$i).'" and parent='.$pid.' id!="'.$_POST['id'].'"')->numRows())$i++;
		echo '<em>'.__('A page named "%1" already exists. Page name amended to "%2"',$name,$name.$i).'</em>';
		$name=$name.$i;
	}
	// }
	$category1=getVar('category1');
	$category2=getVar('category2');
	$category=$category2&&$category2!=__('add another')?$category2:$category1;
	// }
	$q='update pages set importance="'.$importance.'",category="'.$category.'",template="'.$template.'",edate=now(),type="'.$_POST['type'].'"';
	if(!$translation)$q.=',keywords="'.$keywords.'",description="'.$description.'",name="'.addslashes($name).'",title="'.$_POST['title'].'",body="'.addslashes($body).'"';
	else{
		dbQuery("DELETE FROM translations WHERE object_type='page' AND object_id=$id AND lang='".addslashes($_SESSION['editing_language'])."'");
		recordTranslation('keywords',$keywords);
		recordTranslation('description',$description);
		recordTranslation('title',$title);
		recordTranslation('body',$body);
		recordTranslation('name',$name);
	}
	$q.=',parent='.$pid;
	if(has_page_permissions(128))$q.=',special='.$special;
	$q.=' where id='.$id;
	dbQuery($q);
	// { page_vars
	dbQuery('delete from page_vars where page_id="'.$id.'"');
	$pagevars=isset($_REQUEST['page_vars'])?$_REQUEST['page_vars']:array();
	if(isset($_REQUEST['banned_countries']) && is_array($_REQUEST['banned_countries'])){
		$pagevars['banned_countries']=join(',',$_REQUEST['banned_countries']);
	}
	if(is_array($pagevars))foreach($pagevars as $k=>$v)dbQuery('insert into page_vars (name,value,page_id) values("'.addslashes($k).'","'.addslashes($v).'",'.$id.')');
	// }
	if(isset($_REQUEST['recursively_update_page_templates']))recursively_update_page_templates($id,$template);
	echo '<em>'.__('An item\'s details have been updated.').'</em>';
}
else{
	echo '<em>'.__('No update rights.').'</em>';
}
