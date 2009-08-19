(function($){
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
			fholder.load(ww.widgetForms[p.type],p);
		}
		else $('<p>no config form associated with this widget</p>').appendTo(form);
		$('<a href="javascript:;" title="remove widget">remove</a>')
			.click(function(){
				if(!confirm('Are you sure you want to remove this widget from this panel?'))return;
				var panel=w.closest('.panel-wrapper');
				w.remove();
				updateWidgets(panel);
			})
			.appendTo(form);
	}
	function buildRightWidget(p){
		var widget=$('<div class="widget-wrapper"><h4>'+p.type+'</h4></div>')
			.data('widget',p);
		$('<span class="panel-opener">&darr;</span>')
			.appendTo($('h4',widget))
			.click(showWidgetForm);
		return widget;
	}
	$(document).ready(function(){
		var panel_column=$('#panels');
		var widget_column=$('#widgets');
		ww.widgetsByName={};
		for(var i=0;i<ww.panels.length;++i){
			var p=ww.panels[i];
			$('<div class="panel-wrapper" id="panel'+p.id+'"><h4>'+p.name+'</h4></div>')
				.data('widgets',p.widgets.widgets)
				.appendTo(panel_column);
		}
		for(var i=0;i<ww.widgets.length;++i){
			var p=ww.widgets[i];
			$('<div class="widget-wrapper"><h4>'+p.type+'</h4><p>'+p.description+'</p></div>')
				.appendTo(widget_column)
				.data('widget',p);
			ww.widgetsByName[p.type]=p;
		}
		$('<span class="panel-opener">&darr;</span>')
			.appendTo('.panel-wrapper h4')
			.click(function(){
				var $this=$(this);
				var panel=$this.closest('div');
				if($('.panel-body',panel).length){
					return $('.panel-body',panel).remove();
				}
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
				var clone=buildRightWidget(p);
				showWidgetForm(clone);
				clone.insertBefore(ui.item);
				$(this).sortable('cancel');
				updateWidgets(panel);
			}
		})
	});
})(jQuery);
