<?php
class Pages{
    static $instancesByParent = array();
    public $pages=array();
    function __construct($v,$byField=0){
        # byField: 0=Parent; 1=Name
        global $isadmin;
        $filter=$isadmin?'':' && !(special&2)';
        if (!$byField && is_numeric($v)) $rs=dbAll("select id,name,special,type from pages where parent=$v$filter order by ord,name");
				else $rs=array();
				if(!count($rs))$rs=array();
				foreach($rs as $r)$this->pages[] = Page::getInstance($r['id'],$r);
        Pages::$instancesByParent[$v] =& $this;
    }
    function getInstancesByParent($pid=0){
        if (!is_numeric($pid)) return false;
        if (!@array_key_exists($pid,$instancesByParent)) $instancesByParent[$pid]=new Pages($pid);
        return $instancesByParent[$pid];
    }
}
