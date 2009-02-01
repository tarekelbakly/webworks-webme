<?php
class ProductOrders{
	static $instancesByFilter = array();
	static $instancesByUserId = array();
	static function getInstancesByUserid($id=0){
		if (!is_numeric($id)) return false;
		$id=(int)$id;
		if(array_key_exists($id,self::$instancesByUserId))return self::$instancesByUserId[$id];
		$rs=dbAll("select * from product_orders where userid=$id order by date_created desc");
		self::$instancesByUserId[$id]=array();
		foreach($rs as $r)self::$instancesByUserId[$id][]=ProductOrder::getInstance($r['id'],0,$r);
		return self::$instancesByUserId[$id];
	}
	static function getInstancesByFilter($sql){
		if(array_key_exists($sql,self::$instancesByFilter))return self::$instancesByFilter[$sql];
		$rs=dbAll("select * from product_orders where $sql order by status,date_created desc");
		self::$instancesByFilter[$sql]=array();
		foreach($rs as $r)self::$instancesByFilter[$sql][]=ProductOrder::getInstance($r['id'],0,$r);
		return self::$instancesByFilter[$sql];
	}
}
