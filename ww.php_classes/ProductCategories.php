<?php
class ProductCategories{
	static $instances         = array();
	static $instancesByParent = array();
	function __construct(){
	}
	static function getAll(){
		if(count(self::$instances))return self::$instances;
		$rs=cache_load('product_lists','by_name');
		if($rs===false){
			$rs=dbAll('select * from product_category order by name');
			cache_save('product_lists','by_name',$rs);
		}
		foreach($rs as $r)self::$instances[]=ProductCategory::getInstance($r['id'],$r);
		return self::$instances;
	}
	static function getByParent($pid){
		if(!is_numeric($pid))return false;
		if(array_key_exists($pid,self::$instancesByParent))return self::$instancesByParent($pid);
		$rs=cache_load('product_lists','categories_by_parent_'.$pid);
		if($rs===false){
			$rs=dbAll("select * from product_category where parent_id=$pid order by name");
			cache_save('product_lists','categories_by_parent_'.$pid,$rs);
		}
		self::$instancesByParent[$pid]=array();
		foreach($rs as $r)self::$instancesByParent[$pid][]=ProductCategory::getInstance($r['id'],$r);
		return self::$instancesByParent[$pid];
	}
}
