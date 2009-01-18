<?php
class ProductCategories{
	static $instances = array();
	function __construct(){
	}
	function getAll(){
		if(count(self::$instances))return self::$instances;
		$rs=dbAll('select * from product_category order by name');
		foreach($rs as $r)self::$instances[]=ProductCategory::getInstance($r['id'],$r);
		return self::$instances;
	}
}
