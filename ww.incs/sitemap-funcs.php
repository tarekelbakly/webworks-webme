<?php
require_once SCRIPTBASE . 'ww.incs/menus.php';
function ww_showSitemap($c=''){
	global $PAGEDATA;
	$rs=menu_getChildren(0,$PAGEDATA->id);
	$c='<ul>'.ww_showSitemapLinks($rs).'</ul>';
	return $c;
}
function ww_showSitemapLinks($rs){
	global $PAGEDATA;
	$c='';
	foreach($rs as $r){
		$d=(ereg('hasChildren',$r['classes']))?'<ul>'.ww_showSitemapLinks(menu_getChildren($r['id'],$PAGEDATA->id)).'</ul>':'';
		$c.='<li><a href="'.$r['link'].'" class="'.$r['classes'].'">'.htmlspecialchars($r['name']).'</a>'.$d.'</li>';
	}
	return $c;
}
