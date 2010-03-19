<?php
include('../ww.incs/common.php');
header('Content-type: text/xml; charset=utf-8');
$pagename=str_replace('-','_',preg_replace('#^/|.rss$#','',urldecode($_SERVER['REQUEST_URI'])));
$r=dbRow("select id from pages where name like '".$pagename."'");
if(count($r)){
	$r2=dbRow("select rss from page_summaries where page_id='".$r['id']."'");
	if(count($r2)){
		if($r2['rss']==''){
			include_once(SCRIPTBASE.'common/page.summaries.php');
			displayBlogExcerpts($r['id']);
			$r2=dbRow("select rss from page_summaries where page_id='".$r['id']."'");
		}
		$rss=str_replace('&rsquo;','&apos;',$r2['rss']);
		$rss=str_replace('&sbquo;','&apos;',$rss);
		echo $rss;
	}
}else echo 'page "'.$pagename.'" not found';
