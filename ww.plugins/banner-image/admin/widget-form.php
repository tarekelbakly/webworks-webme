<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');
if(isset($_REQUEST['get_banner'])){
	require '../frontend/banner-image.php';
	$o=new stdClass();
	$o->id=(int)$_REQUEST['get_banner'];
	echo show_banner($o);
	exit;
}

if(isset($_REQUEST['id']))$id=(int)$_REQUEST['id'];
else $id=0;
echo '<strong>Banner to show</strong><br />';
echo '<select name="id"><option value="0">any</option>';
$banners=dbAll('select id from banners_images order by id');
foreach($banners as $b){
	echo '<option value="'.$b['id'].'"';
	if($id==$b['id'])echo ' selected="selected"';
	echo '>banner'.$b['id'].'</option>';
}
echo '</select>';
?>
(<a href="javascript:;" onclick="banner_image_preview(this)">view</a>)
<script>
function banner_image_preview(el){
	var form=$(el).closest('form');
	var b=$('select[name="id"]',form).val();
	if(b==0)return alert('no banner selected');
	var d=$('<div></div>');
	d.load('/ww.plugins/banner-image/admin/widget-form.php',{'get_banner':b},function(){
		d.dialog({modal:true});
	});
}
</script>
