<?php
/*
	Webme Banner Image Plugin
	File: upgrade.php
	Developers: Conor Mac Aoidh <http://macaoidh.name>
	            Kae Verens <http://verens.com/>
	Report Bugs: <conor@macaoidh.name>
	             <kae@verens.com>
*/

if($version==0) $version='0.1';
if($version=='0.1'){ // banners_images and banners_pages
	dbQuery('create table if not exists banners_images( id int auto_increment not null primary key, html text)default charset=utf8;');
	dbQuery('create table if not exists banners_pages( pageid int, bannerid int);');
	if(file_exists(USERBASE.'f/skin_files/banner.png')){
		mkdir(USERBASE.'f/skin_files/banner-images');
		rename(USERBASE.'f/skin_files/banner.png',USERBASE.'f/skin_files/banner-images/1.png');
		dbQuery('insert into banners_images values(1,"")');
	}
	$version=1;
}
if($version=='1'){ // update table to allow choice of image/HTML
	dbQuery('alter table banners_images add type smallint default 0'); // 0 is image, 1 is HTML
	dbQuery('alter table banners_images add pages smallint default 0'); // 0 is all pages, 1 means check the banners_pages table
	$version=2;
}
$DBVARS[$pname.'|version']=$version;
config_rewrite();
