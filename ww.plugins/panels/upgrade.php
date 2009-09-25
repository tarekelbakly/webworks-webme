<?php
if($version==0){ // panels table
	dbQuery('CREATE TABLE IF NOT EXISTS `panels` (
		`id` int(11) NOT NULL auto_increment,
		`name` text,
		`body` text,
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=1;
}
if($version==1){ // convert panel into widget container
	$rs=dbAll('select * from panels');
	foreach($rs as $r){
		$html=addslashes($r['body']);
		$rid=$r['id'];
		dbQuery("insert into banners_images (html,type) values ('$html',1)");
		$id=dbOne('select last_insert_id() as id limit 1','id');
		dbQuery("update panels set body='{\"widgets\":[{\"type\":\"banner-image\",\"id\":$id}]}' where id=$rid");
	}
	$version=2;
}
if($version==2){ // add 'visibility' field
	dbQuery('alter table panels add visibility text');
	$version=3;
}

$DBVARS[$pname.'|version']=$version;
config_rewrite();
