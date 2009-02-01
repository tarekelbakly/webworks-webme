<?php
class ProductType{
	static $instances = array();
	static $instancesByName=array();
	function __construct($v,$r=false){
		$v=(int)$v;
		if(!$v)$r=array('name'=>'default template','short_template'=>'','long_template'=>'','id'=>0,'has_prices'=>0,'uses_stock_control'=>0);
		else $r=$r?$r:dbRow("select * from product_types where id=$v limit 1");
		if(!count($r))return false;
		foreach ($r as $k=>$val) $this->{$k}=$val;
		$this->dbVals=$r;
		self::$instances[$this->id] =& $this;
		self::$instancesByName[$this->name] =& $this;
	}
	static function getInstance($id=0,$r=false){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new ProductType($id,$r);
		return self::$instances[$id];
	}
	static function getInstanceByName($name){
		if(!$name) return false;
		if(array_key_exists($name,self::$instancesByName))return self::$instancesByName[$name];
		$r=dbRow("select id from product_types where name='".addslashes($name)."'");
		if(!count($r))return false;
		new ProductType($r['id'],$r);
		return self::$instances[$r['id']];
	}
}
