<?php
class ProductTypeField{
	static $instances = array();
	function __construct($pid,$name,$r=false){
		$pid=(int)$pid;
		if(!$pid)return false;
		$r=$r?$r:dbRow("select * from product_types_fields where product_type_id=$pid and name='".addslashes($name)."' limit 1");
		if(!count($r))return false;
		foreach ($r as $k=>$val) $this->{$k}=$val;
		self::$instances[$pid][$name] = &$this;
	}
	function getInstance($pid,$name,$r=false){
		if (!is_numeric($pid)) return false;
		if(!array_key_exists($pid,self::$instances))self::$instances[$pid]=array();
		if(!array_key_exists($name,self::$instances[$pid]))new ProductTypeField($pid,$name,$r);
		return self::$instances[$pid][$name];
	}
}
