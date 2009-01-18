<?php
class ProductCheckout{
	static $instances = array();
	function __construct($v){
		$v=(int)$v;
		if($v<0)return;
		$r=$v?
			dbRow("select * from product_checkouts where id=$v limit 1"):
			array('name'=>'default checkout','countries'=>'','id'=>0,'currency'=>'','form_id'=>0,'vat'=>0,'invoice'=>'');
		if(!count($r))return false;
		foreach ($r as $k=>$val) $this->{$k}=$val;
		$this->dbVals=$r;
		self::$instances[$this->id] =& $this;
	}
	function getInstance($id=0){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new ProductCheckout($id);
		return self::$instances[$id];
	}
}
