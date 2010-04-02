<?php
if(allowedToEditPage($parent)){
	include 'pages/pages.action.common.php';
	$name=$_REQUEST['name'];
	if(isset($_REQUEST['prefill_body_with_title_as_header']))$body='<h1>'.htmlspecialchars($name).'</h1><p>&nbsp;</p>';
	else if(isset($_REQUEST['body']))$body=$_REQUEST['body'];
	else $body='';
	$name=addslashes($name);
	$pid=(int)$_REQUEST['parent'];
	if(dbQuery("select id from pages where name='$name' and parent=$pid")->rowCount()){
		$i=2;
		while(dbQuery("select id from pages where name='$name$i' and parent=$pid")->rowCount())$i++;
		$msgs.='<em>'.__('A page named "%1" already exists. Page name amended to "%2"',$name,$name.$i).'</em>';
		$name.=$i;
	}
	// { variables
	$template=getVar('template');
	$type=getVar('type');
	$title=isset($_REQUEST['title'])?addslashes($_REQUEST['title']):'';
	$keywords=getVar('keywords');
	$associated_date=getVar('associated_date');
	$description=getVar('description');
	$importance=(float)getVar('importance');
	if($importance<0)$importance=0;
	if($importance>1)$importance=1;
	$category1=getVar('category1');
	$category2=getVar('category2');
	$category=$category2&&$category2!=__('add another')?$category2:$category1;
	// }
	if(getVar('page_order')==0){
		dbQuery("update pages set ord=ord+1 where parent=".$pid);
		$ord=0;
	}
	else{
		$ord=dbOne('select ord from pages where parent='.$pid.' order by ord desc limit 1','ord')+1;
	}
	$q='insert into pages set ord="'.$ord.'",importance="'.$importance.'",category="'.$category.'",keywords="'.$keywords.'",description="'.$description.'",cdate=now(),template="'.$template.'",edate=now(),name="'.$name.'",title="'.$title.'",body="'.addslashes(sanitise_html($body)).'",type="'.$type.'",associated_date="'.addslashes($associated_date).'"';
	$q.=',parent='.$pid;
	if(has_page_permissions(128))$q.=',special='.$special;else $q.=',special=0';
	dbQuery($q);
	$id=dbOne('select last_insert_id() as id','id');
	dbQuery('insert into permissions set id="'.$id.'", type=1, value="'.get_userid().'=7'."\n\n4".'"');
	$msgs.='<em>'.__('New page created.').'</em>';
	dbQuery('update page_summaries set rss=""');
	cache_clear('menus');
	cache_clear('pages');
}
else{
	$msgs.='<em>'.__('You do not have permissions for creating a document in that location.').'</em>';
}
