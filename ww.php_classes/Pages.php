<?php
class Pages{
	static $instancesByParent = array();
	public $pages=array();
	function __construct($v,$byField=0){
		# byField: 0=Parent; 1=Name
		global $isadmin;
		$filter=$isadmin?'':' && !(special&2)';
		if (!$byField && is_numeric($v)) $rs=dbAll("select * from pages where parent=$v$filter order by ord,name");
		else $rs=array();
		if(!count($rs))$rs=array();
		foreach($rs as $r)$this->pages[] = Page::getInstance($r['id'],$r);
		Pages::$instancesByParent[$v] =& $this;
	}
	static function getInstancesByParent($pid=0){
		if (!is_numeric($pid)) return false;
		if (!@array_key_exists($pid,$instancesByParent))new Pages($pid);
		return Pages::$instancesByParent[$pid];
	}
	function precache($ids){
		if(count($ids)){
			$rs3=dbAll('select * from pages where id in ('.join(',',$ids).')');
			$pvars=dbAll('select * from page_vars where page_id in ('.join(',',$ids).')');
			$rs2=array();
			foreach($pvars as $p){
				if(!isset($rs2[$p['page_id']]))$rs2[$p['page_id']]=array();
				$rs2[$p['page_id']][]=$p;
			}
			foreach($rs3 as $r){
				if(isset($rs2[$r['id']]))Page::getInstance($r['id'],$r,$rs2[$r['id']]);
				else Page::getInstance($r['id'],$r);
			}
		}
	}
}
