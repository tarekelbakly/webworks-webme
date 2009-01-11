<?php
function ww_showSearchResults(){
	// { variables
		global $PAGEDATA;
		$start=getVar('start',0);
		$search=getVar('search');
		if(!$search)return '<em id="searchResultsTitle">'.__('no search text entered').'</em>';
		$totalfound=0;
		$c='';
	// }
	// { pages
		$q=dbAll('select id,name,body from pages where (name like "%'.$search.'%" or body like "%'.$search.'%")');
		$n=count($q);
		if($n>0){
			$totalfound+=$n;
			$q=dbAll('select id,name,title,body from pages where (name like "%'.$search.'%" or body like "%'.$search.'%") order by edate desc limit '.$start.',20');
			$c.='<h2>'.__('Page Search Results').'</h2><em id="searchResultsTitle">';
			if($n==1)$c.=__('1 result found');
			else $c.=__('%1 results found',$n);
			$c.='</em> <div class="showhide">';
			if($start>0)$c.='[<a href="'.$PAGEDATA->getRelativeURL().'?search='.urlencode($search).'&amp;start='.($start-20).'">previous 20</a>] ';
			if($start+20<$n)$c.='[<a href="'.$PAGEDATA->getRelativeURL().'?search='.urlencode($search).'&amp;start='.($start+20).'">next 20</a>] ';
			$c.='<ol start="'.($start+1).'" id="searchResults">';
			foreach($q as $r){
				$title=($r['title']=='')?$r['name']:$r['title'];
				$c.='<li><h4>'.htmlspecialchars($title).'</h4>';
				$c.='<p>'.substr(preg_replace('/<[^>]*>/','', webmeParse( $r['body'])),0,200).'...';
				$c.='<br /><a href="/'.urlencode($r['name']).'?search='.$search.'">/'.htmlspecialchars($r['name']).'</a></p></li>';
			}
			$c.='</ol></div>';
		}
	// }
	if(!$totalfound){
		$c.='<em id="searchResultsTitle">no results found</em>';
	}
	return $c;
}
