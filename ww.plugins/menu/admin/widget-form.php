<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');

if(isset($_REQUEST['get_menu'])){
	$r=dbRow('select * from menus where id='.(int)$_REQUEST['get_menu']);
	if($r===false)$r=array(
		'parent'=>0,
		'direction'=>0
	);
	if($r['parent'])$r['parent_name']=Page::getInstance($r['parent'])->name;
	else $r['parent_name']=' -- none -- ';
	echo json_encode($r);
	exit;
}
if(isset($_REQUEST['action']) && $_REQUEST['action']=='save'){
	$id=(int)$_REQUEST['id'];
	$id_was=$id;
	$parent=(int)$_REQUEST['parent'];
	$direction=(int)$_REQUEST['direction'];
	$background=addslashes($_REQUEST['background']);
	$opacity=(float)$_REQUEST['opacity'];
	$columns=(int)$_REQUEST['columns'];
	$sql="menus set parent='$parent',direction='$direction',background='$background',opacity=$opacity,columns=$columns";
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
echo '<a href="javascript:;" id="menu_editlink_'.$id.'" class="menu_editlink">view or edit menu</a>';
?>
<script>
if(!ww.menu)ww.menu={
	selected_dir:'/'
};
function menu_edit(ev){
	var el=ev.target;
	var id=el.id.replace(/menu_editlink_/,'');
	var d=$('<div id="menu_form">'
		+'<ul><li><a href="#menu_main">Main</a></li><li><a href="#menu_style">Sub-menu Style</a></li></ul>'
		+'<div id="menu_main"><table><tr><th>Parent Page</th><td><select id="menu_parent"></select></td></tr><tr><th>Direction</th><td><select id="menu_direction"><option value="1">Vertical</option><option value="0">Horizontal</option></select></td></tr></table></div>'
		+'<div id="menu_style"><table><tr><th>Sub-menu Background</th><td><input id="menu_background" /><div id="menu_background_picker" style="width:195px;height:195px;"></div></td></tr><tr><th>Opacity</th><td><input id="menu_opacity" /><div id="menu_opacity_slider"></div></td></tr><tr><th>Columns</th><td><input id="menu_columns" class="small" /></td></tr></table></div>'
	+'</div>');
	$.getJSON('/ww.plugins/menu/admin/widget-form.php',{'get_menu':id},function(res){
		d.dialog({
			modal:true,
			width:400,
			buttons:{
				'Save':function(){
					$.post('/ww.plugins/menu/admin/widget-form.php',
						{
							'id':id,
							'action':'save',
							'parent':$('#menu_parent').val(),
							'direction':+$('#menu_direction').val(),
							'background':$('#menu_background').val(),
							'opacity':$('#menu_opacity').val(),
							'columns':$('#menu_columns').val()
						},
						function(ret){
							if(ret.id!=ret.was_id){
								el.id='menu_editlink_'+ret.id;
							}
							id=ret.id;
							var w=$(el).closest('.widget-wrapper');
							var wd=w.data('widget');
							wd.id=id;
							w.data('widget',wd);
							updateWidgets(w.closest('.panel-wrapper'));
							d.dialog('close');
							$('#menu_form').remove();
						}
					,'json');
				},
				'Close':function(){
					d.dialog('close');
					$('#menu_form').remove();
				}
			}
		});
		$('#menu_form').tabs();
		$('#menu_parent').html('<option value="'+res.parent+'">'+htmlspecialchars(res.parent_name)+'</option>');
		$('#menu_direction').val(+res.direction);
		if(!res.background)res.background='#ffffff';
		$('#menu_background')
			.val(res.background)
			.css('background-color',res.background);
		$('#menu_background_picker')
			.farbtastic('#menu_background');
		$('#menu_opacity')
			.val(+res.opacity)
			.css('display','none');
		$('#menu_opacity_slider')
			.slider({
				min:0,
				max:1,
				step:.05,
				value:+res.opacity,
				slide:function(ev,ui){
					$('#menu_opacity').val(ui.value);
				}
			});
		$('#menu_columns').val(+res.columns);
		setTimeout(function(){
			$('#menu_parent').remoteselectoptions({
				url:'/ww.admin/pages/get_parents.php'
			});
		},1);
	});
}
if(!window.menu_editlink_added)$('.menu_editlink').live('click',menu_edit);
window.menu_editlink_added=true;
</script>
