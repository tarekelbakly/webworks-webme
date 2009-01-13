<?php
class ProductCategory{
	static $instances = array();
	function __construct($v,$r=false){
		$v=(int)$v;
		if(!$v)return;
		if(!$r)$r=dbRow("select * from product_category where id=$v limit 1");
		if(!count($r))return false;
		foreach ($r as $k=>$v) $this->{$k}=$v;
		$this->dbVals=$r;
		self::$instances[$this->id] =& $this;
	}
	function getInstance($id=0,$r=false){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new ProductCategory($id,$r);
		return self::$instances[$id];
	}
	function getRelativeURL(){
		if(isset($this->page_id) && $this->page_id)return Page::getInstance($this->page_id)->getRelativeURL();
		$id=$this->id;
		$pid=dbOne("select page_id from page_vars where name='category_to_show' and value=$id",'page_id');
		if($pid){
			$this->page_id=$pid;
			return Page::getInstance($this->page_id)->getRelativeURL();
		}
		return 'NO_PAGE_FOUND';
	}
}
