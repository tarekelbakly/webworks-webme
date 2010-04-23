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
	$PARENTDATA->initValues();
	$filter=$isadmin?'':'&& !(special&2)';
	// menu order
	$order='ord,name';
	if(isset($PARENTDATA->vars['order_of_sub_pages'])){
		switch($PARENTDATA->vars['order_of_sub_pages']){
			case 1: // { alphabetical
				$order='name';
				if($PARENTDATA->vars['order_of_sub_pages_dir'])$order.=' desc';
				break;
			// }
			case 2: // { associated_date
				$order='associated_date';
				if($PARENTDATA->vars['order_of_sub_pages_dir'])$order.=' desc';
				$order.=',name';
				break;
			// }
			default: // { by admin order
				$order='ord';
				if($PARENTDATA->vars['order_of_sub_pages_dir'])$order.=' desc';
				$order.=',name';
			// }
		}
	}
	// }
	$rs=dbAll("select id as subid,id,name,type,(select count(id) from pages where parent=subid $filter) as numchildren from pages where parent='".$parentid."' $filter order by $order");
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
	cache_save('menus',$md5,$menuitems);
	return $menuitems;
}
function menu_show($b){
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
function menu_build_fg($parentid,$depth,$options){
	$PARENTDATA=Page::getInstance($parentid);
	$PARENTDATA->initValues();
	// menu order
	$order='ord,name';
	if(isset($PARENTDATA->vars['order_of_sub_pages'])){
		switch($PARENTDATA->vars['order_of_sub_pages']){
			case 1: // { alphabetical
				$order='name';
				if($PARENTDATA->vars['order_of_sub_pages_dir'])$order.=' desc';
				break;
			// }
			case 2: // { associated_date
				$order='associated_date';
				if($PARENTDATA->vars['order_of_sub_pages_dir'])$order.=' desc';
				$order.=',name';
				break;
			// }
			default: // { by admin order
				$order='ord';
				if($PARENTDATA->vars['order_of_sub_pages_dir'])$order.=' desc';
				$order.=',name';
			// }
		}
	}
	// }
	$rs=dbAll("select id as subid,id,name,type from pages where parent='".$parentid."' order by $order");
	if($rs===false || !count($rs))return '';

	$items=array();
	foreach($rs as $r){
		$item='<li>';
		$page=Page::getInstance($r['id']);
		$item.='<a href="'.$page->getRelativeUrl().'">'.htmlspecialchars($page->name).'</a>';
		$item.=menu_build_fg($r['id'],$depth+1,$options);
		$item.='</li>';
		$items[]=$item;
	}
	$options['columns']=(int)$options['columns'];

	// return top-level menu
	if(!$depth)return '<ul>'.join('',$items).'</ul>';

	$s='';
	if($options['background'])$s.='background:'.$options['background'].';';
	if($options['opacity'])$s.='opacity:'.$options['opacity'].';';
	if($s){
		$s=' style="'.$s.'"';
	}

	// return 1-column sub-menu
	if($options['columns']<2)return '<ul'.$s.'>'.join('',$items).'</ul>';

	// return multi-column submenu
	$items_count=count($items);
	$items_per_column=ceil($items_count/$options['columns']);
	$c='<table'.$s.'><tr><td><ul>';
	for($i=1;$i<$items_count+1;++$i){
		$c.=$items[$i-1];
		if($i!=$items_count && !($i%$items_per_column))$c.='</ul></td><td><ul>';
	}
	$c.='</ul></td></tr></table>';
	return $c;
}
function menu_show_fg($opts){
	$md5=md5('menu_fg|'.print_r($opts,true));
	$cache=cache_load('menus',$md5);
	if($cache)return $cache;

	$options=array(
		'direction' => 0,  // 0: horizontal, 1: vertical
		'parent'    => 0,  // top-level
		'background'=> '', // sub-menu background colour
		'columns'   => 1,  // for wide drop-down sub-menus
		'opacity'   => 0   // opacity of the sub-menu
	);
	foreach($opts as $k=>$v){
		if(isset($options[$k]))$options[$k]=$v;
	}
	if(!is_numeric($options['parent'])){
		$r=Page::getInstanceByName($options['parent']);
		if($r)$options['parent']=$r->id;
	}
	if(is_numeric($options['direction'])){
		if($options['direction']=='0')$options['direction']='horizontal';
		else $options['direction']='vertical';
	}
	$c.='<script src="/j/fg.menu/fg.menu.js"></script>';
	$c.='<link rel="stylesheet" type="text/css" href="/j/fg.menu/fg.menu.css" />';
	$items=array();
	$menuid=$GLOBALS['fg_menus']++;
	$c.='<div class="menu-fg menu-fg-'.$options['direction'].'" id="menu-fg-'.$menuid.'">'.menu_build_fg($options['parent'],0,$options).'</div>';
	if($options['direction']=='vertical'){
		$posopts="positionOpts: { posX: 'left', posY: 'top',
			offsetX: 40, offsetY: 10, directionH: 'right', directionV: 'down',
			detectH: true, detectV: true, linkToFront: false },";
	}
	else{
		$posopts='';
	}
	$c.="<script>
jQuery.fn.outer = function() {
  return $( $('<div></div>').html(this.clone()) ).html();

}
$(function(){
	$('#menu-fg-$menuid>ul>li>a').each(function(){
		if(!$(this).next().length)return; // empty
		$(this).menu({
			content:$(this).next().outer(),
			choose:function(ev,ui){
				document.location=ui.item[0].childNodes(0).href;
			},
			$posopts
			flyOut:true
		});
	});
});
</script>";
	return $c;
	cache_save('menus',$md5,$c);
	return $c;
}
$fg_menus=0;
