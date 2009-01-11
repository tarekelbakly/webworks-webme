<?php
$version=0;
require '../common.php';
if(isset($DBVARS['version']))$version=(int)$DBVARS['version'];
if($version==0){ // missing user accounts and groups
	// { user_accounts
	mysql_query('create table user_accounts(
		id int auto_increment not null primary key,
		email text default "",
		name text default "",
		password varchar(32),
		phone text default "",
		active smallint default 0,
		address text,
		parent int default 0
	)default charset=utf8');
	// }
	// { groups
	mysql_query('create table groups(
		id int auto_increment not null primary key,
		name text,
		parent int default 0
	)default charset=utf8');
	// }
	// { users_groups
	mysql_query('create table users_groups(
		user_accounts_id int default 0,
		groups_id int default 0
	)default charset=utf8');
	// }
	echo '<p>Database upgraded - you will need to create an admin by inserting appropriate values into the tables user_accounts, groups and users_groups (use 1 as the primary key for each). Future upgrades should not require any manual action at all.</p>';
	$version=1;
}
if($version==1){ // add .private/.htaccess
	if(file_put_contents('../.private/.htaccess',"order allow,deny\ndeny from all"))$version=2;
	else echo '<p>Error: could not create <code>.private/.htaccess</code>. Please make sure the <code>.private</code> directory is writable by the server.</p>';
}
if($version==2){ // admin vars
	mysql_query('CREATE TABLE `admin_vars` ( `admin_id` int(11) default 0, `varname` text, `varvalue` text) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=3;
}
if($version==3){ // pages
	mysql_query('CREATE TABLE `pages` ( `id` int(11) NOT NULL auto_increment, `name` text, `body` mediumtext, `parent` int(11) default 0, `ord` int(11) NOT NULL default 0,
		`cdate` datetime NOT NULL default "0000-00-00 00:00:00", `special` bigint(20) default NULL, `edate` datetime default NULL, `assocDate` date default NULL, `title` text,
		`htmlheader` text, `template` text, `type` smallint(6) default 0, `keywords` text, `description` text, `category` text NOT NULL, `importance` float default 0.5,
		PRIMARY KEY  (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=4;
}
if($version==4){ // page_vars
	mysql_query('CREATE TABLE `page_vars` (`page_id` int(11) default NULL,`name` text,`value` text) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=5;
}
if($version==5){ // site_vars
	mysql_query('CREATE TABLE `site_vars` ( `name` text, `value` text) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=6;
}
if($version==6){ // permissions
	mysql_query('CREATE TABLE `permissions` ( `id` int(11) default 0, `type` int(11) default 0, `value` text) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=7;
}
if($version==7){ // blog indexes
	mysql_query('CREATE TABLE `blog_indexes` ( `pageid` int(11) default NULL, `parent` int(11) default NULL, `rss` text, `amount_to_show` int(11) default 0) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=8;
}
if($version==8){ // remove blog indexes - will rewrite this section at a later date
	mysql_query('DROP TABLE `blog_indexes`');
	$version=9;
}
if($version==9){ // comments
	mysql_query('CREATE TABLE `comments` ( `id` int(11) NOT NULL auto_increment, `objectid` int(11) default 0, `name` text, `email` text, `homepage` text, `comment` text, `cdate` datetime default NULL, `isvalid` smallint(6) default 0, `verificationhash` char(28) default NULL, PRIMARY KEY  (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=10;
}
if($version==10){ // set default theme
	$DBVARS['theme']='.default';
	$version=11;
}

$DBVARS['version']=$version;
config_rewrite();

echo '<p>Site upgraded. Please <a href="/">click here</a> to return to the site.</p><script type="text/javascript">setTimeout(function(){document.location="/";},3000);</script>';
