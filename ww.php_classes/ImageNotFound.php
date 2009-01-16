<?php
class ImageNotFound{
	static $instances = array();
	function __construct($size){
		$size=(int)$size;
		if(!$size)return;
		if($size<65)$img=64;
		else if($size<101)$img=100;
		else if($size<151)$img=150;
		else if($size<201)$img=201;
		else $img=250;
		$this->relativeURL='/i/not_found/'.$img.'.gif';
		$this->size=$size;
		self::$instances[$this->size] =& $this;
	}
	function getInstance($size){
		if (!is_numeric($size)) return false;
		if (!@array_key_exists($size,self::$instances)) new ImageNotFound($size);
		return self::$instances[$size];
	}
	function getRelativeURL(){
		if(isset($this->relativeURL))return $this->relativeURL;
	}
}
