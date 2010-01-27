<?php
// var_dump($_REQUEST);
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');

function image_transition_get_subdirs($base,$dir){
	$arr=array();
	$D=new DirectoryIterator($base.$dir);
	$ds=array();
	foreach($D as $dname){
		$d=$dname.'';
		if(substr($d,0,1)=='.')continue;
		if(!is_dir($base.$dir.'/'.$d))continue;
		$ds[]=$d;
	}
	asort($ds);
	foreach($ds as $d){
		$arr[]=$dir.'/'.$d;
		$arr=array_merge($arr,image_transition_get_subdirs($base,$dir.'/'.$d));
	}
	return $arr;
}

if(isset($_REQUEST['get_image_transition'])){
	$r=dbRow('select * from image_transitions where id='.(int)$_REQUEST['get_image_transition']);
	$dirs=image_transition_get_subdirs(USERBASE.'f','');
	if($r===false)$r=array('pause'=>3000);
	echo json_encode(array(
		'data'=>$r,
		'directories'=>$dirs
	));
	exit;
}
if(isset($_REQUEST['action']) && $_REQUEST['action']=='save'){
	$id=(int)$_REQUEST['id'];
	$id_was=$id;
	$directory=addslashes($_REQUEST['directory']);
	$trans_type=addslashes($_REQUEST['trans_type']);
	$pause=(int)$_REQUEST['pause'];
	if(!$pause)$pause=3000;
	$sql="image_transitions set directory='$directory',trans_type='$trans_type',pause=$pause";
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
echo '<a href="javascript:;" id="image_transition_editlink_'.$id.'" class="image_transition_editlink">view or edit snippet</a>';
?>
<script>
if(!ww.image_transition)ww.image_transition={
	selected_dir:'/'
};
function image_transition_file_manager(){
	window.open('/j/kfm/?startup_folder='+$('#image_transition_folder').val(),'kfm','modal,width=800,height=600');
}
function image_transition_edit(ev){
	var el=ev.target;
	var id=el.id.replace(/image_transition_editlink_/,'');
	var trans_types=["none", "fade", "scrollUp", "scrollDown", "scrollLeft", "scrollRight", "scrollHorz", "scrollVert", "slideX", "slideY", "shuffle", "turnUp", "turnDown", "turnLeft", "turnRight", "zoom", "fadeZoom", "blindX", "blindY", "blindZ", "growX", "growY", "curtainX", "curtainY", "cover", "uncover", "toss", "wipe"];
	var d=$('<table id="image_transition_form"><tr><th>Folder holding the images to transition</th><td><select id="image_transition_folder"></select></td></tr><tr><th>Manage images in the folder</th><td><a href="javascript:image_transition_file_manager()">manage images</a></td></tr><tr><th>Transition Type</th><td><select id="image_transition_type"><option>'+trans_types.join('</option><option>')+'</select></td></tr><tr><th>Pause time in milliseconds</th><td><input class="small" id="image_transition_pause" /></td></tr></table>');
	$.getJSON('/ww.plugins/image-transition/admin/widget-form.php',{'get_image_transition':id},function(res){
		d.dialog({
			minWidth:630,
			minHeight:400,
			height:400,
			width:630,
			modal:true,
			buttons:{
				'Save':function(){
					$.post('/ww.plugins/image-transition/admin/widget-form.php',
						{
							'id':id,
							'action':'save',
							'directory':$('#image_transition_folder').val(),
							'trans_type':$('#image_transition_type').val(),
							'pause':+$('#image_transition_pause').val()
						},
						function(ret){
							if(ret.id!=ret.was_id){
								el.id='image_transition_editlink_'+ret.id;
							}
							id=ret.id;
							var w=$(el).closest('.widget-wrapper');
							var wd=w.data('widget');
							wd.id=id;
							w.data('widget',wd);
							updateWidgets(w.closest('.panel-wrapper'));
							d.dialog('close');
							$('#image_transition_form').remove();
						}
					,'json');
				},
				'Close':function(){
					d.dialog('close');
					$('#image_transition_form').remove();
				}
			}
		});
		var sel=$('#image_transition_folder'),i,dir;
		for(i=0;i<res.directories.length;++i){
			dir=res.directories[i];
			$('<option></option>').text(dir).attr('value',dir).appendTo(sel);
		}
		sel.val(res.data.directory);
		$('#image_transition_type').val(res.data.trans_type);
		$('#image_transition_pause').val(res.data.pause);
	});
}
$('.image_transition_editlink').each(function(){
	if(this.content_click_added)return;
	$(this).click(image_transition_edit);
	this.content_click_added=true;
})
</script>
