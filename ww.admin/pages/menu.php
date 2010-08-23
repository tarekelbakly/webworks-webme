<script src="/j/jstree-1.0rc2/jquery.jstree.js"></script>
<script src="/j/jstree-1.0rc2/_lib/jquery.cookie.js"></script>
<script src="/ww.admin/pages/menu.js"></script>
<?php
echo '<div id="pages-wrapper">';
$rs=dbAll('select id,special&2 as disabled,type,name,parent from pages order by ord,name');
$pages=array();
foreach($rs as $r){
	if(!isset($pages[$r['parent']]))$pages[$r['parent']]=array();
	$pages[$r['parent']][]=$r;
}
function show_pages($id){
	global $pages;
	if(!isset($pages[$id]))return;
	echo '<ul>';
	foreach($pages[$id] as $page){
		echo '<li id="page_'.$page['id'].'"><a';
		if($page['disabled']=='1')echo ' class="disabled"';
		echo '>'.htmlspecialchars($page['name']).'</a>';
		show_pages($page['id']);
		echo '</li>';
	}
	echo '</ul>';
}
show_pages(0);
if(count($pages))echo '<script>selected_page='.$pages[0][0]['id'].';</script></div>';
