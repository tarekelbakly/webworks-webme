<?php
class Page{
	static $instances			  = array();
	static $instancesByName		= array();
	static $instancesByNAndP	   = array();
	static $instancesByProductType = array();
	static $instancesBySpecial	 = array();
	static $instancesByType		= array();
	public $vals;
	function __construct($v,$byField=0,$fromRow=0,$pvq=0){
		# byField: 0=ID; 1=Name
		if (!$byField && is_numeric($v)) $r=$fromRow?$fromRow:($v?dbRow("select * from pages where id=$v limit 1"):array());
		else if ($byField == 1){ // by name
			$name=strtolower(str_replace('-','_',$v));
			$fname='page_by_name_'.md5($name);
			$r=cache_load('pages',$fname);
			if($r===false){
				$r=dbRow("select * from pages where name like '".addslashes($name)."' limit 1");
				if(count($r))cache_save('pages',$fname,$r);
			}
		}
		else if ($byField == 2 && is_numeric($v)){ // by type
			$fname='page_by_type_'.$v;
			$r=cache_load('pages',$fname);
			if(!$r){
				$r=dbRow("select * from pages where type=$v limit 1");
				cache_save('pages',$fname,$r);
			}
		}
		else if ($byField == 3 && is_numeric($v)){ // by special
			$fname='page_by_special_'.$v;
			$r=cache_load('pages',$fname);
			if(!$r){
				$r=dbRow("select * from pages where special&$v limit 1");
				cache_save('pages',$fname,$r);
			}
		}
		else if ($byField == 4) $r=$v;
		else return false;
		if(!count($r))return false;
		foreach ($r as $k=>$v) $this->{$k}=$v;
		$this->urlname=$r['name'];
		if(!isset($_SESSION['viewing_language']))$_SESSION['viewing_language']='en';
		if(isset($_SESSION['translation']) && $_SESSION['viewing_language']!='en'){
			$rs=dbAll("SELECT * FROM translations WHERE object_type='page' AND object_id=".$this->id." AND lang='".$_SESSION['viewing_language']."'");
			foreach ($rs as $r) $this->{$r['name']}=$r['value'];
		}
		$this->dbVals=$r;
		self::$instances[$this->id] =& $this;
		self::$instancesByName[preg_replace('/[^a-z0-9]/','-',strtolower($this->urlname))] =& $this;
		self::$instancesBySpecial[$this->special] =& $this;
		self::$instancesByType[$this->type] =& $this;
		// { set up values if supplied. otherwise, delay it 'til required
		$this->__valuesLoaded=false;
		if($pvq)$this->initValues($pvq);
		// }
	}
	function getInstance($id=0,$fromRow=false,$pvq=false){
		if (!is_numeric($id)) return false;
		if (!@array_key_exists($id,self::$instances)) self::$instances[$id]=new Page($id,0,$fromRow,$pvq);
		return self::$instances[$id];
	}
	function getInstanceByName($name=''){
		$name=strtolower($name);
		$nameIndex=preg_replace('#[^a-z0-9/]#','-',$name);
		if(@array_key_exists($nameIndex,self::$instancesByName))return self::$instancesByName[$nameIndex];
		if(strpos($name,'/')){
			$names=explode('/',$nameIndex);
			$pid=0;
			foreach($names as $n){
				$p=self::getInstanceByNameAndParent($n,$pid);
				if(!$p)return false;
				$pid=$p->id;
			}
			self::$instancesByName[$nameIndex]=$p;
		}
		else self::$instancesByName[$nameIndex]=new Page($name,1);
		return self::$instancesByName[$nameIndex];
	}
	function getInstanceByProductType($id=0){
		if(!is_numeric($id))return false;
		if(!@array_key_exists($id,$instancesByProductType)){
			$page_id=dbOne('select page_id from page_vars where name="product_type" and value="'.$id.'"','page_id',0);
			if(!$page_id){
				dbQuery('insert into pages (ord,name,type,parent,cdate,edate,body,special) values(1000,"_product_page",8,0,now(),now(),"",2)');
				$page_id=dbOne('select last_insert_id() as id','id');
				dbQuery('insert into page_vars values('.$page_id.',"product_type",'.$id.')');
			}
			$instancesByProductType[$id]=& self::getInstance($page_id);
		}
		return $instancesByProductType[$id];
	}
	function getInstanceBySpecial($sp=0){
		if (!is_numeric($sp)) return false;
		if (!@array_key_exists($sp,$instancesBySpecial)) $instancesBySpecial[$sp]=new Page($sp,3);
		return $instancesBySpecial[$sp];
	}
	function getInstanceByType($type=0){
		if (!@array_key_exists($type,self::$instancesByType)) new Page($type,2);
		return self::$instancesByType[$type];
	}
	function getInstanceByNameAndParent($name,$parent){
		$name=str_replace('-','_',$name);
	  if(!@array_key_exists($name.'/'.$parent,self::$instancesByNAndP)){
			$r=dbRow("SELECT * FROM pages WHERE parent=$parent AND name LIKE '".addslashes($name)."'");
			if(!count($r))return false;
			self::$instancesByNAndP[$name.'/'.$parent] = new Page($r,4);
		}
		return self::$instancesByNAndP[$name.'/'.$parent];
	}
	function getRelativeURL(){
		if(isset($this->relativeURL))return $this->relativeURL;
				$this->relativeURL='';
				if($this->parent){
					$p=Page::getInstance($this->parent);
					if($p)$this->relativeURL.=$p->getRelativeURL();
				}
		$this->relativeURL.='/'.$this->getURLSafeName();
		return $this->relativeURL;
		}
	function getTopParentId(){
		if(!$this->parent)return $this->id;
		$p=Page::getInstance($this->parent);
		return $p->getTopParentId();
	}
	function getURLSafeName(){
		if(isset($this->getURLSafeName))return $this->getURLSafeName;
		$r=$this->urlname;
		$r=preg_replace('/[^a-zA-Z0-9,-]/','-',$r);
		$this->getURLSafeName=$r;
		return $r;
	}
	function initValues($pvq=false){
		$this->vars=array();
		if(!$pvq){
			$fname='page_vars_'.$this->id;
			$pvq=cache_load('pages',$fname);
			if($pvq===false){
				$pvq=dbAll("select * from page_vars where page_id=".$this->id);
				cache_save('pages',$fname,$pvq);
			}
		}
		foreach($pvq as $pvr)$this->vars[$pvr['name']]=$pvr['value'];
		if(isset($_SESSION['os_country']) && $_SESSION['os_country'] && isset($this->vars['banned_countries']) && $this->vars['banned_countries'] && strpos($this->vars['banned_countries'],$_SESSION['os_country'])!==false)$this->banned=true;
		return $this;
	}
}