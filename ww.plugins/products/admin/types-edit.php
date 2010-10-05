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
		$singleview = $_REQUEST['singleview_template'];
		if (strlen($singleview)<20) {
			$singleview = '{{PRODUCTS_DATATABLE}}'.$singleview;
		}
		$multiview = $_REQUEST['multiview_template'];
		if (strlen($multiview)<20) {
			$multiview = '{{PRODUCTS_DATATABLE align=horizontal}}';
			$multiview.= '<a href="{{PRODUCTS_LINK}}">more</a>';
		}
		$sql='set name="'.addslashes($_REQUEST['name']).'",data_fields="'.addslashes($data_fields).'",multiview_template="'.addslashes($multiview).'",singleview_template="'.addslashes($singleview).'"';
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
		if(isset($_FILES['image_not_found'])){
			@mkdir(USERBASE.'f/products/types/'.$id);
			$imgs=new DirectoryIterator(USERBASE.'f/products/types/'.$id);
			foreach ($imgs as $img) {
				if ($img->isDot()) {
					continue;
				}
				unlink($img->getPathname());
			}
			$from=$_FILES['image_not_found']['tmp_name'];
			$to=USERBASE.'f/products/types/'.$id.'/image-not-found.png';
			echo "convert \"$from\" \"$to\"";
			`convert "$from" "$to"`;
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
echo '<form action="'.$_url.'&amp;id='.$id.'" method="POST" enctype="multipart/form-data"><input type="hidden" name="action" value="save" />';
echo '<div id="tabs"><ul><li><a href="#main-details">Main Details</a></li><li><a href="#data-fields">Data Fields</a></li><li><a href="#multiview-template">Multi-View Template</a></li><li><a href="#singleview-template">Single-View Template</a></li></ul>';
// { main details
echo '<div id="main-details"><table>';
// { name
echo '<tr><th>Name</th><td><input class="not-empty" name="name" value="'.htmlspecialchars($tdata['name']).'" /></td>';
echo '<th>&nbsp;</th><td>&nbsp;</td>';
echo '</tr>';
// }
// { management tabs, image not found
echo '<tr>';
// { management tabs
echo '<th>Management tabs to use</th><td>';
foreach($tabs as $tab){
	echo '<input type="checkbox" name="'.$tab[0].'"';
	if($tdata[$tab[0]])echo ' checked="checked"';
	echo ' /> '.$tab[1].'<br />';
}
echo '</td>';
// }
// { image not found
echo '<th>image-not-found</th><td><input type="file" name="image_not_found" />';
if($id){
	if(!file_exists(USERBASE.'f/products/types/'.$id.'/image-not-found.png')){
		@mkdir(USERBASE.'f/products/types/'.$id,0777,true);
		copy(dirname(__FILE__).'/../i/not-found-250.png',USERBASE.'f/products/types/'.$id.'/image-not-found.png');
	}
	echo '<img src="/kfmgetfull/products/types/'.$id.'/image-not-found.png,width=64,height=64" />';
}
echo '</td>';
// }
echo '</tr>';
// }
echo '</table></div>';
// }
// { data fields
echo '<div id="data-fields"><p>Create the data fields of your product type here. Examples: colour, size, weight, description.</p>';
echo '<textarea name="data_fields" id="data_fields">'.htmlspecialchars(str_replace(array("\n","\r"),array('\n','\r'),$tdata['data_fields'])).'</textarea>';
echo '</div>';
// }
// { multi-view template
echo '<div id="multiview-template"><p>This template is for how the product looks when it is in a list of products. Leave this blank to have one auto-generated when needed.</p>'
	.ckeditor('multiview_template',$tdata['multiview_template'])
	.'<p class="sample-codes">Example codes: {{PRODUCTS_IMAGE}}, {{PRODUCTS_LINK}}, {{$_name}}</p>'
	.'</div>';
// }
// { single-view template
echo '<div id="singleview-template"><p>This template is for how the product looks when shown on its own. Leave this blank to have one auto-generated when needed.</p>';
echo ckeditor('singleview_template',$tdata['singleview_template']);
echo '</div>';
// }
echo '</div><input type="submit" value="Save" /></form><script src="/ww.plugins/products/admin/types-edit.js"></script>';
