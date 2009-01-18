<?php
class ProductVouchers{
	static $instancesByFilter = array();
	function getAll(){
		return ProductVouchers::getInstancesByFilter();
	}
	function getInstancesByFilter($sql='1'){
		if(array_key_exists($sql,self::$instancesByFilter))return self::$instancesByFilter[$sql];
		$rs=dbAll("select * from product_vouchers where $sql order by email");
		self::$instancesByFilter[$sql]=array();
		foreach($rs as $r)self::$instancesByFilter[$sql][]=ProductVoucher::getInstance($r['id'],$r);
		return self::$instancesByFilter[$sql];
	}
}
