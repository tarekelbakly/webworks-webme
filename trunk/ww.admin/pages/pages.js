function pages_delete(){
	if(!confirm('Are you sure you want to delete this?'))return false;
	var p,link=$(this);
	pel=link.closest('li');
	p=$(this)[0].id.replace(/pages_d/,'');
	if(p==page_menu_currentpage || pel.hasClass('hasChildren'))document.location='/ww.admin/pages.php?action=delete&id='+p;
	else $.get('/ww.admin/pages/delete.php?id='+p,function(res){
		if(res)$(res).dialog();
		else link.closest('li').hide(500);
	});
	return false;
}
function pages_new(p){
	if(!p)p=$(this)[0].id.replace(/pages_n/,'');
	$('<form id="newpage_dialog" action="/ww.admin/pages.php" method="post"><input type="hidden" name="prefill_body_with_title_as_header" value="1" /><input type="hidden" name="action" value="Insert Page Details" /><input type="hidden" name="special[1]" value="1" /><input type="hidden" name="newpage_dialog" value="1" /><input type="hidden" name="parent" value="'+p+'" /><table><tr><th>Name</th><td><input name="name" /></td></tr><tr><th>Page Type</th><td><select name="type"><option value="0">normal</option></select></td></tr></table></form>').dialog({
		modal:true,
		buttons:{
			'Create Page': function() {
				document.getElementById('newpage_dialog').submit();
			},
			'Cancel': function() {
				$(this).dialog('close');
			}
		}
	});
	$('#newpage_dialog select[name=type]').remoteselectoptions({url:'/ww.admin/pages/get_types.php'});
	return false;
}
function pages_menu_goto(){
	var id=$(this)[0].id.replace(/pages_name/,'');
	document.location='/ww.admin/pages.php?action=edit&id='+id;
}
function pages_menu_init(){
	$('<ul id="pages_w0" class="pages_menu_wrapper"></ul><a href="#" class="newtoppage" id="page_n0">CLICK HERE FOR A NEW TOP-LEVEL PAGE</a>').appendTo('#page_menu');
	pages_menu_load_subpages(0);
}
function pages_menu_load_subpages(p){
	$.getJSON('/ww.admin/pages/get_subpages.php?p='+p,pages_menu_refresh_subpages);
}
function pages_menu_refresh_subpages(ret){
	var pid=ret.pid,pages=ret.subpages,i,parr=[],page,html;
	for(i=0;i<pages.length;++i){
		page=pages[i];
		html='<li id="pages_p'+page.id+'" class="pages_menu_page'+(+page.numchildren?' hasChildren':'')+(page.id==page_menu_currentpage?' current_page':'')+'">';
		html+='<div class="pages_menu_name" id="pages_name'+page.id+'">'+page.name+'</div>';
		html+='<div class="pages_menu_expander" id="pages_e'+page.id+'" class="closed"></div>';
		html+='<div class="newsubpage" id="pages_n'+page.id+'">[n]</div>';
		html+='<div class="deletepage" id="pages_d'+page.id+'">[x]</div>';
		html+='<ul class="pages_menu_wrapper" id="pages_w'+page.id+'"></ul>';
		html+='</li>';
		parr.push(html);
	}
	$(parr.join('')).appendTo($('#pages_w'+pid).empty());
	$('.pages_menu_wrapper').sortable({
		'connectWith':'.pages_menu_wrapper',
		'tolerance':'pointer',
		'placeholder':'placeholder',
		'cursor':'pointer',
		'start':pages_menu_record_start,
		'stop':pages_menu_record_stop
	})
	$('.pages_menu_expander').click(pages_menu_expand);
	$('.newtoppage,.newsubpage').click(pages_new);
	$('.deletepage').click(pages_delete);
	$('.pages_menu_name').click(pages_menu_goto);
}
function pages_menu_expand(){
	var $this=$(this);
	var pid=$this.attr('id').replace(/pages_e/,'');
	if($this.hasClass('open')){
		$('#pages_w'+pid).empty();
	}
	else pages_menu_load_subpages(pid);
	$this.toggleClass('open');
}
function pages_menu_record_start(ev,ui){
	var item=ui.item[0];
	pages_menu_vars.f_item=item.id.replace(/pages_p/,'');
	pages_menu_vars.f_parent=item.parentNode.id.replace(/pages_w/,'');
}
function pages_menu_record_stop(ev,ui){
	var item=ui.item[0],i,item_ids=[];
	pages_menu_vars.t_parent=item.parentNode.id.replace(/pages_w/,'');
	var items=item.parentNode.childNodes;
	for(i=0;i<items.length;++i){
		item_ids.push(items[i].id.replace(/pages_p/,''));
	}
	$.get('/ww.admin/pages/move_page.php?id='+pages_menu_vars.f_item+'&t='+pages_menu_vars.t_parent+'&order='+item_ids);
}
$(document).ready(function(){
	pages_menu_init();
	$('.newtoppage,.newsubpage').click(pages_new);
	$('.deletepage').click(pages_delete);
	$('#pages_form select[name=type]').remoteselectoptions({url:'/ww.admin/pages/get_types.php'});
	$('#pages_form select[name=parent]').remoteselectoptions({
		url:'/ww.admin/pages/get_parents.php',
		other_GET_params:page_menu_currentpage
	});
});
window.pages_menu_vars={};
