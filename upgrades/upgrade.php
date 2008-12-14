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

$config='<'."?php
\$DBVARS=array(
	'username' => '".addslashes($DBVARS['username'])."',
	'password' => '".addslashes($DBVARS['password'])."',
	'hostname' => '".addslashes($DBVARS['hostname'])."',
	'db_name'  => '".addslashes($DBVARS['db_name'])."',
	'version'  => $version
);";

file_put_contents('../.private/config.php',$config);

echo '<p>Site upgraded. Please <a href="/">click here</a> to return to the site.</p>';
