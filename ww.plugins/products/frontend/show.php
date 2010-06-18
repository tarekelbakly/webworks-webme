<?php
if(!file_exists(USERBASE.'/ww.cache/products')){
	mkdir(USERBASE.'/ww.cache/products');
	mkdir(USERBASE.'/ww.cache/products/templates');
	mkdir(USERBASE.'/ww.cache/products/templates_c');
}
function products_get_add_to_cart_button(){
	return '<form method="POST"><input type="hidden" name="products_action" value="add_to_cart" /><input type="submit" value="Add to Cart" />'
		.'<input type="hidden" name="product_id" value="'.$GLOBALS['smarty_vars']['product_id'].'" /></form>';
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
function products_show_by_category($PAGEDATA,$id=0){
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
	$smarty->register_function('PRODUCTS_BUTTON_ADD_TO_CART','products_get_add_to_cart_button');
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
			$rs=dbAll('select id from products where enabled');
			foreach($rs as $r)$product_ids[]=$r['id'];
			new Products($product_ids,'all');
		}
		return self::$instances['all'];
	}
	function getByCategory($id){
		if(!is_numeric($id)) return false;
		if(!array_key_exists($id,self::$instances)){
			$product_ids=array();
			$rs=dbAll('select id from products,products_categories_products where id=product_id and enabled and category_id='.$id);
			foreach($rs as $r)$product_ids[]=$r['id'];
			new Products($product_ids,$id);
		}
		return self::$instances[$id];
	}
	function getByType($id){
		if(!is_numeric($id)) return false;
		if(!array_key_exists($id,self::$instances)){
			$product_ids=array();
			$rs=dbAll('select id from products where enabled and product_type_id='.$id);
			foreach($rs as $r)$product_ids[]=$r['id'];
			new Products($product_ids,$id);
		}
		return self::$instances[$id];
	}
	function render(){
		$c='';
		foreach($this->product_ids as $pid){
			$product=Product::getInstance($pid);
			if($product){
				$type=ProductType::getInstance($product->get('product_type_id'));
				if(!$type)$c.='Missing product type: '.$product->get('product_type_id');
				else $c.=$type->render($product,'multiview');
			}
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
		$GLOBALS['smarty_vars']=array(
			'product_id'=>$product->get('id')
		);
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
