<?php
class Products{
	static $instancesByFilter = array();
	static $instancesBySearch = array();
	static $instances = array();
	function __construct(){
	}
	function getAll($enabled=true){
		if(count(self::$instances))return self::$instances;
		$filter=$enabled?' WHERE enabled ':'';
		$rs=dbAll("select * from products $filter order by name");
		foreach($rs as $r)self::$instances[]=Product::getInstance($r['id'],$r);
		return self::$instances;
	}
	function getByFilter($filter=''){
		if(array_key_exists($filter,self::$instancesByFilter))return self::$instancesByFilter[$filter];
		$rs=dbAll("select * from products $filter order by name");
		foreach($rs as $r)self::$instancesByFilter[]=Product::getInstance($r['id'],$r);
		return self::$instancesByFilter[$filter];
	}
	function getBySearch($terms,$enabled=true){
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
