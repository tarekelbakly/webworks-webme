<?php
class ProductVoucher{
	static $instances = array();
	static $instancesByCode = array();
	function __construct($v,$r=false){
		$v=(int)$v;
		if($v<0)return;
		$r=$r?$r:dbRow("select * from product_vouchers where id=$v limit 1");
		if(!count($r))$r=array('code'=>'','value'=>'','id'=>0,'email'=>'');
		foreach ($r as $k=>$val) $this->{$k}=$val;
		$this->dbVals=$r;
		self::$instances[$this->id] =& $this;
		self::$instancesByCode[$this->code] =& $this;
	}
	function getInstance($id=0,$r=false){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new ProductVoucher($id,$r);
		return self::$instances[$id];
	}
	function getInstanceByCode($code=0,$r){
		if (!$code) return false;
		if(!$r)$r=dbRow("SELECT * FROM product_vouchers WHERE code='$code' LIMIT 1");
		if (!@array_key_exists($code,self::$instancesByCode)) new ProductVoucher($r['id'],$r);
		return self::$instancesByCode[$code];
	}
}
