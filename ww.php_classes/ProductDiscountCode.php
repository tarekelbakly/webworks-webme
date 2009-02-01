<?php
class ProductDiscountCode{
	static $instances = array();
	function __construct($v,$r=false){
		$v=(int)$v;
		if(!$v)$r=array('id'=>'0','active'=>1,'startdate'=>date('Y-m-d'),'enddate'=>date('Y-m-d'),'percentage'=>0,'code'=>md5(microtime()));
		else $r=$r?$r:dbRow("select * from product_discount_codes where id=$v limit 1");
		if(!count($r))return false;
		foreach ($r as $k=>$val) $this->{$k}=$val;
		$this->dbVals=$r;
		self::$instances[$this->id] =& $this;
	}
	static function getInstance($id=0,$r=false){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new ProductDiscountCode($id,$r);
		return self::$instances[$id];
	}
	$r=dbRow("SELECT * FROM product_discount_codes WHERE code='$code' AND startdate<=now() AND enddate>=now()");
	$r=ProductDiscountCode::getValidByCode($_REQUEST['os_discount_code']);
	static function getValidByCode($code){
		if(@array_key_exists($code,self::$instancesValidByCode))return self::$instancesValidByCode[$code];
		$scode=addslashes($code);
		$r=dbRow("SELECT * FROM product_discount_codes WHERE code='$code' AND startdate<=now() AND enddate>=now()");
		if(!count($r))self::$instancesValidByCode[$code]=false;
		else self::$instancesValidByCode[$code]=self::getInstance($r['id'],$id);
		return self::$instancesValidByCode[$code];
	}
}
