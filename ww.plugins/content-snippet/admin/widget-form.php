<?php
// var_dump($_REQUEST);
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');
if(isset($_REQUEST['get_content_snippet'])){
	require '../frontend/index.php';
	$o=new stdClass();
	$o->id=(int)$_REQUEST['get_content_snippet'];
	$ret=array('content'=>show_content_snippet($o));
	echo json_encode($ret);
	exit;
}
if(isset($_REQUEST['action']) && $_REQUEST['action']=='save'){
	$id=(int)$_REQUEST['id'];
	$id_was=$id;
	$html=addslashes($_REQUEST['html']);
	$sql="content_snippets set html='$html'";
	if($id){
		$sql="update $sql where id=$id";
		dbQuery($sql);
	}
	else{
		$sql="insert into $sql";
		dbQuery($sql);
		$id=dbOne('select last_insert_id() as id','id');
	}
	$ret=array('id'=>$id,'id_was'=>$id_was);
	echo json_encode($ret);
	exit;
}

if(isset($_REQUEST['id']))$id=(int)$_REQUEST['id'];
else $id=0;
echo '<a href="javascript:;" id="content_snippet_editlink_'.$id.'" class="content_snippet_editlink">view or edit snippet</a>';
if($id){
	echo '<div id="content_snippet_preview_'.$id.'"></div>';
}
?>
<script>
if(!ww.content_snippet)ww.content_snippet={
	editor_instances:0
};
function content_snippet_edit(ev){
	var el=ev.target;
	var id=el.id.replace(/content_snippet_editlink_/,'');
	ww.content_snippet.editor_instances++;
	var d=$('<div><textarea style="width:600px;height:300px;" id="content_snippet_html'+ww.content_snippet.editor_instances+'" name="content_snippet_html'+ww.content_snippet.editor_instances+'"></textarea></div>');
	$.getJSON('/ww.plugins/content-snippet/admin/widget-form.php',{'get_content_snippet':id},function(res){
		d.dialog({
			minWidth:630,
			minHeight:400,
			height:400,
			width:630,
			beforeclose:function(){
				if(!ww.content_snippet.rte)return;
				ww.content_snippet.rte.destroy();
				ww.content_snippet.rte=null;
			},
			buttons:{
				'Save':function(){
					var html=ww.content_snippet.rte.getData();
					$.post('/ww.plugins/content-snippet/admin/widget-form.php',{'id':id,'action':'save','html':html},function(ret){
						if(ret.id!=ret.was_id){
							el.id='content_snippet_editlink_'+ret.id;
						}
						id=ret.id;
						var w=$(el).closest('.widget-wrapper');
						var wd=w.data('widget');
						wd.id=id;
						w.data('widget',wd);
						updateWidgets(w.closest('.panel-wrapper'));
						d.dialog('close');
					},'json');
				},
				'Close':function(){
					d.dialog('close');
				}
			}
		});
		ww.content_snippet.rte=CKEDITOR.replace( 'content_snippet_html'+ww.content_snippet.editor_instances,{filebrowserBrowseUrl:"/j/kfm/",menu:"WebME"} );
		ww.content_snippet.rte.setData(res.content);
	});
}
$('.content_snippet_editlink').each(function(){
	if(this.content_click_added)return;
	$(this).click(content_snippet_edit);
	this.content_click_added=true;
})
<?php
if($id){
	echo '$("#content_snippet_preview_'.$id.'").load("/ww.plugins/content-snippet/admin/get_text_preview.php?id='.$id.'")';
}
?>
</script>
