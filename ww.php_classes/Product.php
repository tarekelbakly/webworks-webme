<?php
class Product{
	static $instances = array();
	function __construct($v,$r=false,$values=false,$enabled=true){
		$v=(int)$v;
		if(!$v)return;
		$filter=$enabled?' and enabled ':'';
		if(!$r)$r=dbRow("select * from products where id=$v $filter limit 1");
		if(!count($r) || !is_array($r))return false;
		foreach ($r as $k=>$val) $this->{$k}=$val;
		if(!isset($this->id))return false;
		$this->dbVals=$r;
		// { set up values if they are supplied. otherwise, "lazy load" them
		$this->__hasValues=false;
		if($values)$this->initValues($values);
		// }
		self::$instances[$this->id] =& $this;
	}
	function getImage($size){
		$default_image='';
		$images=array();
		if(is_dir('f/product_images/product'.$this->id)){
			$imagedir=new DirectoryIterator('f/product_images/product'.$this->id);
			foreach($imagedir as $image){
				if($image->isDot())continue;
				$default_image='product_images/product'.$this->id.'/'.$image;
				$images[]='product_images/product'.$this->id.'/'.$image;
			}
		}
		if($default_image){ // has at least one image
			return '/kfmgetfull/'.$default_image.',width='.$size.',height='.$size;
		}
		else return ImageNotFound::getInstance($size)->getRelativeURL();
	}
	static function getInstance($id=0,$r=false,$vals=false,$enabled=true){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new Product($id,$r,$vals,$enabled);
		if(!isset(self::$instances[$id]))return false;
		return self::$instances[$id];
	}
	function getRelativeURL(){
		return '/common/redirector.php?type=product&id='.$this->id;
	}
	function getBuyMultipleWidget(){
		$is_available=true;
		$disabled=$is_available?'':' disabled="disabled" title="'.__('out of stock').'"';
		return '<form class="os_add_multiple" method="post"><input type="hidden" name="product_id" value="'.$this->id.'" /><input name="amount" class="add_multiple_widget_amount" value="1" style="width:50px"'.$disabled.' /><input name="os_action" class="add_to_cart" type="submit"'.$disabled.' value="'.__('add to cart').'" /></span></form>';

	}
	function getPageURL(){
		if(isset($this->pageURL))return $this->pageURL;
		$basehref='';
		$id=$this->id;
		$cid=dbOne("select category_id from product_category_product where product_id=$id limit 1",'category_id');
		if($cid){
			$p=ProductCategory::getInstance($cid);
			$basehref=$p->getPageURL();
		}
		else if(isset($GLOBALS['PAGEDATA']) && $GLOBALS['PAGEDATA']->type==8)$basehref=$GLOBALS['PAGEDATA']->getPageURL();
		else{
			$r=Page::getInstanceByType(8);
			if($r)$basehref=@$r->getPageURL();
		}
		$this->pageURL=$basehref.'?product_id='.$this->id;
		return $this->pageURL;
	}
	function getPriceString(){
		if(isset($this->price_string))return $this->price_string;
		$this->initValues();
		$price=isset($this->__values['price'])?(float)$this->__values['price']:0;
		$trade_price=isset($this->__values['trade_price'])?(float)$this->__values['trade_price']:0;
		$sale_price=isset($this->__values['sale_price'])?(float)$this->__values['sale_price']:0;
		$bulk_price=isset($this->__values['bulk_price'])?(float)$this->__values['bulk_price']:0;
		$bulk_amount=isset($this->__values['bulk_amount'])?(int)$this->__values['bulk_amount']:0;
		$p1=(float)$sale_price;
		if($p1)$tmp=__('Price').': <strike class="os_price">'.osToPrice($price).'</strike> <strong class="os_price">'.osToPrice($p1).'</strong>';
		else $tmp=__('Price').': <strong class="os_price">'.osToPrice($price).'</strong>';
		if($bulk_price && $bulk_amount)$tmp.='<br />'.__('%1 for %2 or more',osToPrice($bulk_price),$bulk_amount);
		$pricestring='<span class="os_full_price">'.$tmp.'</span>';
		$this->price_string=$pricestring;
		return $pricestring;
	}
	function initValues($values=false){
		if($this->__hasValues)return $this;
		$values=is_array($values)?$values:dbAll("SELECT * FROM products_values WHERE product_id=$this->id");
		foreach($values as $pdv)$this->{$pdv['varname']}=$pdv['varvalue'];
		$this->__values=array();
		foreach($values as $pdv)$this->__values[$pdv['varname']]=$pdv['varvalue'];
		$this->__hasValues=true;
		return $this;
	}
	function set($name,$value){
		dbQuery('update products set '.addslashes($name).'="'.addslashes($value).'" where id='.$this->id);
		$this->{$name}=$value;
	}
}
