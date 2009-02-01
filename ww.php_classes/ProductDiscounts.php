<?php
class ProductDiscounts{
	static $instances = array();
	static $instancesByFilter = array();
	function __construct(){
	}
	static function getAll(){
		if(count(self::$instances))return self::$instances;
		$rs=dbAll("select * from product_discounts");
		foreach($rs as $r)self::$instances[$r['id']]=ProductDiscount::getInstance($r['id'],$r);
		return self::$instances;
	}
	static function getByFilter($filter=''){
		if(array_key_exists($filter,self::$instancesByFilter))return self::$instancesByFilter[$filter];
		$rs=dbAll("select * from product_discounts $filter");
		self::$instancesByFilter[$filter]=array();
		foreach($rs as $r)self::$instancesByFilter[$filter][$r['id']]=ProductDiscount::getInstance($r['id'],$r);
		return self::$instancesByFilter[$filter];
	}
}
