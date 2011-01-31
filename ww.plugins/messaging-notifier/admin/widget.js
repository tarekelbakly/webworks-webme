if(!ww.messaging_notifier)ww.messaging_notifier={
	editor_instances:0
};
function messaging_notifier_edit(ev){
	var el=ev.target;
	var id=el.id.replace(/messaging_notifier_editlink_/,'');
	ww.messaging_notifier.editor_instances++;
	var d=$('<div id="messaging_notifier_table_wrapper'+ww.messaging_notifier.editor_instances+'"></div>');
	$.getJSON('/ww.plugins/messaging-notifier/admin/widget-form.php',{'get_messaging_notifier':id},function(res){
		d.dialog({
			minWidth:630,
			minHeight:400,
			height:400,
			width:630,
			buttons:{
				'Save':function(){
					var data=[];
					$('tr',d).each(function(){
						var sel=$('select',this),url=$('input.url',this),refresh=$('input.refresh',this);
						if(!sel.length || !url.length)return;
						refresh=parseInt(refresh.val());
						if(refresh<1)refresh=60;
						var arr={
							'type':sel.val(),
							'url':url.val(),
							'refresh':refresh
						};
						if(!arr.type || arr.type=='--none--' || !arr.url)return;
						data.push(arr);
					});
					$.post('/ww.plugins/messaging-notifier/admin/widget-form.php',{'id':id,'action':'save','data':Json.toString(data)},function(ret){
						if(ret.id!=ret.was_id){
							el.id='messaging_notifier_editlink_'+ret.id;
						}
						id=ret.id;
						var w=$(el).closest('.widget-wrapper');
						var wd=w.data('widget');
						wd.id=id;
						w.data('widget',wd);
						updateWidgets(w.closest('.panel-wrapper'));
						d.dialog('close');
						d.remove();
					},'json');
				},
				'Close':function(){
					d.dialog('close');
					d.remove();
				}
			}
		});
		var t=$('<table style="width:100%"><tr><th>Type</th><th>URL</th><th>Refresh<br />(minutes)<th></tr>');
		for(var i=0;i<res.data.length;++i){
			t.append(messaging_notifier_table_row(res.data[i]));
		}
		t.append(messaging_notifier_table_row());
		t.appendTo(d);
	});
}
function messaging_notifier_table_row(rdata){
	var ts=[
		['email','enter in this form: username|password|mailserver|optional_link_url'],
		['phpBB3','address of the forum. example: http://forum.php.ie/viewforum.php?f=2'],
		['RSS','address of the RSS feed. example: http://planet.php.ie/rss/'],
		['Twitter','address of the twitter account. example: http://twitter.com/IrishPhpUG']
	];
	if(!rdata)rdata={'type':'','url':'','refresh':60};
	var tr='<tr><td><select><option>--none--</option>';
	for(var j=0;j<ts.length;++j){
		tr+='<option title="'+ts[j][1]+'"';
		if(rdata.type==ts[j][0])tr+=' selected="selected"';
		tr+='>'+ts[j][0]+'</option>';
	}
	tr+='</select></td>';
	tr+='<td><input style="width:100%" class="url" value="'+rdata.url+'" /><span></span></td>';
	tr+='<td><input size="3" class="refresh" value="'+rdata.refresh+'" /></td>';
	tr+='</tr>';
	var $tr=$(tr);
	$('select',$tr).change(function(){
		var opt=this.getElementsByTagName('option')[this.selectedIndex];
		$('span',$(this).closest('tr')).html(opt.title);
		if(opt.title)$tr.closest('table').append(messaging_notifier_table_row());
	});
	return $tr;
}
$('.messaging_notifier_editlink').live('click', messaging_notifier_edit);
