<?php
if(!is_admin())exit;
// { set up initial variables
if(isset($_REQUEST['id']) && is_numeric($_REQUEST['id']))$id=(int)$_REQUEST['id'];
else $id=0;
// }

if(isset($_REQUEST['action']) && $_REQUEST['action']='save'){
	$errors=array();
	if(!isset($_REQUEST['name']) || $_REQUEST['name']=='')$errors[]='You must fill in the <strong>Name</strong>.';
	if(count($errors)){
		echo '<em>'.join('<br />',$errors).'</em>';
	}
	else{
		// { save main data and data fields
		$sql='set name="'.addslashes($_REQUEST['name']).'"'
			.',product_type_id='.((int)$_REQUEST['product_type_id'])
			.',enabled='.(int)$_REQUEST['enabled']
			.',images_directory="'.addslashes($_REQUEST['images_directory']).'"';
		$dfs=array();
		if(!isset($_REQUEST['data_fields']))$_REQUEST['data_fields']=array();
		foreach($_REQUEST['data_fields'] as $n=>$v){
			$dfs[]=array(
				'n'=>$n,
				'v'=>$v
			);
		}
		$sql.=',data_fields="'.addslashes(json_encode($dfs)).'"';
		if($id){
			dbQuery("update products $sql where id=$id");
		}
		else{
			dbQuery("insert into products $sql,date_created=now()");
			$id=dbOne('select last_insert_id() as id','id');
		}
		// }
		// { save categories
		dbQuery('delete from products_categories_products where product_id='.$id);
		foreach($_REQUEST['product_categories'] as $key=>$val)dbQUery('insert into products_categories_products set product_id='.$id.',category_id='.$key);
		// }
		echo '<em>Product saved</em>';
		if(isset($_REQUEST['frontend-admin'])){
			echo '<script type="text/javascript">parent.location=parent.location;</script>';
		}
	}
}

if($id){
	$pdata=dbRow("select * from products where id=$id");
	if(!$pdata)die('<em>No product with that ID exists.</em>');
}
else{
	$pdata=array(
		'id'=>0,
		'name'=>'',
		'data_fields'=>'',
		'product_type_id'=>0,
		'image_default'=>0,
		'enabled'=>1,
		'date_created'=>date('Y-m-d'),
		'data_fields'=>'{}',
		'images_directory'=>''
	);
}
echo '<form id="products-form" action="'.$_url.'&amp;id='.$id.'" method="post"><input type="hidden" name="action" value="save" />';
echo '<div id="tabs"><ul><li><a href="#main-details">Main Details</a></li><li><a href="#data-fields">Data Fields</a></li><li><a href="#categories">Categories</a></ul>';
// { main details
echo '<div id="main-details"><table>';
// { name
echo '<tr><th><div class="help products/name"></div>Name</th><td><input class="not-empty" name="name" value="'.htmlspecialchars($pdata['name']).'" /></td>';
// }
// { type
echo '<th><div class="help products/type"></div>Type</th><td>';
$ptypes=dbAll('select id,name from products_types order by name');
if($ptypes===false){
	echo '<em>No product types created yet. Please <a href="plugin.php?_plugin=products&amp;_page=types-edit">create one</a> before you go any further!</em>';
}
else{
	if(!$pdata['product_type_id'])$pdata['product_type_id']=$ptypes[0]['id'];
	echo '<select name="product_type_id">';
	foreach($ptypes as $ptype){
		echo '<option value="'.$ptype['id'].'"';
		if($ptype['id']==$pdata['product_type_id'])echo ' selected="selected"';
		echo '>'.htmlspecialchars($ptype['name']).'</option>';
	}
	echo '</select>';
}
echo '</td>';
// }
// { enabled
echo '<th><div class="help products/enabled"></div>Enabled</th><td><select name="enabled"><option value="1">Yes</option><option value="0"';
if(!$pdata['enabled'])echo ' selected="selected"';
echo '>No</option></select></td></tr>';
// }
// { images
echo '<tr><th><div class="help products/images"></div>Images</th><td colspan="5">';
if(!isset($pdata['images_directory']) || !$pdata['images_directory'] || !is_dir(USERBASE.'f/'.$pdata['images_directory'])){
	if(!is_dir(USERBASE.'f/products/product-images')){
		mkdir(USERBASE.'f/products/product-images',0777,true);
	}
	$pdata['images_directory']='/products/product-images/'.md5(rand().microtime());
	mkdir(USERBASE.'f'.$pdata['images_directory']);
}
$dir_id=kfm_api_getDirectoryId(preg_replace('/^\//','',$pdata['images_directory']));
$images=kfm_loadFiles($dir_id);
$images=$images['files'];
$n=count($images);
echo '<iframe src="/ww.plugins/products/admin/uploader.php?images_directory='.urlencode($pdata['images_directory']).'" style="width:400px;height:50px;border:0;overflow:hidden"></iframe><script>window.kfm={alert:function(){}};window.kfm_vars={};function x_kfm_loadFiles(){}function kfm_dir_openNode(){$("#products-form").submit();}var product_id='.$id.';</script>';
if($n){
	echo '<div id="product-images-wrapper">';
	for($i=0;$i<$n;$i++){
		$default=($images[$i]['id']==$pdata['image_default'])?' class="default"':'';
		echo '<div'.$default.'><img src="/kfmget/'.$images[$i]['id'].',width=64,height=64" title="'.str_replace('\\\\n','<br />',$images[$i]['caption']).'" /><br /><input type="checkbox" id="products-dchk-'.$images[$i]['id'].'" /><a class="delete" href="javascript:;" id="products-dbtn-'.$images[$i]['id'].'">delete</a><br /><a class="mark-as-default" href="javascript:;" id="products-dfbtn-'.$images[$i]['id'].'">set default</a></div>';
	}
	echo '</div>';
}else{
	echo '<em>no images yet. please upload some.</em>';
}
echo '<input type="hidden" name="images_directory" value="'.htmlspecialchars($pdata['images_directory']).'" />';
// }
echo '</table></div>';
// }
// { data fields
echo '<div id="data-fields"><table>';
$dfs=json_decode($pdata['data_fields'],true);
$dfjson=dbOne('select data_fields from products_types where id='.$pdata['product_type_id'],'data_fields');
if($dfjson=='')$dfjson='[]';
$dfjson=str_replace(array("\n","\r"),array('\n',''),$dfjson);
$dfjson=json_decode($dfjson,true);
$dfdefs=array();
foreach($dfjson as $d)$dfdefs[$d['n']]=$d;
function product_dfs_show($df,$def){
	echo '<tr><th>'.htmlspecialchars($def['n']).'</th><td>';
	switch($def['t']){
		case 'checkbox': // {
			echo '<input name="data_fields['.htmlspecialchars($def['n']).']" type="checkbox"';
			if($def['r'])echo ' class="required"';
			if($df['v'])echo ' checked="checked"';
			echo ' />';
			break;
		// }
		case 'date': // {
			echo '<input class="date-human';
			if($def['r'])echo ' required';
			echo '" name="data_fields['.htmlspecialchars($def['n']).']" value="'.htmlspecialchars($df['v']).'" />';
			break;
		// }
		case 'selectbox': // {
			$opts=explode("\n",$def['e']);
			echo '<select name="data_fields['.htmlspecialchars($def['n']).']">';
			foreach($opts as $opt){
				echo '<option';
				if($opt==$df['v'])echo ' selected="selected"';
				echo '>'.htmlspecialchars($opt).'</option>';
			}
			echo '</select>';
			break;
		// }
		case 'textarea': // {
			echo ckeditor('data_fields['.htmlspecialchars($def['n']).']',$df['v']);
			break;
		// }
		default: // { inputbox
			echo '<input name="data_fields['.htmlspecialchars($def['n']).']"';
			if($def['r'])echo ' class="required"';
			echo ' value="'.htmlspecialchars($df['v']).'" />';
		// }
	}
	echo '</td></tr>';
}
foreach($dfs as $df){
	if(isset($dfdefs[$df['n']])){
		$def=$dfdefs[$df['n']];
		unset($dfdefs[$df['n']]);
	}
	else{
		if($df['v']=='')continue;
		$def=array('n'=>$df['n'],'t'=>'inputbox','r'=>0);
	}
	product_dfs_show($df,$def);
}
foreach($dfdefs as $def){
	product_dfs_show(array('v'=>''),$def);
}
echo '</table></div>';
// }
// { categories
echo '<div id="categories">';
// { build array of categories
$rs=dbAll('select id,name,parent_id from products_categories');
$cats=array();
foreach($rs as $r)$cats[$r['id']]=$r;
// }
// { add selected categories to the list
$rs=dbAll('select * from products_categories_products where product_id='.$id);
foreach($rs as $r)$cats[$r['category_id']]['selected']=true;
// }
function show_sub_cats($parent){
	global $cats;
	$found=array();
	foreach($cats as $id=>$cat){
		if(isset($cat['parent_id']) && $cat['parent_id']==$parent && isset($cat['name'])){
			$l='<li><input type="checkbox" name="product_categories['.$id.']"';
			if(isset($cat['selected']))$l.=' checked="checked"';
			$l.='>'.htmlspecialchars($cat['name']);
			$l.=show_sub_cats($id);
			$found[]=$l;
		}
	}
	return '<ul>'.join('',$found).'</ul>';
}
echo show_sub_cats(0);
echo '</div>';
// }
if(isset($_REQUEST['frontend-admin'])){
	echo '<input type="hidden" name="frontend-admin" value="1" />';
}
echo '</div><input type="submit" value="Save" /></form>';
echo '<script src="/ww.plugins/products/admin/products.js"></script>';
