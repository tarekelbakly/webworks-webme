<?php
include('../common.php');
header('Content-type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
echo '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">';
$rs=dbAll("select cdate,importance,name from pages where importance>0 order by importance desc");
foreach($rs as $r){
	echo '<url><loc>http://'.$sitedomain.'/'.htmlspecialchars(str_replace(' ','-',$r['name'])).'</loc>'
		.'<lastmod>'.preg_replace('/ .*/','',$r['cdate']).'</lastmod>'
		.'<priority>'.$r['importance'].'</priority>'
		.'</url>';
}
echo '</urlset>';
