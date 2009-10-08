<?php
/*
	Webme News Plugin v0.1
	File: frontend/display.php
	Developers:
		Conor Mac Aoidh  http://macaoidh.name/
		Kae Verens       http://verens.com/
	Report Bugs:
		Kae Verens       kae@verens.com
		Conor Mac Aoidh  conor@macaoidh.name
*/

$items_per_page=5;
$p=isset($_REQUEST['news_page'])?(int)$_REQUEST['news_page']:0;
if($p<0) $p=0;

$num_stories=dbOne('select count(id) as num from pages where parent='.$GLOBALS['id'],'num');
$rs=dbAll('select * from pages where parent='.$GLOBALS['id']." order by cdate desc limit $p,$items_per_page");

$nextprev=array();
$nextprev[]='<span class="page_n_of_n">page '.(1+floor($p/$items_per_page)).' of '.(ceil($num_stories/$items_per_page)).'</span>';
if($p)$nextprev[]='<a class="prev" href="?news_page='.($p-$items_per_page).'">Previous Page</a>';
if($p+$items_per_page < $num_stories)$nextprev[]='<a class="next" href="?news_page='.($p+$items_per_page).'">Next Page</a>';
$nextprev='<div class="nextprev">'.join(' | ', $nextprev).'</div>';

$html=$nextprev;

$links=array();
foreach($rs as $r){
	$page=Page::getInstance($r['id'],$r);
	$links[]='<h2><a href="'.$page->getRelativeURL().'">'.htmlspecialchars($page->name).'</a></h2>'
		.'<p>'.substr(preg_replace('/<[^>]*>/','',preg_replace('#<h1>[^<]*</h1>#','',$page->body)),0,600).'...</p>'
		.'<a href="'.$page->getRelativeURL().'">posted on '.date_m2h($page->cdate).'</a>';
}
$html.='<ul class="news_page_contents"><li>'.join('</li><li>',$links).'</ul>';

$html.=$nextprev;
