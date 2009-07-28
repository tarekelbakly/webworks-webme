<?php
/*
        Webme News Plugin v0.1
        File: frontend/display.php
        Developer: Conor Mac Aoidh <http://macaoidh.name>
        Report Bugs: <conor@macaoidh.name>
*/

$p=@$_GET['news_page'];
if($p==0) $p=1;
$l=$p*5;
$m=$l-5;
$limit=$m.','.$l;

$q=dbAll('select name,body,cdate from pages where parent='.$GLOBALS['id'].' order by cdate desc limit '.$limit);
$n=count($q);

$html='';

if($n==5) $html.='<p style="float:right;margin-top:-20px"><a href="?news_page='.($p+1).'">Next Page</a></p>';
if($p>1) $html.='<p style="margin-top:20px"><a href="?news_page='.($p-1).'">Previous Page</a></p>';

$html.='<ul style="list-style-type:none;margin:40px 10px">';
for($i=0;$i<=($n-1);$i++){
	$url='/'.str_replace(' ','-',$q[$i]['name']);
        $html.='<li style="margin:20px 0"><h2><a href="'.$url.'">'.$q[$i]['name'].'</a></h2><p>'.substr(preg_replace('/<[^>]*>/','',$q[$i]['body']),0,600).'...
		<p><a href="'.$url.'">Posted on '.substr($q[$i]['cdate'],0,-9).'</a></p></li>';
}
$html.='</ul>';
if($n==5) $html.='<p style="float:right;margin-bottom:20px"><a href="?news_page='.($p+1).'">Next Page</a></p>';
if($p>1) $html.='<p style="float:left;margin-bottom:20px"><a href="?news_page='.($p-1).'">Previous Page</a></p>';
