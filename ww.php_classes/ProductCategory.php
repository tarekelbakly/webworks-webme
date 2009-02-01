<?php
class ProductCategory{
	static $instances = array();
	function __construct($v,$r=false){
		$v=(int)$v;
		if($v==0 && !$r)$r=array(
			'id'=>0,
			'name'=>'root',
			'enabled'=>true,
			'parent_id'=>-1
		);
		if($v<0)return;
		if(!$r && $v)$r=dbRow("select * from product_category where id=$v limit 1");
		if(!count($r))return false;
		foreach ($r as $k=>$v) $this->{$k}=$v;
		$this->dbVals=$r;
		self::$instances[$this->id] =& $this;
	}
	static function getInstance($id=0,$r=false){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new ProductCategory($id,$r);
		return self::$instances[$id];
	}
	function getChildProductsRows($enabled=true){
		if(isset($this->childProductsRows))return $this->childProductsRows;
		$filter=$enabled?' and enabled ':'';
		$this->childProductsRows=dbAll("select * from products,product_category_product where enabled and product_id=id and category_id='$this->id' $filter order by name");
		return $this->childProductsRows;
	}
	function getChildProducts($enabled=true){
		if(isset($this->childProducts))return $this->childProducts;
		$this->childProducts=array();
		$rs=$this->getChildProductsRows($enabled);
		foreach($rs as $r)$this->childProducts[]=Product::getInstance($r['id'],$r);
		return $this->childProducts;
	}
	function getRelativeURL(){
		if(isset($this->page_id) && $this->page_id)return Page::getInstance($this->page_id)->getRelativeURL();
		$id=$this->id;
		$r=PageVars::getByNameAndValue('category_to_show',$id,true);
		$pid=$r?$r['page_id']:0;
		if($pid){
			$this->page_id=$pid;
			return Page::getInstance($this->page_id,$r)->getRelativeURL();
		}
		return 'NO_PAGE_FOUND ('.$id.')';
	}
	function getChildCategories($enabled=true){
		if(isset($this->childCategories))return $this->childCategories;
		$filter=$enabled?' AND enabled ':'';
		$r=dbAll("SELECT * FROM product_category WHERE parent_id='$this->id' $filter ORDER BY name");
		$this->childCategories=array();
		foreach($r as $c)$this->childCategories[]=ProductCategory::getInstance($c['id'],$c);
		return $this->childCategories;
	}
}
