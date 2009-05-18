<?php
class ProductOrder{
	static $instances = array();
	function __construct($v,$uid=0,$r=false){
		$v=(int)$v;
		if(!$v)return;
		$filter=$uid?' and userid='.((int)$uid):'';
		$r=$r?$r:dbRow("select * from product_orders where id=$v $filter limit 1");
		if(!count($r))return false;
		foreach ($r as $k=>$val) $this->{$k}=$val;
		$this->dbVals=$r;
		self::$instances[$this->id] =& $this;
	}
	static function getInstance($id=0,$uid=0,$r=false){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new ProductOrder($id,$uid,$r);
		return self::$instances[$id];
	}
}
