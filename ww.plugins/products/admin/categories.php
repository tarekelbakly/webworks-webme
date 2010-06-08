<script src="/j/jquery.jstree/jquery.tree.js"></script>
<script src="/j/jquery.inlinemultiselect.js"></script>
<script src="/ww.plugins/products/admin/get-product-names-js.php"></script>
<script src="/ww.plugins/products/admin/categories.js"></script>
<table>
	<tr><th style="width:300px">Categories</th><th style="width:300px">Attributes</th></tr>
	<tr><td>
<div id="categories-wrapper">
<?php
$rs=dbAll('select * from products_categories');
$cats=array();
foreach($rs as $r){
	if(!isset($cats[$r['parent_id']]))$cats[$r['parent_id']]=array();
	$cats[$r['parent_id']][]=$r;
}
function show_cats($id){
	global $cats;
	if(!isset($cats[$id]))return;
	echo '<ul>';
	foreach($cats[$id] as $cat){
		echo '<li id="cat_'.$cat['id'].'"><a href="#"';
		if($cat['enabled']=='0')echo ' class="disabled"';
		echo '><ins>&nbsp;</ins>'.htmlspecialchars($cat['name']).'</a>';
		show_cats($cat['id']);
		echo '</li>';
	}
	echo '</ul>';
}
show_cats(0);
echo '<script>selected_cat='.$cats[0][0]['id'].';</script>';
?>
</div>
</td>
<td id="products-categories-attrs"></td></tr>
</table>
