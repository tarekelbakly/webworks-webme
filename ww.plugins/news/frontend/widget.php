<?php

$html='';
if(!isset($vars->id)){
	$html='<em>No news page selected.</em>';
	return;
}
if(!$vars->stories_to_show)$vars->stories_to_show=10;
$rs=dbAll('select id from pages where parent='.$vars->id.' order by cdate desc limit '.$vars->stories_to_show);
if(!count($rs)){
	$html='<em>No news items to display.</em>';
	return;
}
$links=array();
foreach($rs as $r){
	$page=Page::getInstance($r['id']);
	$body='';
	if($vars->characters_shown){
		$body=preg_replace('#<h1[^<]*</h1>#','',$page->body);
		$body=preg_replace('/<[^>]*>/','',$body);
		$body='<br /><i>'.substr($body,0,$vars->characters_shown).'...</i>';
	}
	$links[]='<a href="'.$page->getRelativeURL().'"><strong>'.htmlspecialchars($page->name).'</strong>'.$body.'</a>';
}
$style='';
if(isset($vars->scrolling) && $vars->scrolling){
	if(isset($vars->scrolling) && $vars->scrolling)$html.='<script src="/ww.plugins/news/j/jquery.vticker.js"></script><script>$(document).ready(function(){
	$(".news_excerpts_wrapper").vTicker({
		speed: 4000,
		pause: 5000,
		showItems: 1,
		animation: "",
		mousePause: true
	});
});</script>';
}
$html.='<div class="news_excerpts_wrapper"><ul class="news_excerpts"><li>'.join('</li><li>',$links).'</li></ul></div>';
