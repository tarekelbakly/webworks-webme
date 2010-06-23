<?php
if(!is_admin())exit;
// { set up initial variables
if(isset($_REQUEST['id']) && is_numeric($_REQUEST['id']))$id=(int)$_REQUEST['id'];
else $id=0;
$tabs=array(
	array('show_product_variants','Product Variants'),
	array('show_related_products','Related Products'),
	array('show_contained_products','Contained Products'),
	array('show_countries','Countries')
);
// }

if(isset($_REQUEST['action']) && $_REQUEST['action']='save'){
	$errors=array();
	if(!isset($_REQUEST['name']) || $_REQUEST['name']=='')$errors[]='You must fill in the <strong>Name</strong>.';
	if(count($errors)){
		echo '<em>'.join('<br />',$errors).'</em>';
	}
	else{
		$data_fields=str_replace(array("\n","\r"),array('\n',''),$_REQUEST['data_fields']);
		$sql='set name="'.addslashes($_REQUEST['name']).'",data_fields="'.addslashes($data_fields).'",multiview_template="'.addslashes($_REQUEST['multiview_template']).'",singleview_template="'.addslashes($_REQUEST['singleview_template']).'"';
		foreach($tabs as $tab){
			$sql.=','.$tab[0].'='.(isset($_REQUEST[$tab[0]])?1:0);
		}
		if($id){
			dbQuery("update products_types $sql where id=$id");
		}
		else{
			dbQuery("insert into products_types $sql");
			$id=dbOne('select last_insert_id() as id','id');
		}
		echo '<em>Product Type saved</em>';
		cache_clear('products/templates');
	}
}

if($id){
	$tdata=dbRow("select * from products_types where id=$id");
	if(!$tdata)die('<em>No product type with that ID exists.</em>');
}
else{
	$tdata=array(
		'id'=>0,
		'name'=>'',
		'show_product_variants'=>0,
		'show_related_products'=>0,
		'show_contained_products'=>0,
		'show_countries'=>0,
		'data_fields'=>''
	);
}
echo '<form action="'.$_url.'&amp;id='.$id.'&amp;action=save" method="post">';
echo '<div id="tabs"><ul><li><a href="#main-details">Main Details</a></li><li><a href="#data-fields">Data Fields</a></li><li><a href="#multiview-template">Multi-View Template</a></li><li><a href="#singleview-template">Single-View Template</a></li></ul>';
// { main details
echo '<div id="main-details"><table>';
echo '<tr><th>Name</th><td><input class="not-empty" name="name" value="'.htmlspecialchars($tdata['name']).'" /></td></tr>';
echo '<tr><th>Management tabs to use</th><td><p>When creating products with this type, what extra tabs do you want visible?</p>';
foreach($tabs as $tab){
	echo '<input type="checkbox" name="'.$tab[0].'"';
	if($tdata[$tab[0]])echo ' checked="checked"';
	echo ' /> '.$tab[1].'<br />';
}
echo '</td></tr>';
echo '</table></div>';
// }
// { data fields
echo '<div id="data-fields"><p>Create the data fields of your product type here. Examples: colour, size, weight, description.</p>';
echo '<textarea name="data_fields" id="data_fields">'.htmlspecialchars(str_replace(array("\n","\r"),array('\n','\r'),$tdata['data_fields'])).'</textarea>';
echo '</div>';
// }
// { multi-view template
echo '<div id="multiview-template"><p>This template is for how the product looks when it is in a list of products. Leave this blank to have one auto-generated when needed.</p>';
echo ckeditor('multiview_template',$tdata['multiview_template']);
echo '</div>';
// }
// { single-view template
echo '<div id="singleview-template"><p>This template is for how the product looks when shown on its own. Leave this blank to have one auto-generated when needed.</p>';
echo ckeditor('singleview_template',$tdata['singleview_template']);
echo '</div>';
// }
echo '</div><input type="submit" value="Save" /></form><script src="/ww.plugins/products/admin/types-edit.js"></script>';
