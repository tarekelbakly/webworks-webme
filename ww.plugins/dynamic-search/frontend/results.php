<?php
/*
	Webme Dynamic Search Plugin v0.2
	File: frontend/results.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/

function getDescendants($id){
        $s=' or parent='.$id;
        $q=dbAll('select id from pages where parent="'.$id.'"');
        $n=count($q);
        if($n==0) return $s;
	foreach($q as $r){
                $s.=getDescendants($r['id']);
        }
        return $s;
}

function catags($ss,$s,$cat,$limit){
        if($ss=='') return 'Fatal Error.';
        $catags=explode(',',$ss);
        foreach($catags as $catag){
                if($cat==$catag){
                        $id=dbOne('select id from pages where name="'.$cat.'"','id');
                        $gd=getDescendants($id);
                        $q=dbAll('select * from pages where (id='.$id.' '.$gd.') and (body like "%'.$s.'%" or name like "%'.$s.'%") order by edate limit '.$limit);
                        return $q;
                }
        }
}

$s=@$_GET['dynamic_search'];
$cat=@$_GET['dynamic_category'];
if($cat=='') $cat='Site Wide';

$p=@$_GET['dynamic_page'];
if($p==0) $p=1;
$l=$p*10;
$m=$l-9;
$limit=$m.','.$l;

dbQuery('insert into latest_search values ("","'.$s.'","'.$cat.'","'.$_SERVER['REQUEST_TIME'].'","'.date('dd/mm/yy').'")');

if($cat=='Site Wide') $q=dbAll('select * from pages where name like "%'.$s.'%" or body like "%'.$s.'%" order by edate limit '.$limit);
else $q=catags($SS['cat'],$s,$cat,$limit);

$n=count($q);

$c='<div id="dynamic_searches"><div id="dynamic_search_results">';

if($n==0||!$n) $c.='<i>No search results found for "'.$s.'" in category "'.$cat.'". Please try less keywords.</i>';
else{
	$c.='<ul id="dynamic_list">';
	$num=($p==0)?0:$m-1;
	foreach($q as $r){
		$num++;
		$title=($r['title']=='')?$r['name']:$r['title'];
		$c.='<li><h4>'.$num.'. &nbsp;&nbsp;'.htmlspecialchars($title).'</h4>';
		$c.='<p>'.substr(preg_replace('/<[^>]*>/','',  $r['body']),0,200).'...';
		$c.='<br /><a href="/'.urlencode($r['name']).'">/'.htmlspecialchars($r['name']).'</a></p></li>';
	}
	$c.='</ul>';
	if($n==10) $c.='<p class="right"><a href="?dynamic_search_submit=search&dynamic_search='.$s.'&dynamic_category='.$cat.'&dynamic_page='.($p+1).'">Next Page</a></p>'; 
}

if($p>1) $c.='<p class="left"><a href="?dynamic_search_submit=search&dynamic_search='.$s.'&dynamic_category='.$cat.'&dynamic_page='.($p-1).'">Previous Page</a></p>';

$html.=$c.'</div></div>';

?>
