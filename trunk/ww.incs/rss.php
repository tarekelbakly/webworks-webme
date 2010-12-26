<?php
include('../ww.incs/common.php');
header('Content-type: text/xml; charset=utf-8');
$pagename=str_replace('-','_',preg_replace('#^/|.rss$#','',urldecode($_SERVER['REQUEST_URI'])));
$page=Page::getInstanceByName($pagename);
if($page){
	$r2=dbRow("select rss from page_summaries where page_id='".$page->id."'");
	if(count($r2)){
		if($r2['rss']==''){
			include_once(SCRIPTBASE.'common/page.summaries.php');
			displayBlogExcerpts($page->id);
			$r2=dbRow("select rss from page_summaries where page_id='".$page->id."'");
		}
		$rss=str_replace('&rsquo;','&apos;',$r2['rss']);
		$rss=str_replace('&sbquo;','&apos;',$rss);
		echo $rss;
	}
}else echo 'page "'.$pagename.'" not found';
