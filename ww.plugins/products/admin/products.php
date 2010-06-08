<?php
if(!is_admin())exit;
if(isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])){
	dbQuery('delete from products where id='.$_REQUEST['delete']);
	echo '<em>Product deleted.</em>';
}
$rs=dbAll('select id,name from products order by name');
if(!count($rs)){
	echo '<em>No existing products. <a href="plugin.php?_plugin=products&amp;_page=products-edit">Click here to create one</a>.</em>';
}
else{
	echo '<a href="plugin.php?_plugin=products&amp;_page=products-edit">Add a Product</a><br /><br />';
	echo '<div style="width:50%"><table class="datatable"><thead><tr><th>Name</th><th>&nbsp;</th></tr></thead><tbody>';
	foreach($rs as $r){
		echo '<tr><td class="edit-link"><a href="plugin.php?_plugin=products&amp;_page=products-edit&amp;id='.$r['id'].'">'.htmlspecialchars($r['name']).'</td><td>';
		echo '<a href="'.$_url.'&delete='.$r['id'].'" onclick="return confirm(\'are you sure you want to delete this product?\\n'.htmlspecialchars($r['name']).'\')" title="delete">[x]</a>';
		echo '&nbsp;';
		echo '</td></tr>';
	}
	echo '</tbody></table></div>';
}
