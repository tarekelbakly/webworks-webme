<?php
require SCRIPTBASE.'ww.incs/db.php';
if($version==0){ // panels table
	dbQuery('CREATE TABLE IF NOT EXISTS `panels` (
		`id` int(11) NOT NULL auto_increment,
		`name` text,
		`body` text,
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=1;
}

$DBVARS[$pname.'|version']=$version;
config_rewrite();
