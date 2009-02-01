<?php
class ProductDiscount{
	static $instances = array();
	function __construct($v,$r=false){
		$v=(int)$v;
		if(!$v)$r=array('id'=>'0','type'=>'0','x'=>0,'discount'=>0);
		else $r=$r?$r:dbRow("select * from product_discounts where id=$v limit 1");
		if(!count($r))return false;
		foreach ($r as $k=>$val) $this->{$k}=$val;
		$this->dbVals=$r;
		self::$instances[$this->id] =& $this;
	}
	static function getInstance($id=0,$r=false){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new ProductDiscount($id,$r);
		return self::$instances[$id];
	}
}
