<?php
if(!is_admin())exit;
echo '<script src="/ww.plugins/products/admin/products.js"></script>';
if(isset($_REQUEST['delete']) && is_numeric($_REQUEST['delete'])){
	if (isset($_REQUEST['delete-images'])&&($_REQUEST['delete-images']==1)) {
		$imagesDir
			= dbOne(
				'select images_directory
				from products
				where id='.$_REQUEST['delete'],
				'images_directory'
			);
		$id = kfm_api_getDirectoryId($imagesDir);
		$dir = kfmDirectory::getInstance($id);
		$dir->delete();
	}
	dbQuery('delete from products where id='.$_REQUEST['delete']);
	echo '<em>Product deleted.</em>';
}
$rs=dbAll('select id,name from products order by name');
if(!dbOne('select id from products_types limit 1','id')){
	echo '<em>You can\'t create a product until you have created a type. <a href="plugin.php?_plugin=products&amp;_page=types-edit">Click here to create one</a></em>';
}
else if(!count($rs)){
	echo '<em>No existing products. <a href="plugin.php?_plugin=products&amp;_page=products-edit">Click here to create one</a>.</em>';
}
else{
	echo '<a href="plugin.php?_plugin=products&amp;_page=products-edit">Add a Product</a><br /><br />';
	echo '<div style="width:50%"><table class="datatable"><thead><tr><th>Name</th><th>&nbsp;</th><th>Remove Associated Images</th></tr></thead><tbody>';
	foreach($rs as $r){
		echo '<tr><td class="edit-link"><a href="plugin.php?_plugin=products&amp;_page=products-edit&amp;id='.$r['id'].'">'.htmlspecialchars($r['name']).'</td><td>';
		echo '<a class="delete_link" id="delete_link_'.$r['id'].'" href="'.$_url.'&delete='.$r['id'].'&delete-images=1" onclick="return confirm(\'are you sure you want to delete this product?\\n'.htmlspecialchars($r['name']).'\')" title="delete">[x]</a>';
		echo '&nbsp;';
		echo '</td>';
		echo '<td><input type="checkbox" 
			id="delete_link_'.$r['id'].'"
			onChange="change_href('.$r['id'].');"
			class="delete_checkbox"
			checked="checked" />';
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody></table></div>';
}
