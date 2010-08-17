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
				if (isset($_POST['remove_associated-files'])) {
					$base = $_SERVER['DOCUMENT_ROOT'];
					require_once $base.'/j/kfm/api/api.php';
					require_once $base.'/j/kfm/classes/kfmDirectory.php';
					$base_dir_name = USERBASE.'f/products/products-images';
					$base_dir_id = kfm_api_getDirectoryId($base_dir_name);
					$base_dir = kfmDirectory::getInstance($base_dir_id);
					$sub_dirs = $base_dir->getSubDirs();
					foreach($sub_dirs as $sub) {
						$sub->delete();
					}
				}	
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
			$patterns 
				= array("/^\"_/", "/^_/" ,"/^ \"_/", "/^ _/", '/^ "/', '/"$/');
			// { The headings are the first line.
			foreach ($tmp as $col) {
				// { Assume that leading underscores in the name should be removed
				$col = preg_replace($patterns, '', $col);
				$colNames[] = $col;
				// }
				${$col}= array();
			}
			// }
			$row = fgetcsv($file);
			// { Build the arrays of data
			$rowPatterns = array('/^"/', '/^ "/', '/"$/');
			while ($row) {
				$i = 0;
				$data_fields_contents = '';
				foreach ($colNames as $col) {
					for($i; $i<count($row); $i++) {
						$data = $row[$i];
						$data = preg_replace($rowPatterns, '', $data);
						if ($col=='data_fields') {
							while ($i<10) {
								$data = str_replace('""', '"', $row[$i]);
								if (preg_match('/^v/', $data)) {
									$data_fields_contents.=',"';
								}
								$data_fields_contents.= $data;
								$i++;
								$data = $row[$i];
							}
							$data_fields_contents 
								= str_replace(
									'}{', 
									'},{',
									$data_fields_contents
								);
								$data_fields_contents 
									= preg_replace(
										$rowPatterns, 
										'', 
										$data_fields_contents
									);
						}
						break;
					}
					if (is_array(${$col})) {
						if ($col=='data_fields') {
							${$col}[] = $data_fields_contents;
						}
						else {
							${$col}[] = $data;
						}
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
			$ids = dbAll('select id from products');
			// { Put the data into the products database
			for($i=0; $i<$numRows; $i++) {
				if (is_array($id)) {
					if (in_array($id[$i],$ids)&&is_numeric($id[$i])) {
						dbQuery(
							'update products 
							set 
								name = 
									\''.addslashes($name[$i]).'\',
								product_type_id = '
									.(int)$product_type_id[$i].',
								image_default = 
									\''.addslashes($image_default[$i]).'\',
								enabled = '
									.(int)$enabled[$i].', 
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
							('
								.'\''.(int)$id[$i].'\',
								\''.addslashes($name[$i]).'\','
								.' \''.(int)$product_type_id[$i].'\','
								.' \''.(int)$enabled[$i].'\',
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
								'.addslashes($name[$i]).', '
								.'\''.(int)$product_type_id[$i].'\', '
								.'\''.addslashes($image_default[$i]).'\', '
								.'\''.(int)$enabled[$i].'\', ' 
								.'\''.addslashes($date_created[$i]).'\', '
								.'\''.addslashes($data_fields[$i]).'\', '
								.'\''.addslashes($images_directory[$i]).'\'
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
						product_type_id = '
							.(int)$product_type_id[$i].',
						image_default = 
							\''.addslashes($image_default[$i]).'\',
						enabled = '
							.(int)$enabled[$i].', 
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
			fclose($file);
			unlink($location.'/'.$newName);
			$_FILES['file'] = '';
			echo '<em>Products Imported</em>';
		}
		else {
			echo '<em>Only csv files are permitted</em>';
		}
	}
}
// { The Form
echo '<form method="post" enctype="multipart/form-data">';
echo 'Clear database before import? ';
echo '<input type="checkbox" id="clear_database" name="clear_database"
	onChange="toggle_remove_associated_files();" />';
echo '<div id="new_line"></div>';
echo '<input type="file" name="file" />';
echo '<br />';
echo '<input type="submit" name="import" value="Import Data" />';
echo '</form>';
// }

