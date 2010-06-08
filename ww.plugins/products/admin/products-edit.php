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
		$sql='set name="'.addslashes($_REQUEST['name']).'",product_type_id='.((int)$_REQUEST['product_type_id']);
		foreach($tabs as $tab){
			$sql.=','.$tab[0].'='.(isset($_REQUEST[$tab[0]])?1:0);
		}
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
		echo '<em>Product saved</em>';
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
		'default_image'=>'',
		'enabled'=>1,
		'date_created'=>date('Y-m-d'),
		'data_fields'=>'{}'
	);
}
echo '<form action="'.$_url.'&amp;id='.$id.'&amp;action=save" method="post">';
echo '<div id="tabs"><ul><li><a href="#main-details">Main Details</a></li><li><a href="#data-fields">Data Fields</a></li></ul>';
// { main details
echo '<div id="main-details"><table>';
echo '<tr><th>Name</th><td><input class="not-empty" name="name" value="'.htmlspecialchars($pdata['name']).'" /></td></tr>';
echo '<tr><th>Type</th><td>';
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
echo '</td></tr>';
echo '</table></div>';
// }
// { data fields
echo '<div id="data-fields"><table>';
$dfs=json_decode($pdata['data_fields'],true);
$dfjson=dbOne('select data_fields from products_types where id='.$pdata['product_type_id'],'data_fields');
if($dfjson=='')$dfjson='[]';
$dfjson=json_decode($dfjson,true);
$dfdefs=array();
foreach($dfjson as $d)$dfdefs[$d['n']]=$d;
function product_dfs_show($df,$def){
	echo '<tr><th>'.htmlspecialchars($def['n']).'</th><td>';
	switch($def['t']){
		case 'textarea': // {
			echo ckeditor('data_fields['.htmlspecialchars($def['n']).']',$df['v']);
			break;
		// }
		default: // { inputbox
			echo '<input name="data_fields['.htmlspecialchars($def['n']).']" value="'.htmlspecialchars($df['v']).'" />';
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
echo '</div><script>$(function(){$("#tabs").tabs();});</script><input type="submit" value="Save" /></form>';
