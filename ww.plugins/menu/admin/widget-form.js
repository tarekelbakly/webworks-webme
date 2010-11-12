ww.menu={
	selected_dir:'/'
};
function menu_edit(ev){
	var el=ev.target;
	var id=el.id.replace(/menu_editlink_/,'');
	// { build the HTML for the form
	var d=$('<div id="menu_form">'
		+'<ul><li><a href="#menu_main">Main</a></li><li><a href="#menu_style">Sub-menu Style</a></li></ul>'
		+'<div id="menu_main"><table>'
		+'<tr><th>Parent Page</th><td><select id="menu_parent"></select></td></tr>'
		+'<tr><th>Direction</th><td><select id="menu_direction">'
			+'<option value="1">Vertical</option>'
			+'<option value="0">Horizontal</option>'
		+'</select></td></tr>'
		+'<tr id="row-menu-type"style="display:none"><th>Type</th><td>'
		+'<select name="menu_type_v">'
			+'<option value="0">Drop-down</option>'
			+'<option value="1">Accordion</option>'
		+'</select></td></tr>'
		+'</table></div>'
		+'<div id="menu_style"><table><tr><th>Sub-menu Background</th><td><input id="menu_background" /><div id="menu_background_picker" style="width:195px;height:195px;"></div></td></tr><tr><th>Opacity</th><td><input id="menu_opacity" /><div id="menu_opacity_slider"></div></td></tr><tr><th>Columns</th><td><input id="menu_columns" class="small" /></td></tr></table></div>'
	+'</div>');
	// }
	$.getJSON('/ww.plugins/menu/admin/widget-form.php',{'get_menu':id},function(res){
		d.dialog({
			modal:true,
			width:400,
			buttons:{
				'Save':function(){
					var direction=+$('#menu_direction').val();
					$.post('/ww.plugins/menu/admin/widget-form.php',
						{
							'id':id,
							'action':'save',
							'parent':$('#menu_parent').val(),
							'direction':+direction,
							'type':(direction?$('select[name=menu_type_v]').val():''),
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
		$('#menu_direction')
			.val(+res.direction)
			.change(function(){
				var val= +$(this).val();
				$('#row-menu-type').css(
					'display',
					val?'table-row':'none'
				);
			});
		if (res.direction==1) {
			$('#row-menu-type').css('display','table-row');
		}
		$('select[name=menu_type_v]').val(res.type);
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
$('.menu_editlink').live('click',menu_edit);
