<?php
function menu_containsPage($needle,$haystack){
	$r=Page::getInstance($needle);
	if(!isset($r->parent) || $r->parent==0)return 0;
	if($r->parent==$haystack)return 1;
	return menu_containsPage($r->parent,$haystack);
}
function menu_getChildren($parentid,$currentpage=0,$isadmin=0,$topParent=0,$search_options=0){
	$md5=md5($parentid.'|'.$currentpage.'|'.$isadmin.'|'.$topParent.'|'.$search_options);
	$cache=cache_load('menus',$md5);
	if($cache)return $cache;
	$pageParentFound=0;
	$PARENTDATA=Page::getInstance($parentid);
	$filter=$isadmin?'':'&& !(special&2)';
	$rs=dbAll("select id as subid,id,name,type,(select count(id) from pages where parent=subid $filter) as numchildren from pages where parent='".$parentid."' $filter order by ord,name");
	$menuitems=array();
	// { optimise db retrieval of pages
	$ids=array();
	foreach($rs as $r)if(!isset(Page::$instances[$r['id']]))$ids[]=$r['id'];
	Pages::precache($ids);
	// }
	$i=0;
	foreach($rs as $k=>$r){
		$PAGEDATA=Page::getInstance($r['id']);
		if(isset($PAGEDATA->banned) && $PAGEDATA->banned)continue;
		$c=array();
		$c[]=($parentid==$topParent)?'menuItemTop':'menuItem';
		if(!$i++)$c[]='first';
		if($r['numchildren'])$c[]='ajaxmenu_hasChildren';
		if(($search_options&1) && $PAGEDATA->type==8)$c[]='ajaxmenu_hasChildren';
		if($r['id']==$currentpage){
			$c[]='ajaxmenu_currentPage';
			$pageParentFound=1;
		}
		else if($r['numchildren']&&!$pageParentFound&&menu_containsPage($currentpage,$r['id'])){ # does this page contain the current page
			$c[]='ajaxmenu_containsCurrentPage';
			$pageParentFound=1;
		}
		$rs[$k]['classes']=join(' ',$c);
		$rs[$k]['link']=$PAGEDATA->getRelativeURL();
		$rs[$k]['name']=$PAGEDATA->name;
		$rs[$k]['parent']=$parentid;
		$menuitems[]=$rs[$k];
	}
	if(isset($PARENTDATA->type) && $PARENTDATA->type==8 && ($search_options&1)){
		$PARENTDATA->initValues();
		if(isset($PARENTDATA->property_type) && $PARENTDATA->property_type)$filter="where enabled and product_type_id='".$PARENTDATA->property_type."'";
		else $filter="where enabled";
		$rs2=dbAll("select id,name from products $filter order by name");
		$rs2=Products::getByFilter($filter.' order by name');
		foreach($rs2 as $r2){
			$rs[]=array('link'=>$PARENTDATA->getRelativeURL().'&product_id='.$r2->id,'name'=>$r2->name,'parent'=>$parentid);
		}
	}
	cache_save('menus',$md5,$menuitems);
	return $menuitems;
}
function menu_setup_main_menu($template){
	$a=array("\n","\r");
	$b=array('WWNLRT','WWNLRT');
	$template=str_replace($a,$b,$template);
	do{
		$change=0;
		if(ereg('%MENU{[^}]*}%',$template)){
			$tmp=preg_replace('/.*%MENU{(.*?)}%.*/','\1',$template);
			$old='%MENU{'.$tmp.'}%';
			$new=str_replace($a,$b,menuDisplay($tmp));
			$template=str_replace($old,$new,$template);
			$change++;
		}
	}while($change);
	$template=str_replace('WWNLRT',"\n",$template);
	return $template;
}
function ww_menuDisplay($b){
	global $PAGEDATA;
	if(!$PAGEDATA->id)return '';
	$md5=md5('ww_menudisplay|'.print_r($b,true));
	$cache=cache_load('menus',$md5);
	if($cache)return $cache;
	if(is_array($b)){
		$align=(isset($b['direction']) && $b['direction']=='vertical')?'Left':'Top';
		$vals=$b;
	}
	else{
		$arr=explode('|',$b);
		$b=$arr[0];
		$vals=array();
		if(count($arr)>1)$d=split(',',$arr[1]);
		else $d=array();
		foreach($d as $e){
			$f=split('=',$e);
			if(count($f)>1)$vals[$f[0]]=$f[1];
			else $vals[$f[0]]=1;
		}
		$c='';
		$align=($b=='vertical')?'Left':'Top';
	}
	$parent=0;
	$classes='';
	if(isset($vals['mode'])){
		if($vals['mode']=='accordian' || $vals['mode']=='accordion'){
			$classes.=' click_required accordion';
		}
		else if($vals['mode']=='two-tier'){
			$classes.=' two-tier';
		}
	}
	else $vals['mode']='default';
	if(isset($vals['preopen_menu']))$classes.=' preopen_menu';
	if(isset($vals['close']) && $vals['close']=='no'){
		$classes.=' noclose';
	}
	if(isset($vals['parent'])){
		$r=Page::getInstanceByName($vals['parent']);
		if($r)$parent=$r->id;
	}
	$search_options=0;
	if(isset($vals['products'])){
		$classes.=' products';
		$search_options+=1;
	}
	$ajaxmenu=@$vals['nodropdowns']?'':' ajaxmenu ';
	$c='<div id="ajaxmenu'.$parent.'" class="menuBar'.$align.$ajaxmenu.$classes.' parent'.$parent.'">';
	$rs=menu_getChildren($parent,$PAGEDATA->id,0,$parent,$search_options);
	$links=0;
	if(count($rs))foreach($rs as $r){
		$page=Page::getInstance($r['id']);
		if(!$links)$r['classes'].=' first';
		$c.='<a id="ajaxmenu_link'.$r['id'].'" class="'.$r['classes'].'" href="'.$page->getRelativeURL().'"><span class="l"></span>'.htmlspecialchars($page->name).'<span class="r"></span></a>';
		$links++;
	}
	$c.='<a class="menuItemTop nojs" href="'.$PAGEDATA->getRelativeURL().'&amp;webmespecial=sitemap">'.__('Site Map').'</a>';
	$c.='</div>';
	if($vals['mode']=='two-tier'){
		$pid=$PAGEDATA->getTopParentId();
		if($pid!=2 && $pid!=3 && $pid!=17 && $pid!=32 && $pid!=33 && $pid!=34)$pid=2;
		$rs=menu_getChildren($pid,$PAGEDATA->id,0,$parent,$search_options);
		$c.='<div id="ajaxmenu'.$pid.'" class="menu tier-two">';
		if(count($rs))foreach($rs as $r){
			$page=Page::getInstance($r['id']);
			$c.='<a id="ajaxmenu_link'.$r['id'].'" class="'.$r['classes'].'" href="'.$page->getRelativeURL().'"><span class="l"></span>'.htmlspecialchars($page->name).'<span class="r"></span></a>';
		}
		else $c.='<a><span class="l"></span>&nbsp;<span class="r"></span></a>';
		$c.='</div>';
	}
	$c.='<script type="text/javascript">plugins_to_load.ajaxmenu=1;</script>';
	cache_save('menus',$md5,$c);
	return $c;
}
