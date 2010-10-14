<?php
function displayPageSummaries($id){
	$PAGEDATA=Page::getInstance($id);
	global $sitedomain;
	$r=dbRow('select * from page_summaries where page_id="'.$PAGEDATA->id.'"');
	if(!count($r))return '<em>This page is marked as a page summary, but there is no information on how to handle it.</em>';
	if($r['rss'])return rss_to_html($r['rss']);
	// { build rss
		$title=($PAGEDATA->title=='')?$sitedomain:htmlspecialchars($PAGEDATA->title);
		$rss='<'.'?xml version="1.0" ?'.'><rss version="2.0"><channel><title>'.$title.'</title>';
		$rss.='<link>'.$_SERVER['REQUEST_URI'].'</link><description>RSS for '.$PAGEDATA->name.'</description>';
		$category=$PAGEDATA->category?' and category="'.$PAGEDATA->category.'"':'';
		$containedpages=get_contained_pageids($r['parent_id']);
		if(count($containedpages)){
			$q2=dbAll('select edate,name,title,body from pages where id in ('.join(',',$containedpages).')'.$category.' order by cdate desc limit 20');
			foreach($q2 as $r2){
				$rss.='<item>';
				if(!$r2['title'])$r2['title']=$r2['name'];
				$rss.='<title>'.htmlspecialchars($r2['title']).'</title>';
				$rss.='<pubDate>'.date_m2h($r2['edate']).'</pubDate>';
				{ # build body
					if($r['amount_to_show']==0 || $r['amount_to_show']==1){
						$length=$r['amount_to_show']==0?300:600;
						$body=substr( preg_replace('/<[^>]*>/','',str_replace(array('&amp;','&nbsp;','&lsquo;'),array('&',' ','&apos;'),$r2['body'])),0,$length).'...';
					}
					else $body=$r2['body'];
					$body=str_replace('&euro;','&#8364;',$body); # xml parsers can't handle this
				}
				$rss.='<description>'.$body.'</description>';
				$rss.='<link>http://'.$_SERVER['HTTP_HOST'].'/'.urlencode(str_replace(' ','-',$r2['name'])).'</link>';
				$rss.='</item>';
			}
		}
		$rss.='</channel></rss>';
		dbQuery('update page_summaries set rss="'.addslashes($rss).'" where page_id="'.$PAGEDATA->id.'"');
	// }
	return rss_to_html($rss);
}
function get_contained_pageids($id,$containedpages=array()){
	$q=dbAll('select id,type,special,category from pages where parent="'.$id.'" and !(special&4)');
	foreach($q as $r){
		switch($r['type']){
			case 0: {
				$containedpages[]=$r['id'];
				break;
			}
		}
		$containedpages=get_contained_pageids($r['id'],$containedpages);
	}
	return $containedpages;
}
function rss_to_html($rss){
	$rss=str_replace('<'.'?xml version="1.0" ?'.'><rss version="2.0">','',$rss);
	$rss=preg_replace('/<channel.*?\/description>/','',$rss);
	$rss=preg_replace('/<pubDate>.*?<\/pubDate>/','',$rss);
	$rss=str_replace(array('<title>','</title>','&#8364;'),array('<h3>','</h3>','&euro;'),$rss);
	$rss=str_replace('<description>','<p>',$rss);
	$rss=str_replace('</description>','</p>',$rss);
	$rss=str_replace('<item>','<div class="page_summary_item">',$rss);
	$rss=str_replace('</item>','</div>',$rss);
	$rss=str_replace('<link>','<a href="',$rss);
	$rss=str_replace('</link>','">[more...]</a>',$rss);
	$rss=str_replace(array('</rss>','</channel>'),array('',''),$rss);
	return $rss==''?'<em>No articles contained here</em>':$rss;
}
