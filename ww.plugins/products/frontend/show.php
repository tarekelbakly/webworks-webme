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
	$c='';
	// { search
	$search=isset($_REQUEST['products-search'])?$_REQUEST['products-search']:'';
	if(isset($PAGEDATA->vars['products_add_a_search_box']) && $PAGEDATA->vars['products_add_a_search_box']){
		$c.='<form action="'.$PAGEDATA->getRelativeUrl()
			.'" class="products-search"><input name="products-search" value="'
			.htmlspecialchars($search)
			.'" /><input type="submit" value="Search" /></form>';
	}
	// }
	// { set limit variables
	$limit=isset($PAGEDATA->vars['products_per_page'])?(int)$PAGEDATA->vars['products_per_page']:0;
	$start=isset($_REQUEST['start'])?(int)$_REQUEST['start']:0;
	if($start<0)$start=0;
	// }
	// { set order fields
	$order_by=isset($PAGEDATA->vars['products_order_by'])?$PAGEDATA->vars['products_order_by']:'';
	$order_dir=isset($PAGEDATA->vars['products_order_direction'])?(int)$PAGEDATA->vars['products_order_direction']:0;
	// }
	switch($PAGEDATA->vars['products_what_to_show']){
		case '1':
			return $c.products_show_by_type($PAGEDATA,0,$start,$limit,$order_by,$order_dir,$search);
		case '2':
			return $c.products_show_by_category($PAGEDATA,0,$start,$limit,$order_by,$order_dir,$search);
		case '3':
			return $c.products_show_by_id($PAGEDATA);
	}
	return $c.products_show_all($PAGEDATA,$start,$limit,$order_by,$order_dir,$search);
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
function products_show_by_category($PAGEDATA,$id=0,$start=0,$limit=0,$order_by='',$order_dir=0,$search=''){
	if($id==0){
		$id=(int)$PAGEDATA->vars['products_category_to_show'];
	}
	$products=Products::getByCategory($id,$search);
	return $products->render($PAGEDATA,$start,$limit,$order_by,$order_dir);
}
function products_show_by_type($PAGEDATA,$id=0,$start=0,$limit=0,$order_by='',$order_dir=0,$search=''){
	if($id==0){
		$id=(int)$PAGEDATA->vars['products_type_to_show'];
	}
	$products=Products::getByType($id,$search);
	return $products->render($PAGEDATA,$start,$limit,$order_by,$order_dir);
}
function products_show_all($PAGEDATA,$start=0,$limit=0,$order_by='',$order_dir=0,$search=''){
	$products=Products::getAll($search);
	return $products->render($PAGEDATA,$start,$limit,$order_by,$order_dir);
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
		return self::$instances[$id];
	}
	function get($name){
		if(isset($this->vals[$name]))return $this->vals[$name];
		return false;
	}
	function search($search){
		$pt=ProductType::getInstance($this->vals['product_type_id']);
		foreach($pt->data_fields as $df){
			if($df->s && strpos($this->get($df->n),$search)!==false)return true;
		}
		return false;
	}
}
class Products{
	static $instances=array();
	function __construct($vs,$id,$search=''){
		if($search!=''){
			$arr=array();
			foreach($vs as $v){
				$p=Product::getInstance($v);
				if(!$p)continue;
				if(!$p->search($search))continue;
				$arr[]=$v;
			}
			$vs=$arr;
		}
		$this->product_ids=$vs;
		self::$instances[$id]=& $this;
		return $this;
	}
	function getAll($search=''){
		$id=md5('all|'.$search);
		if(!array_key_exists($id,self::$instances)){
			$product_ids=array();
			$rs=dbAll('select id from products where enabled');
			foreach($rs as $r)$product_ids[]=$r['id'];
			new Products($product_ids,$id,$search);
		}
		return self::$instances[$id];
	}
	function getByCategory($id,$search=''){
		if(!is_numeric($id)) return false;
		$id=md5($id.'|'.$search);
		if(!array_key_exists($id,self::$instances)){
			$product_ids=array();
			$rs=dbAll('select id from products,products_categories_products where id=product_id and enabled and category_id='.$id);
			foreach($rs as $r)$product_ids[]=$r['id'];
			new Products($product_ids,$id,$search);
		}
		return self::$instances[$id];
	}
	function getByType($id,$search=''){
		if(!is_numeric($id)) return false;
		$id=md5($id.'|'.$search);
		if(!array_key_exists($id,self::$instances)){
			$product_ids=array();
			$rs=dbAll('select id from products where enabled and product_type_id='.$id);
			foreach($rs as $r)$product_ids[]=$r['id'];
			new Products($product_ids,$id,$search);
		}
		return self::$instances[$id];
	}
	function render($PAGEDATA,$start=0,$limit=0,$order_by='',$order_dir=0){
		$c='';
		// { sort based on $order_by
		if($order_by!=''){
			$tmpprods1=array();
			$prods=$this->product_ids;
			foreach($prods as $key=>$pid){
				$prod=$product=Product::getInstance($pid);
				if($product->get($order_by)){
					if(!isset($tmpprods1[$product->get($order_by)]))$tmpprods1[$product->get($order_by)]=array();
					$tmpprods1[$product->get($order_by)][]=$pid;
					unset($prods[$key]);
				}
			}
			if($order_dir)krsort($tmpprods1);
			else ksort($tmpprods1);
			$tmpprods=array();
			foreach($tmpprods1 as $pids)foreach($pids as $pid)$tmpprods[]=$pid;
			foreach($prods as $key=>$pid){
				$tmpprods[]=$pid;
			}
		}
		else $tmpprods=&$this->product_ids;
		// }
		// { sanitise the limits
		$cnt=count($tmpprods);
		if(!$limit){
			$limit=$cnt;
			$start=0;
		}
		else{
			if($start && $start>=count($this->product_ids))$start=$cnt-$limit-1;
		}
		// }
		// { build array of items
		$prevnext='';
		if($cnt==$limit){
			$prods=&$tmpprods;
		}
		else{
			$prods=array();
			for($i=$start;$i<$limit+$start;++$i)if(isset($tmpprods[$i]))$prods[]=$tmpprods[$i];
			if($start)$prevnext.='<a class="products-prev" href="'.$PAGEDATA->getRelativeUrl().'?start='.($start-$limit).'">&lt;-- prev</a>';
			if($limit && $start+$limit<$cnt){
				if($start)$prevnext.=' | ';
				$prevnext.='<a class="products-next" href="'.$PAGEDATA->getRelativeUrl().'?start='.($start+$limit).'">next --&gt;</a>';
			}
		}
		// }
		// { see if there are search results
		if(isset($PAGEDATA->vars['products_add_a_search_box']) && $PAGEDATA->vars['products_add_a_search_box']){
			if(!count($prods)){
				return '<div class="error">No products found matching that search. Try using less specific terms.</div>';
			}
			else $c.='<div class="products-num-results"><strong>'.count($prods).'</strong> results found.</div>';
		}
		// }
		foreach($prods as $pid){
			$product=Product::getInstance($pid);
			if($product){
				$type=ProductType::getInstance($product->get('product_type_id'));
				if(!$type)$c.='Missing product type: '.$product->get('product_type_id');
				else $c.=$type->render($product,'multiview');
			}
		}
		return $prevnext.$c.$prevnext;
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
				case 'checkbox': // {
					$smarty->assign($f->n,$val?'Yes':'No');
					break;
				// }
				case 'date': // {
					$smarty->assign($f->n,date_m2h($val));
					break;
				// }
				default: // { everything else
					$smarty->assign($f->n,$val);
				// }
			}
		}
		return $smarty->fetch(USERBASE.'/ww.cache/products/templates/types_'.$template.'_'.$this->id);
	}
}
