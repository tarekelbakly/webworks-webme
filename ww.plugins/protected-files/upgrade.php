<?php
if($version==0){ // protect_files table
	dbQuery('CREATE TABLE IF NOT EXISTS `protected_files` (
		`id` int(11) NOT NULL auto_increment,
		`directory` text,
		`recipient_email` text,
		PRIMARY KEY  (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=1;
}

$DBVARS[$pname.'|version']=$version;
config_rewrite();
