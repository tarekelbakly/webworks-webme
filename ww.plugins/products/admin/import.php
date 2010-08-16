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
// { The Form
echo '<form method="post" enctype="multipart/form-data">';
echo '<input type="file" name="file" />';
echo '<input type="submit" name="import" value="Import Data" />';
echo '</form>';
// }
if (isset($_POST['import'])) {
	if (isset($_FILES['file'])) {
		$file = $_FILES['file'];
		if ($file['type']=='text/csv') { // If it has the right extension
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
					for($i; $i<count($row); $i++) {
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
		}
		else {
			echo 'Only csv files are permitted';
		}
	}
}
