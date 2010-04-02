<?php
$special=0;
$specials=getVar('special');
if(is_array($specials))foreach($specials as $a=>$b)$special+=pow(2,$a);
if(isset($_REQUEST['name']))$_REQUEST['name']=trim($_REQUEST['name']);
$homes=dbOne("SELECT COUNT(id) AS ids FROM pages WHERE (special&1)".($id?" AND id!=$id":""),'ids');
if($special&1){ // there can be only one! homepage, I mean
	if($homes!=0){
		dbQuery("UPDATE pages SET special=special-1 WHERE special&1");
	}
}
else{
	if($homes==0){
		$special+=1;
		$msgs.='<em>'.__('This page has been marked as the site\'s Home Page, because there must always be one.').'</em>';
	}
}
