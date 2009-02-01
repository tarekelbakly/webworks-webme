<?php
class Products{
	static $instancesByFilter = array();
	static $instancesBySearch = array();
	static $instancesOrderedByProductValue=array();
	static $instances = array();
	function __construct(){
	}
	static function getAll($enabled=true){
		if(count(self::$instances))return self::$instances;
		$filter=$enabled?' WHERE enabled ':'';
		$rs=dbAll("select * from products $filter order by name");
		foreach($rs as $r)self::$instances[]=Product::getInstance($r['id'],$r);
		return self::$instances;
	}
	static function getByFilter($filter=''){
		if(array_key_exists($filter,self::$instancesByFilter))return self::$instancesByFilter[$filter];
		$rs=dbAll("select * from products $filter");
		self::$instancesByFilter[$filter]=array();
		foreach($rs as $r)self::$instancesByFilter[$filter][]=Product::getInstance($r['id'],$r);
		return self::$instancesByFilter[$filter];
	}
	static function getByIds($ids=array()){
		$ids=addslashes(join(',',$ids));
		if(array_key_exists($ids,self::$instancesByIds))return self::$instancesByIds[$ids];
		self::$instancesByFilter[$ids]=array();
		$rs=dbAll("select * from products where id in ($ids)");
		foreach($rs as $r)self::$instancesByIds[$ids][]=Product::getInstance($r['id'],$r);
		return self::$instancesByIds[$ids];
	}
	static function getOrderedByProductValue($name){
		if(array_key_exists($name,self::$instancesOrderedByProductValue))return self::$instancesOrderedByProductValue[$name];
		self::$instancesOrderedByProductValue[$name]=array();
		$vn=addslashes($name);
		$rs=dbAll("select * from products,products_values where id=product_id and varname='$name' order by varvalue");
		foreach($rs as $r)self::$instancesOrderedByProductValue[$name][]=Product::getInstance($r['id'],$r);
		return self::$instancesOrderedByProductValue[$name];
	}
	static function getBySearch($terms,$enabled=true){
		if (!@array_key_exists($terms,self::$instancesBySearch)){
			$filter=$enabled?' AND enabled ':'';
			$arr=array();
			$rs=dbAll('select distinct product_id,products.* from products_values,products where varvalue like "%'.addslashes($terms).'%" AND enabled AND product_id=id');
			foreach($rs as $r)$arr[]=$r['id'];
			if(count($arr)){ // there are results
				$rs2=dbAll("SELECT * FROM products_values WHERE product_id IN (".join(',',$arr).")");
				$arr2=array();
				foreach($rs as $r){
					$arr=array();
					foreach($rs2 as $key=>$r2)if($r2['product_id']==$r['id']){
						$arr[]=$r2;
						unset($rs2[$key]);
					}
					$arr2[]=Product::getInstance($r['id'],$r,$arr);
				}
				self::$instancesBySearch[$terms]=$arr2;
			}
			else self::$instancesBySearch[$terms]=array();
		}
		return self::$instancesBySearch[$terms];
	}
}
