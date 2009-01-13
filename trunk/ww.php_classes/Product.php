<?php
class Product{
	static $instances = array();
	function __construct($v,$r=false){
		$v=(int)$v;
		if(!$v)return;
		if(!$r)$r=dbRow("select * from products where id=$v limit 1");
		if(!count($r))return false;
		foreach ($r as $k=>$v) $this->{$k}=$v;
		$this->dbVals=$r;
		self::$instances[$this->id] =& $this;
	}
	function getInstance($id=0,$r=false){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new Product($id,$r);
		return self::$instances[$id];
	}
}
