<?php
class ProductTypeFields{
	static $instances = array();
	static $instancesByFilter = array();
	static function getInstances($product_type){
		if(array_key_exists($product_type,self::$instances))return self::$instances[$product_type];
		$product_type=(int)$product_type;
		$rs=dbAll("select * from product_types_fields where product_type_id=$product_type");
		self::$instances[$product_type]=array();
		foreach($rs as $r)self::$instances[$product_type][$r['name']]=ProductTypeField::getInstance($product_type,$r['name'],$r);
		return self::$instances[$product_type];
	}
	static function getAll($product_type){
		return self::getByFilter($product_type,'1');
	}
	static function getByFilter($product_type,$filter){
		if(array_key_exists($product_type,self::$instancesByFilter) && array_key_exists($filter,self::$instancesByFilter[$product_type]))return self::$instancesByFilter[$product_type][$filter];
		$product_type=(int)$product_type;
		$f=addslashes($filter);
		$rs=dbAll("select * from product_types_fields where $f and product_type_id=$product_type");
		if(!array_key_exists($product_type,self::$instancesByFilter))self::$instancesByFilter[$product_type]=array();
		self::$instancesByFilter[$product_type][$filter]=array();
		foreach($rs as $r)self::$instancesByFilter[$product_type][$filter][$r['name']]=ProductTypeField::getInstance($product_type,$r['name'],$r);
		return self::$instancesByFilter[$product_type][$filter];
	}
}
