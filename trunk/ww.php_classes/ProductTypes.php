<?php
class ProductTypes{
	static $instances = array();
	static $instancesByFilter = array();
	function __construct(){
	}
	function getAll(){
		if(count(self::$instances))return self::$instances;
		$rs=dbAll("select * from product_types order by name");
		foreach($rs as $r)self::$instances[$r['id']]=ProductType::getInstance($r['id'],$r);
		return self::$instances;
	}
	function getByFilter($filter=''){
		if(array_key_exists($filter,self::$instancesByFilter))return self::$instancesByFilter[$filter];
		$rs=dbAll("select * from product_types $filter order by name");
		self::$instancesByFilter[$filter]=array();
		foreach($rs as $r)self::$instancesByFilter[$filter][$r['id']]=ProductType::getInstance($r['id'],$r);
		return self::$instancesByFilter[$filter];
	}
}
