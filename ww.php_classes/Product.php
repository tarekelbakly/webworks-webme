<?php
class Product{
	static $instances = array();
	function __construct($v,$r=false,$enabled=false){
		$v=(int)$v;
		if(!$v)return;
		$filter=$enabled?' and enabled ':'';
		if(!$r)$r=dbRow("select * from products where id=$v $filter limit 1");
		if(!count($r))return false;
		foreach ($r as $k=>$val) $this->{$k}=$val;
		$this->dbVals=$r;
		$pDataVars=dbAll("SELECT * FROM products_values WHERE product_id=$v");
		foreach($pDataVars as $pdv)$this->{$pdv['varname']}=$pdv['varvalue'];
		self::$instances[$this->id] =& $this;
	}
	function getImage($size){
		$default_image=$this->default_image;
		$imagedir=kfm_api_getDirectoryId('product_images/product'.$this->id);
		$GLOBALS['kfm_session']->set('cwd_id',$imagedir);
		if(!$default_image&&$imagedir){
			$res=kfm_loadFiles($imagedir);
			if(is_array($res)&&count($res['files'])){
				$default_image=$res['files'][0]['id'];
			}
		}
		if($default_image){ // has at least one image
			return '/kfmget/'.$default_image.',width='.$size.',height='.$size;
		}
		else return ImageNotFound::getInstance($size)->getRelativeURL();
	}
	function getInstance($id=0,$r=false){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new Product($id,$r);
		return self::$instances[$id];
	}
	function getRelativeURL(){
		if(isset($this->relativeURL))return $this->relativeURL;
		$basehref='';
		$id=$this->id;
		$cid=dbOne("select category_id from product_category_product where product_id=$id limit 1",'category_id');
		if($cid){
			$p=ProductCategory::getInstance($cid);
			$basehref=$p->getRelativeURL();
		}
		else if($GLOBALS['PAGEDATA']->type==8)$basehref=$GLOBALS['PAGEDATA']->getRelativeURL();
		else{
			$r=Page::getInstanceByType(8);
			if($r)$basehref=$r->getRelativeURL();
		}
		$this->relativeURL=$basehref.'?product_id='.$this->id;
		return $this->relativeURL;
	}
}
