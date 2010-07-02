<?php
include('../ww.incs/common.php');
header('Content-type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
echo '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">';
$rs=dbAll("select id,cdate,importance,name from pages where importance>0 order by importance desc");
foreach($rs as $r){
	$page=Page::getInstance($r['id']);
	echo '<url><loc>http://'.$_SERVER['HTTP_HOST'].$page->getRelativeUrl().'</loc>'
		.'<lastmod>'.preg_replace('/ .*/','',$r['cdate']).'</lastmod>'
		.'<priority>'.$r['importance'].'</priority>'
		.'</url>';
}
echo '</urlset>';
