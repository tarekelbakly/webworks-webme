<?php
$html='';
if(!isset($vars->id)){
	$html='<em>No news page selected.</em>';
	return;
}
if(!$vars->stories_to_show)$vars->stories_to_show=10;
$rs=dbAll('select id from pages where parent='.$vars->id.' order by associated_date desc,cdate desc limit 20');
if(!count($rs)){
	$html='<em>No news items to display.</em>';
	return;
}
$links=array();
foreach($rs as $r){
	$page=Page::getInstance($r['id']);
	$body='';
	if($vars->characters_shown){
		$body=preg_replace('#<h1[^<]*</h1>#','',$page->render());
		$body=preg_replace('/<[^>]*>/','',$body);
		$body='<br /><i>'.substr($body,0,$vars->characters_shown).'...</i>';
	}
	$links[]='<a href="'.$page->getRelativeURL().'"><strong>'.htmlspecialchars($page->name).'</strong><div class="date">'.date_m2h($page->associated_date).'</div>'.$body.'</a>';
}
if(isset($vars->scrolling) && $vars->scrolling){
	$n_items=isset($vars->stories_to_show) && is_numeric($vars->stories_to_show)?$vars->stories_to_show:2;
	if(isset($vars->scrolling) && $vars->scrolling){
		WW_addScript('/j/jquery.vticker.js');
		WW_addCSS('/ww.plugins/news/c/scroller.css');
		$html.='<script>$(function(){
			$(".news_excerpts_wrapper").vTicker({
				speed: 4000,
				pause: 5000,
				showItems: '.$n_items.',
				animation: "",
				mousePause: true
			});
		});</script>';
	}
}
$html.='<div class="news_excerpts_wrapper"><ul class="news_excerpts"><li>'.join('</li><li>',$links).'</li></ul></div>';
