<?php
if(!file_exists(USERBASE.'/ww.cache/products')){
	mkdir(USERBASE.'/ww.cache/products');
	mkdir(USERBASE.'/ww.cache/products/templates');
	mkdir(USERBASE.'/ww.cache/products/templates_c');
}
function products_show($PAGEDATA){
	if(!isset($PAGEDATA->vars['products_what_to_show']))$PAGEDATA->vars['products_what_to_show']='0';
	switch($PAGEDATA->vars['products_what_to_show']){
		case '1':
			return products_show_by_type($PAGEDATA);
		case '2':
			return products_show_by_category($PAGEDATA);
		case '3':
			return products_show_by_id($PAGEDATA);
	}
	return products_show_all($PAGEDATA);
}
function products_show_by_id($PAGEDATA,$id=0){
	if($id==0){
		$id=(int)$PAGEDATA->vars['products_product_to_show'];
	}
	if($id<1)return '<em>product '.$id.' does not exist.</em>';
	$product=Product::getInstance($id);
	$type=ProductType::getInstance($product->get('product_type_id'));
	if(!$type)return '<em>product type '.$product->get('product_type_id').' does not exist.</em>';
	return $type->render($product);
}
function products_show_by_category($PAGEDATA){
	if($id==0){
		$id=(int)$PAGEDATA->vars['products_category_to_show'];
	}
	$products=Products::getByCategory($id);
	return $products->render();
}
function products_show_by_type($PAGEDATA,$id=0){
	if($id==0){
		$id=(int)$PAGEDATA->vars['products_type_to_show'];
	}
	$products=Products::getByType($id);
	return $products->render();
}
function products_show_all($PAGEDATA){
	$products=Products::getAll();
	return $products->render();
}
function products_setup_smarty(){
	$smarty=new Smarty();
	$smarty->compile_dir=USERBASE.'/ww.cache/products/templates_c';
	$smarty->left_delimiter = '{{';
	$smarty->right_delimiter = '}}';
	$smarty->template_dir='/ww.cache/products/templates';
	return $smarty;
}

class Product{
	static $instances=array();
	function __construct($v,$r=false,$enabled=true){
		$v=(int)$v;
		if($v<1)return false;
		$filter=$enabled?' and enabled ':'';
		if(!$r)$r=dbRow("select * from products where id=$v $filter limit 1");
		if(!count($r) || !is_array($r))return false;
		$vals=json_decode($r['data_fields']);
		unset($r['data_fields']);
		$this->vals=array();
		foreach($r as $k=>$v)$this->vals[$k]=$v;
		foreach($vals as $val){
			$this->vals[preg_replace('/[^a-zA-Z0-9\-_]/','_',$val->n)]=$val->v;
		}
		$this->id=$r['id'];
		self::$instances[$this->id] =& $this;
		return $this;
	}
	function getInstance($id=0,$r=false,$enabled=true){
		if (!is_numeric($id)) return false;
		if (!array_key_exists($id,self::$instances))return new Product($id,$r,$enabled);
		return false;
	}
	function get($name){
		if(isset($this->vals[$name]))return $this->vals[$name];
		return false;
	}
}
class Products{
	static $instances=array();
	function __construct($v,$id){
		$this->product_ids=$v;
		self::$instances[$id]=& $this;
		return $this;
	}
	function getAll(){
		if(!array_key_exists('all',self::$instances)){
			$product_ids=array();
			$rs=dbAll('select id from products');
			foreach($rs as $r)$product_ids[]=$r['id'];
			new Products($product_ids,'all');
		}
		return self::$instances['all'];
	}
	function getByCategory($id){
		if(!is_numeric($id)) return false;
		if(!array_key_exists($id,self::$instances)){
			$product_ids=array();
			$rs=dbAll('select product_id from products_categories_products where category_id='.$id);
			foreach($rs as $r)$product_ids[]=$r['product_id'];
			new Products($product_ids,$id);
		}
		return self::$instances[$id];
	}
	function getByType($id){
		if(!is_numeric($id)) return false;
		if(!array_key_exists($id,self::$instances)){
			$product_ids=array();
			$rs=dbAll('select id from products where product_type_id='.$id);
			foreach($rs as $r)$product_ids[]=$r['id'];
			new Products($product_ids,$id);
		}
		return self::$instances[$id];
	}
	function render(){
		$c='';
		foreach($this->product_ids as $pid){
			$product=Product::getInstance($pid);
			$type=ProductType::getInstance($product->get('product_type_id'));
			$c.=$type->render($product,'multiview');
		}
		return $c;
	}
}
class ProductType{
	static $instances=array();
	function __construct($v){
		$v=(int)$v;
		if($v<1)return false;
		$r=dbRow("select * from products_types where id=$v limit 1");
		if(!count($r))return false;
		$this->data_fields=json_decode($r['data_fields']);
		if(!file_exists(USERBASE.'/ww.cache/products/templates/types_multiview_'.$v)){
			file_put_contents(
				USERBASE.'/ww.cache/products/templates/types_multiview_'.$v,
				$r['multiview_template']
			);
		}
		unset($r['multiview_template']);
		if(!file_exists(USERBASE.'/ww.cache/products/templates/types_singleview_'.$v)){
			file_put_contents(
				USERBASE.'/ww.cache/products/templates/types_singleview_'.$v,
				$r['singleview_template']
			);
		}
		unset($r['singleview_template']);
		$this->id=$r['id'];
		self::$instances[$this->id] =& $this;
		return $this;
	}
	function getInstance($id=0){
		if (!is_numeric($id)) return false;
		if (!array_key_exists($id,self::$instances))new ProductType($id);
		return self::$instances[$id];
	}
	function render($product,$template='singleview'){
		$smarty=products_setup_smarty();
		foreach($this->data_fields as $f){
			$f->n=preg_replace('/[^a-zA-Z0-9\-_]/','_',$f->n);
			$val=$product->get($f->n);
			switch($f->t){
				default: // { everything else
					$smarty->assign($f->n,$val);
				// }
			}
		}
		return $smarty->fetch(USERBASE.'/ww.cache/products/templates/types_'.$template.'_'.$this->id);
	}
}
class ProductType_BAK{
	static $instances = array();
	static $instancesByName=array();
	function __construct($v,$r=false){
		$v=(int)$v;
		if(!$v)$r=array(
			'name'=>'default template',
			'short_template'=>'',
			'long_template'=>'',
			'id'=>0,
			'has_prices'=>0,
			'uses_stock_control'=>0,
			'longform_large_image_size'=>'250x250',
			'longform_thumb_image_size'=>64,
			'shortform_thumb_size'=>128,
			'products_per_page'=>10,
			'show_product_variants'=>1,
			'show_also_bought'=>1,
			'show_related_products'=>1,
			'show_contained_products'=>1
		);
		else $r=$r?$r:dbRow("select * from product_types where id=$v limit 1");
		if(!count($r))return false;
		foreach ($r as $k=>$val) $this->{$k}=$val;
		$this->dbVals=$r;
		self::$instances[$this->id] =& $this;
		self::$instancesByName[$this->name] =& $this;
	}
	static function getInstance($id=0,$r=false){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) new ProductType($id,$r);
		return self::$instances[$id];
	}
	static function getInstanceByName($name){
		if(!$name) return false;
		if(array_key_exists($name,self::$instancesByName))return self::$instancesByName[$name];
		$r=dbRow("select id from product_types where name='".addslashes($name)."'");
		if(!count($r))return false;
		new ProductType($r['id'],$r);
		return self::$instances[$r['id']];
	}
}
/*
mysql> describe products_types;
+-------------------------+-------------+------+-----+---------+----------------+
| Field                   | Type        | Null | Key | Default | Extra          |
+-------------------------+-------------+------+-----+---------+----------------+
| id                      | int(11)     | NO   | PRI | NULL    | auto_increment |
| name                    | text        | NO   |     | NULL    |                |
| multiview_template      | text        | YES  |     | NULL    |                |
| singleview_template     | text        | YES  |     | NULL    |                |
| show_product_variants   | smallint(6) | YES  |     | 1       |                |
| show_related_products   | smallint(6) | YES  |     | 1       |                |
| show_contained_products | smallint(6) | YES  |     | 1       |                |
| show_countries          | smallint(6) | YES  |     | 0       |                |
| data_fields             | text        | YES  |     | NULL    |                |
+-------------------------+-------------+------+-----+---------+----------------+
*/
class Product_BAK{
	static $instances = array();
	function __construct($v,$r=false,$values=false,$enabled=true){
		$v=(int)$v;
		if(!$v)return;
		$filter=$enabled?' and enabled ':'';
		if(!$r)$r=dbRow("select * from products where id=$v $filter limit 1");
		if(!count($r) || !is_array($r))return false;
		foreach ($r as $k=>$val) $this->{$k}=$val;
		if(!isset($this->weight) || !$this->weight){
			$pt=ProductType::getInstance($this->product_type_id);
			$this->weight=$pt->default_weight;
		}
		if(!isset($this->id))return false;
		$this->dbVals=$r;
		# { set up values if they are supplied. otherwise, "lazy load" them
		$this->__hasValues=false;
		if($values)$this->initValues($values);
		# }
		self::$instances[$this->id] =& $this;
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
	function getPageURL(){
		if(isset($this->pageURL))return $this->pageURL;
		$basehref='';
		$id=$this->id;
		$cid=dbOne("select category_id from product_category_product where product_id=$id limit 1",'category_id');
		if($cid){
			$p=ProductCategory::getInstance($cid);
			$refpage=false;
			if(isset($_SERVER['HTTP_REFERER'])){
				$name=preg_replace('#https?://[^/]*/([^?&]*).*#','\1',$_SERVER['HTTP_REFERER']);
				$refpage=Page::getInstanceByName($name);
			}
			$basehref=$p->getPageURL($refpage);
		}
		else if(isset($GLOBALS['PAGEDATA']) && $GLOBALS['PAGEDATA']->type==8)$basehref=$GLOBALS['PAGEDATA']->getPageURL();
		else{
			$r=Page::getInstanceByType(8);
			if($r)$basehref=@$r->getPageURL();
		}
		$this->pageURL=$basehref.'?product_id='.$this->id;
		return $this->pageURL;
	}
	function set($name,$value){
		dbQuery('update products set '.addslashes($name).'="'.addslashes($value).'" where id='.$this->id);
		$this->{$name}=$value;
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
}
