<?php
class ProductCategory{
	static $instances       = array();
	static $instancesByName = array();
	function __construct($v,$r=false){
		$v=(int)$v;
		if($v==0 && !$r)$r=array(
			'id'=>0,
			'name'=>'root',
			'enabled'=>true,
			'parent_id'=>-1
		);
		if($v<0)return;
		if(!$r && $v){
			$fname='category_'.$v;
			$r=cache_load('product_lists',$fname);
			if($r===false){
				$r=dbRow("select * from product_category where id=$v limit 1");
				cache_save('product_lists',$fname,$r);
			}
		}
		if(!count($r))return false;
		foreach ($r as $k=>$v) $this->{$k}=$v;
		$this->dbVals=$r;
		self::$instances[$this->id] =& $this;
		self::$instancesByName[$this->name] =& $this;
	}
	static function getInstance($id=0,$r=false){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new ProductCategory($id,$r);
		return self::$instances[$id];
	}
	static function getInstanceByName($name='',$r=false){
		if (!@array_key_exists($name,self::$instancesByName)){
			$r=dbRow('select * from product_category where name="'.addslashes($name).'" and enabled');
			new ProductCategory($r['id'],$r);
		}
		return self::$instancesByName[$name];
	}
	function getChildProductsRows($enabled=true){
		if(!$this->id)return array();
		if(isset($this->childProductsRows))return $this->childProductsRows;
		$fname='childproducts_of_'.$this->id.($enabled?',enabled':'');
		$rs=cache_load('product_lists',$fname);
		if($rs===false){
			$filter=$enabled?' and enabled ':'';
			$rs=dbAll("select * from products,product_category_product where product_id=id and category_id='$this->id' $filter order by name");
			cache_save('product_lists',$fname,$rs);
		}
		$this->childProductsRows=$rs;
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
		return '/common/redirector.php?type=product_category&id='.$this->id;
	}
	function getPageURL(){
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
		$fname='childcategories_of_'.$this->id.($enabled?',enabled':'');
		$rs=cache_load('product_lists',$fname);
		if($rs===false){
			$filter=$enabled?' AND enabled ':'';
			$rs=dbAll("SELECT * FROM product_category WHERE parent_id='$this->id' $filter ORDER BY name");
			cache_save('product_lists',$fname,$rs);
		}
		$this->childCategories=array();
		foreach($rs as $r)$this->childCategories[]=ProductCategory::getInstance($r['id'],$r);
		return $this->childCategories;
	}
	function getRecursiveCategoryIds($ids=array()){
		$ids[]=$this->id;
		$subcats=$this->getChildCategories();
		foreach($subcats as $c){
			$ids=$c->getRecursiveCategoryIds($ids);
		}
		return $ids;
	}
	function getRecursiveProducts(){
		$ps=$this->getRecursiveCategoryIds();
		$ps=dbAll('select product_id from product_category_product where category_id in ('.join(',',$ps).')');
		$ids=array();
		foreach($ps as $r)$ids[]=$r['product_id'];
		return $ids;
	}
}
