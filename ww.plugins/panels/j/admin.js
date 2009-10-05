function updateWidgets(panel){
	var id=panel[0].id.replace(/panel/,'');
	var w_els=$('.widget-wrapper',panel);
	var widgets=[];
	for(var i=0;i<w_els.length;++i){
		widgets.push($(w_els[i]).data('widget'));
	}
	panel.data('widgets',widgets);
	var json=Json.toString({'widgets':widgets});
	$.post('/ww.plugins/panels/admin/save.php',{'id':id,'data':json});
}
function showWidgetForm(w){
	if(!w.length)w=$(this).closest('.widget-wrapper');
	var f=$('form',w);
	if(f.length){
		f.remove();
		return;
	}
	var form=$('<form></form>').appendTo(w);
	var p=w.data('widget');
	if(ww.widgetForms[p.type]){
		$('<button style="float:right">Save</button>')
			.click(function(){
				$('input,select').each(function(i,el){
					p[el.name]=$(el).val();
				});
				w.data('widget',p);
				updateWidgets(form.closest('.panel-wrapper'));
				return false;
			})
			.appendTo(form);
		var fholder=$('<div style="clear:both;border-bottom:1px solid #416BA7">loading...</div>').prependTo(form);
		p.panel=$('h4>span.name',form.closest('.panel-wrapper')).eq(0).text();
		fholder.load(ww.widgetForms[p.type],p);
	}
	else $('<p>no config form needed for this widget</p>').appendTo(form);
	$('<a href="javascript:;" title="remove widget">remove</a>')
		.click(function(){
			if(!confirm('Are you sure you want to remove this widget from this panel?'))return;
			var panel=w.closest('.panel-wrapper');
			w.remove();
			updateWidgets(panel);
		})
		.appendTo(form);
	$('<span>, </span>').appendTo(form);
	$('<a href="javascript:;">visibility</a>')
		.click(widget_visibility)
		.appendTo(form);
}
function buildRightWidget(p){
	var widget=$('<div class="widget-wrapper"><h4><span class="name">'+p.type+'</span></h4></div>')
		.data('widget',p);
	$('<span class="panel-opener">&darr;</span>')
		.appendTo($('h4',widget))
		.click(showWidgetForm);
	return widget;
}
function widget_visibility(ev){
	var el=ev.target,vis=[];
	var w=$(el).closest('.widget-wrapper');
	var wd=w.data('widget');
	if(wd.visibility)vis=wd.visibility;
	$.get('/ww.plugins/panels/admin/get-visibility.php?visibility='+vis,function(options){
		var d=$('<form><p>This panel will be visible in <select name="panel_visibility_pages[]" multiple="multiple">'+options+'</select>. If you want it to be visible in all pages, please choose <b>none</b> to indicate that no filtering should take place.</p></form>');
		d.dialog({
			width:300,
			height:400,
			close:function(){
				$('#panel_visibility_pages').remove();
				d.remove();
			},
			buttons:{
				'Save':function(){
					var arr=[];
					$('input[name="panel_visibility_pages[]"]:checked').each(function(){
						arr.push(this.value);
					});
					wd.visibility=arr;
					w.data('widget',wd);
					updateWidgets(w.closest('.panel-wrapper'));
					d.dialog('close');
				},
				'Close':function(){
					d.dialog('close');
				}
			}
		});
		$('select').inlinemultiselect({
			'separator':', ',
			'endSeparator':' and '
		});
	});
}
function panel_visibility(id){
	$.get('/ww.plugins/panels/admin/get-visibility.php',{'id':id},function(options){
		var d=$('<form><p>This panel will be visible in <select name="panel_visibility_pages[]" multiple="multiple">'+options+'</select>. If you want it to be visible in all pages, please choose <b>none</b> to indicate that no filtering should take place.</p></form>');
		d.dialog({
			width:300,
			height:400,
			close:function(){
				$('#panel_visibility_pages').remove();
				d.remove();
			},
			buttons:{
				'Save':function(){
					var arr=[];
					$('input[name="panel_visibility_pages[]"]:checked').each(function(){
						arr.push(this.value);
					});
					$.get('/ww.plugins/panels/admin/save-visibility.php?id='+id+'&pages='+arr);
					d.dialog('close');
				},
				'Close':function(){
					d.dialog('close');
				}
			}
		});
		$('select').inlinemultiselect({
			'separator':', ',
			'endSeparator':' and '
		});
	});
}
function panels_init(panel_column){
	for(var i=0;i<ww.panels.length;++i){
		var p=ww.panels[i];
		$('<div class="panel-wrapper" id="panel'+p.id+'"><h4><span class="name">'
				+p.name+'</span></h4>'
				+'<a href="javascript:panel_visibility('
				+p.id+')" class="visibility" style="display:none">visibility</a></div>'
			)
			.data('widgets',p.widgets.widgets)
			.appendTo(panel_column);
	}
}
function widgets_init(widget_column){
	for(var i=0;i<ww.widgets.length;++i){
		var p=ww.widgets[i];
		$('<div class="widget-wrapper"><h4>'+p.type+'</h4><p>'+p.description+'</p></div>')
			.appendTo(widget_column)
			.data('widget',p);
		ww.widgetsByName[p.type]=p;
	}
}
$(document).ready(function(){
	var panel_column=$('#panels');
	var widget_column=$('#widgets');
	ww.widgetsByName={};
	panels_init(panel_column);
	widgets_init(widget_column);
	$('<span class="panel-opener">&darr;</span>')
		.appendTo('.panel-wrapper h4')
		.click(function(){
			var $this=$(this);
			var panel=$this.closest('div');
			if($('.panel-body',panel).length){
				$('.visibility',panel).css('display','none');
				return $('.panel-body',panel).remove();
			}
			$('.visibility',panel).css('display','block');
			var widgets_container=$('<div class="panel-body"></div>');
			widgets_container.appendTo(panel);
			var widgets=panel.data('widgets');
			for(var i=0;i<widgets.length;++i){
				var p=widgets[i];
				buildRightWidget(p).appendTo(widgets_container);
			}
			$('.panel-body').sortable({
				'stop':function(){
					updateWidgets($(this).closest('.panel-wrapper'));
				}
			});
		});
	$('#widgets').sortable({
		'connectWith':'.panel-body',
		'stop':function(ev,ui){
			var item=ui.item;
			var panel=item.closest('.panel-wrapper');
			if(!panel.length)return $(this).sortable('cancel');
			var p=ww.widgetsByName[$('h4',ui.item).text()];
			var clone=buildRightWidget({'type':p.type});
			showWidgetForm(clone);
			clone.insertBefore(ui.item);
			$(this).sortable('cancel');
			updateWidgets(panel);
		}
	})
	$('<br style="clear:both" />').appendTo(widget_column);
});
