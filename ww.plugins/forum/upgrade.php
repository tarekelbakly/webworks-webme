<?php
/**
  * upgrade script for Forum plugin
  *
  * PHP Version 5
  *
	* @category   Whatever
  * @package    WebworksWebme
  * @subpackage Forum
  * @author     Kae Verens <kae@webworks.ie>
  * @license    GPL Version 2
  * @link       www.webworks.ie
 */

if ($version==0) { // forums table
	dbQuery(
		'CREATE TABLE IF NOT EXISTS `forums` (
			`id` int NOT NULL auto_increment primary key,
			`page_id` int,
			`parent_id` int default 0,
			`name` text
		) ENGINE=MyISAM DEFAULT CHARSET=utf8'
	);
	dbQuery(
		'CREATE TABLE IF NOT EXISTS `forums_threads` (
			`id` int NOT NULL auto_increment primary key,
			`forum_id` int,
			`sticky` tinyint default 0,
			`name` text,
			`creator_id` int,
			`created_date` datetime,
			`num_posts` int,
			`last_post_date` datetime,
			`last_post_by` int
		) ENGINE=MyISAM DEFAULT CHARSET=utf8'
	);
	dbQuery(
		'CREATE TABLE IF NOT EXISTS `forums_posts` (
			`id` int NOT NULL auto_increment primary key,
			`thread_id` int,
			`author_id` int,
			`created_date` datetime,
			`body` text
		) ENGINE=MyISAM DEFAULT CHARSET=utf8'
	);
	$version=1;
}

$DBVARS[$pname.'|version']=$version;
config_rewrite();
