<script src="/j/jquery.jstree/jquery.tree.js"></script>
<script src="/j/jquery.jstree/plugins/jquery.tree.contextmenu.js"></script>
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
		echo '<li id="page_'.$page['id'].'"><a href="pages.php?id='.$page['id'].'"';
		if($page['disabled']=='1')echo ' class="disabled"';
		echo '><ins>&nbsp;</ins>'.htmlspecialchars($page['name']).'</a>';
		show_pages($page['id']);
		echo '</li>';
	}
	echo '</ul>';
}
show_pages(0);
echo '<script>selected_page='.$pages[0][0]['id'].';</script></div>';
