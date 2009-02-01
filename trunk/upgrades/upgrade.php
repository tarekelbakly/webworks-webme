<?php
$version=0;
require '../common.php';
if(isset($DBVARS['version']))$version=(int)$DBVARS['version'];
echo '<strong>upgrades detected. running upgrade script.</strong>';
// {
	$db=dbInit();
	if(isset($DBVARS['userbase']))$userbase=$DBVARS['userbase'];
	else $userbase=SCRIPTBASE;
// }
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
	if(file_exists('../.private/.htaccess') || file_put_contents('../.private/.htaccess',"order allow,deny\ndeny from all"))$version=2;
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
	mysql_query("INSERT INTO `pages` VALUES (1,'Home','<h1>Welcome</h1>\\r\\n<p>This is your new website. To administer it, please go to <a href=\\\"/ww.admin/\\\">/ww.admin</a> and log in using your email address and password. If you have forgotten your password, please use the reminder form to have a new password sent to you.</p>\\r\\n<p>If you don\\'t like the default theme, please choose a different one in the Site Options page.</p>\\r\\n<p>To keep uptodate with new developments in the CMS, please bookmark <a href=\\\"http://webme.eu/whats-new\\\">http://webme.eu/whats-new</a>.</p>',0,1,now(),1,now(),NULL,'',NULL,'',0,'','','',0.5)");
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
	if(!isset($DBVARS['theme']))$DBVARS['theme']='.default';
	$version=11;
}
if($version==11){ // smarty template_c directory
	$dir=$userbase. 'templates_c';
	if(!is_dir($dir)){
		mkdir($dir);
		if(!is_dir($dir))echo '<p>Error: could not create directory <code>'.$dir.'</code>. Please make sure that <code>'.$userbase.'</code> is writable by the server.</p>';
	}
	if(is_dir($dir)){
		touch($dir.'/test.txt');
		if(!file_exists($dir.'/test.txt')){
			echo '<p>Error: could not create test file <code>'.$dir.'/test.txt</code>. Please make sure that <code>'.$dir.'</code> is writable by the server.</p>';
		}
		else{
			unlink($dir.'/test.txt');
			$version=12;
		}
	}
}
if($version==12){ // tmp files directory
	$dir=$userbase . 'f';
	if(!is_dir($dir)){
		mkdir($dir);
		if(!is_dir($dir))echo '<p>Error: could not create directory <code>'.$dir.'</code>. Please make sure that <code>'.$userbase.'</code> is writable by the server.</p>';
	}
	if(is_dir($dir)){
		$dir=$dir.'/.files';
		if(!is_dir($dir)){
			mkdir($dir);
			if(!is_dir($dir))echo '<p>Error: could not create directory <code>'.$dir.'</code>. Please make sure that <code>'.$userbase.'f/</code> is writable by the server.</p>';
		}
		if(is_dir($dir)){
			touch($dir.'/test.txt');
			if(!file_exists($dir.'/test.txt')){
				echo '<p>Error: could not create test file <code>'.$dir.'/test.txt</code>. Please make sure that <code>'.$dir.'</code> is writable by the server.</p>';
			}
			else{
				unlink($dir.'/test.txt');
				$version=13;
			}
		}
	}
}
if($version==13){ // set default theme
	$DBVARS['site_title']='Site Title';
	$DBVARS['site_subtitle']='Website\'s Subtitle';
	$version=14;
}
if($version==14){ // set USERBASE define
	if(!isset($DBVARS['userbase']))$DBVARS['userbase']=SCRIPTBASE;
	$version=15;
}
if($version==15){ // page summaries
	mysql_query('create table page_summaries(page_id int default 0,parent_id int default 0,rss text,amount_to_show int default 0)default charset=utf8');
	$version=16;
}
if($version==16){ // skins directory
	if(!isset($DBVARS['theme_dir']))$DBVARS['theme_dir']=$_SERVER['DOCUMENT_ROOT'].'/ww.skins';
	$version=17;
}

$DBVARS['version']=$version;
config_rewrite();

header('Location: /');
echo '<p>Site upgraded. Please <a href="/">click here</a> to return to the site.</p>';
