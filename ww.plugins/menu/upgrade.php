<?php
if($version=='0'){ // add menu table
	dbQuery('create table if not exists menus(
		id int auto_increment not null primary key,
		parent int default 0,
		direction smallint default 1
	)default charset=utf8;');
	$version=1;
}
if($version=='1'){ // add background colours
	dbQuery('alter table menus add background char(7) default "#ff0000"');
	$version=2;
}
if($version=='2'){ // add opacity
	dbQuery('alter table menus add opacity float default .95');
	$version=3;
}
if($version=='3'){ // columns
	dbQuery('alter table menus add columns smallint default 1');
	$version=4;
}

$DBVARS[$pname.'|version']=$version;
config_rewrite();