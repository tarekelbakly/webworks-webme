<?php
if($version==0){ // online_store_orders
	dbQuery('CREATE TABLE IF NOT EXISTS `online_store_orders` (
		`id` int(11) NOT NULL auto_increment,
		`form_vals` text,
		`invoice` text,
		`total` float,
		`date_created` datetime,
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=1;
}
if($version==1){ // items field
	dbQuery('alter table online_store_orders add items text');
	$version=2;
}
if($version==2){ // status
	dbQuery('alter table online_store_orders add status smallint default 0');
	$version=3;
}

$DBVARS[$pname.'|version']=$version;
config_rewrite();
