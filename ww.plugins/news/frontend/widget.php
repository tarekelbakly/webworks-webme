<?php

$html='';
if(!isset($vars->id)){
	$html='<em>No news page selected.</em>';
	return;
}
$rs=dbAll('select id from pages where parent='.$vars->id.' order by cdate desc limit 10');
if(!count($rs)){
	$html='<em>No news items to display.</em>';
	return;
}
$html.='<ul class="news_excerpts">';
foreach($rs as $r){
	$page=Page::getInstance($r['id']);
	$html.='<li><a href="'.$page->getRelativeURL().'">'.htmlspecialchars($page->name).'</a></li>';
}
$html.='</ul>';
