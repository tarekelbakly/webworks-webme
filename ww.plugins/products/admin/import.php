<?php
/**
  * The import script
  *
  * PHP Version 5
  *
  * Displays a form to upload a file, checks its type and puts its contents
  * into the products database
  *
  * @category   ProductsPlugin
  * @package    WebWorksWebme
  * @subpackage ProductsPlugin
  * @author     Belinda Hamilton <bhamilton@webworks.ie>
  * @license    GPL Version 2
  * @link       www.webworks.ie
 */
echo '<script src="/ww.plugins/products/admin/products.js"></script>';
if (isset($_POST['import'])) {
	if (isset($_FILES['file'])) {
		$file = $_FILES['file'];
		if ($file['type']=='text/csv') { // If it has the right extension
			if (isset($_POST['clear_database'])) {
				dbQuery('delete from products');
				dbQuery('delete from products_categories_products');
				if (isset($_POST['remove_associated-files'])) {
					$base = $_SERVER['DOCUMENT_ROOT'];
					require_once $base.'/j/kfm/api/api.php';
					require_once $base.'/j/kfm/classes/kfmDirectory.php';
					$base_dir_name = USERBASE.'f/products/products-images';
					$base_dir_id = kfm_api_getDirectoryId($base_dir_name);
					$base_dir = kfmDirectory::getInstance($base_dir_id);
					$sub_dirs = $base_dir->getSubDirs();
					foreach ($sub_dirs as $sub) {
						$sub->delete();
					}
				}	
			}
			if (isset($_POST['clear_categories_database'])) {
				dbQuery('delete from products_categories');
				dbQuery('delete from products_categories_products');
			}
			$newName = 'webworks_webme_products_import'.time().rand().'.csv';
			$location = USERBASE.'ww.cache/products/imports';
			if (!is_dir($location)) {
				mkdir($location);
			}
			move_uploaded_file(
				$file['tmp_name'], 
				$location.'/'.$newName
			);
			$file = fopen($location.'/'.$newName, 'r');
			$tmp = fgetcsv($file);
			// { The headings are the first line.
			foreach ($tmp as $col) {
				// { Assume that leading underscores in the name should be removed
				$col = preg_replace('/^_/', '', $col);
				$colNames[] = $col;
				// }
				${$col}= array();
			}
			// }
			$row = fgetcsv($file);
			// { Build the arrays of data
			while ($row) {
				$i = 0;
				foreach ($colNames as $col) {
					for ($i; $i<count($row); $i++) {
						$data = $row[$i];
						break;
					}
					if (is_array(${$col})) {
						${$col}[] = $data;
					}
					$i++;
				}
				$row = fgetcsv($file);
			}
			// }
			// { How many rows of data?
			foreach ($colNames as $col) {
				$numRows = count(${$col});
				break;
			}
			// }
			$ids = array();
			$allIds = dbAll('select id from products');
			foreach ($allIds as $num) {
				$ids[] = $num['id'];
			}
			// { Put the data into the products database
			for ($i=0; $i<$numRows; $i++) {
				if (is_array($id)) {
					if (in_array($id[$i], $ids, false)&&is_numeric($id[$i])) {
						dbQuery(
							'update products 
							set 
								name = 
									\''.addslashes($name[$i]).'\',
								product_type_id = 
									'.(int)$product_type_id[$i].',
								image_default = 
									\''.addslashes($image_default[$i]).'\',
								enabled = 
									'.(int)$enabled[$i].', 
								date_created = 
									\''.addslashes($date_created[$i]).'\',
								data_fields = 
									\''.addslashes($data_fields[$i]).'\',
								images_directory = 
									\''.addslashes($images_directory[$i]).'\'
							where id = '.(int)$id[$i]
						);
					}
					elseif (is_numeric($id[$i])) {
						dbQuery(
							'insert into products 
							values
							(
								\''.(int)$id[$i].'\',
								\''.addslashes($name[$i]).'\',
								\''.(int)$product_type_id[$i].'\',
								\''.(int)$enabled[$i].'\',
								\''.addslashes($image_default[$i]).'\',
								\''.addslashes($date_created[$i]).'\',
								\''.addslashes($data_fields[$i]).'\',
								\''.addslashes($images_directory[$i]).'\'
							)'
						);
					}
					elseif ($id[$i]==null) {
						dbQuery(
							'insert into products	
							(
								name,
								product_type_id,
								image_default,
								enabled,
								date_created,
								data_fields,
								images_directory
							)
							values
							(
								'.addslashes($name[$i]).', 
								\''.(int)$product_type_id[$i].'\',
								\''.addslashes($image_default[$i]).'\',
								\''.(int)$enabled[$i].'\',
								\''.addslashes($date_created[$i]).'\',
								\''.addslashes($data_fields[$i]).'\',
								\''.addslashes($images_directory[$i]).'\'
							)'
						);
					}
				}
				else {
					dbQuery(
						'insert into products	
						set 
						name = 
							\''.addslashes($name[$i]).'\',
						product_type_id = 
							'.(int)$product_type_id[$i].',
						image_default = 
							\''.addslashes($image_default[$i]).'\',
						enabled = 
							'.(int)$enabled[$i].', 
						date_created = 
							\''.addslashes($date_created[$i]).'\',
						data_fields = 
							\''.addslashes($data_fields[$i]).'\',
						images_directory = 
							\''.addslashes($images_directory[$i]).'\''
					);
				}
			}
			// }
			if (($_POST['cat_options'])!='') {
				products_import_insert_into_cats($categories, $id);				
			}
			if (isset($_POST['prune_cats'])) {
				$allCats = dbAll('select id from products_categories');
				foreach ($allCats as $cat) {
					products_import_prune_cats($cat['id']);
				}
			}
			fclose($file);
			unlink($location.'/'.$newName);
			$_FILES['file'] = '';
			echo '<em>Products Imported</em>';
		}
		elseif (!empty($_POST['file'])) {
			echo '<em>Only csv files are permitted</em>';
		}
	}
}
// { The Form
echo '<form method="post" enctype="multipart/form-data">';
echo 'Delete products before import? ';
echo '<input type="checkbox" id="clear_database" name="clear_database"
	onchange="toggle_remove_associated_files();" />';
echo '<div id="new_line"></div>'; // a <br /> will keep an extra line
$cats = dbAll('select name, id from products_categories');
$jsonCats = json_encode($cats);
echo 'Delete categories before import? ';
echo '<input type="checkbox" name="clear_categories_database" 
	id="clear_categories_database" 
		onchange=\'show_hide_cat_options('.$jsonCats.');\' />';
echo '<br />';
echo 'Delete empty categories on import? ';
echo '<input type="checkbox" name="prune_cats" id = "prune-cats" />';
echo '<br />';
echo 'Import into categories ';
echo '<select id="cat_options" name="cat_options">';
echo '<option value="">--none--</option>';
echo '<option value="0">In File</option>';
foreach ($cats as $cat) {
	echo '<option value="'.$cat['id'].'">'.$cat['name'].'</option>';
}
echo '</select><br />';
echo 'Select import file ';
echo '<input type="file" name="file" />';
echo '<br />';
echo '<input type="submit" name="import" value="Import Data" />';
echo '</form>';
// }
function products_import_insert_into_cats ($categories, $id) {
	switch ($_POST['cat_options']) {
		case '0': // { The categories are in the file
			$i = 0;
			foreach ($categories as $cats) { // Create cats
				$cats = explode(',', $cats);
				foreach ($cats as $catList) {
					if (!empty($catList)) {
						$catList = explode('>', $catList);
						$parent = 0;
						foreach ($catList as $cat) {
							$catID
								= dbOne(
									'select id 
									from products_categories
									where name=\''.$cat.'\' 
									and parent_id='.$parent,
									'id'
								);
							if (!$catID) {
								dbQuery(
									'insert into products_categories
									(name, parent_id)
									values(
										\''.addslashes($cat).'\', 
										'.(int)$parent
									.')'
								);
								$parent 
									= dbOne(
										'select last_insert_id()',
										'last_insert_id()'
									);
									$catID = $parent;
							}
							else {
								$parent = $catID;
							}
						}
						if (is_numeric($id[$i])) {
							dbQuery(
								'insert into 
								products_categories_products
								values(
									'.(int)$id[$i].'
									,'.(int)$catID
								.')'
							);

						}
					}
				}
				$i++;
			}
		break; // }
		default: // { The category exists
			if (is_numeric($_POST['cat_options'])) {
				for ($i=0; $i<$numRows; $i++) {
					if (is_numeric($id[$i])) {
						dbQuery(
							'insert into 
							products_categories_products
							values(
								'.(int)$id[$i].'
								,'.(int)$_POST['cat_options']
							.')'
						);
					}
				}
			}
		break; // }
	}
}
function products_import_prune_cats ($catID) {
	$prod_id
		= dbOne(
			'select product_id 
			from products_categories_products
			where category_id = '.$catID
			.' limit 1',
			'product_id'
		);
	if ($prod_id) {
		return;
	}
	// { Check the children
	$children 
		= dbAll(
			'select id
			from products_categories 
			where parent_id = '.$catID
		);
	if (count($children)) {
	foreach ($children as $child) {
		products_import_prune_cats($child['id']);
	}
	// }
	$children
		= dbAll(
			'select id 
			from products_categories
			where parent_id = '.$catID
		);
	}
	if (!count($children)) {
		dbQuery('delete from products_categories where id = '.$catID);
	}
}
