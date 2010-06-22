<?php
if($version==0){ // product types
	dbQuery('CREATE TABLE products_types (
	  id int(11) NOT NULL auto_increment,
	  name text NOT NULL,
		short_template text NOT NULL,
		long_template text NOT NULL,
		show_product_variants smallint(6) default 1,
		show_related_products smallint(6) default 1,
		show_contained_products smallint(6) default 1,
		show_countries smallint(6) default 0,
		PRIMARY KEY  (id)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=1;
}
if($version==1){ // data fields
	dbQuery('alter table products_types add data_fields text');
	$version=2;
}
if($version==2){ // multi- and single-view templates
	dbQuery('alter table products_types change short_template multiview_template text');
	dbQuery('alter table products_types change long_template singleview_template text');
	$version=3;
}
if($version==3){ // products table
	dbQuery('CREATE TABLE products (
		id int(11) NOT NULL auto_increment,
		name text,
		product_type_id int(11) default 0,
		default_image text,
		enabled smallint(6) default 1,
		date_created datetime default NULL,
		data_fields text,
		PRIMARY KEY  (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=4;
}
if($version==4){ // products_categories
	dbQuery('CREATE TABLE products_categories (
		id int(11) NOT NULL auto_increment,
		name text,
		parent_id int(11) default 0,
		enabled smallint(1) default 0,
		PRIMARY KEY  (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	dbQuery('insert into products_categories values(1,"default",0,1)');
	$version=5;
}
if($version==5){ // products_categories
	dbQuery('CREATE TABLE products_categories_products (
		product_id int(11) default 0,
		category_id int(11) default 0
		) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	$version=6;
}
if($version==6){ // product images
	dbQuery('alter table products add images_directory text');
	$version=7;
}
if($version==7){ // default image
	dbQuery('alter table products change default_image image_default int default 0');
	$version=8;
}

$DBVARS[$pname.'|version']=$version;
config_rewrite();
