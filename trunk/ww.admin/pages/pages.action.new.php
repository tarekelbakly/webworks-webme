<?php
if(allowedToEditPage($parent)){
	include 'pages/pages.action.common.php';
	$name=addslashes(getVar('name'));
	$pid=(int)$_REQUEST['parent'];
	if(dbQuery("select id from pages where name='$name' and parent=$pid")->numRows()){
		$i=2;
		while(dbQuery("select id from pages where name='$name$i' and parent=$pid")->numRows())$i++;
		echo '<em>'.__('A page named "%1" already exists. Page name amended to "%2"',$name,$name.$i).'</em>';
		$name.=$i;
	}
	{ # variables
		$template=getVar('template');
		$type=getVar('type');
		$body=str_replace(
			array(
				'<u />',"</ul>\r\n<ul>",'<u></u>',' align="center"','margin: 0cm 0cm 0pt;',
				'class="Bodytext"','<span>&nbsp; </span>',' lang="EN-US"',' lang="EN-IE"',' class="MsoNormal"',
				'style=""','bgstyle="color:','bgcolor="',' lang="EN-GB"'),
			array(
				'','','','','margin:0;',
				'','','&nbsp;','','','',
				'','style="background:','style="background:',''),
			getVar('body')
		);
		$body=preg_replace('#</?([ovw]|st1):[^>]*>#','',$body);
		$keywords=getVar('keywords');
		$description=getVar('description');
		$importance=(float)getVar('importance');
		if($importance<0)$importance=0;
		if($importance>1)$importance=1;
		$category1=getVar('category1');
		$category2=getVar('category2');
		$category=$category2&&$category2!=__('add another')?$category2:$category1;
	}
	if(getVar('page_order')==0){
		dbQuery("update pages set ord=ord+1 where parent=".$pid);
		$ord=0;
	}
	else{
		$ord=dbOne('select ord from pages where parent='.$pid.' order by ord desc limit 1','ord')+1;
	}
	$q='insert into pages set ord="'.$ord.'",importance="'.$importance.'",category="'.$category.'",keywords="'.$keywords.'",description="'.$description.'",cdate=now(),template="'.$template.'",edate=now(),name="'.$name.'",title="'.$_POST['title'].'",body="'.addslashes(sanitise_html(getVar('body'))).'",type="'.$type.'"';
	$q.=',parent='.$pid;
	if(has_page_permissions(128))$q.=',special='.$special;else $q.=',special=0';
	dbQuery($q);
	$id=dbOne('select last_insert_id() as id','id');
	dbQuery('insert into permissions set id="'.$id.'", type=1, value="'.get_userid().'=7'."\n\n4".'"');
	rebuild_parent_rsses($id);
	dbQuery('update blog_indexes set rss=""');
	echo '<em>'.__('An item has been added to the database.').'</em>';
}
else{
	echo '<em>'.__('You do not have permissions for creating a document in that location.').'</em>';
}
