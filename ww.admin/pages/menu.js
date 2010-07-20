function pages_add_subpage(node,tree){
	var p=node[0].id.replace(/.*_/,'');
	pages_new(p);
}
function pages_add_main_page(){
	pages_new(0);
}
function pages_new(p){
	$('<form id="newpage_dialog" action="/ww.admin/pages.php" method="post"><input type="hidden" name="prefill_body_with_title_as_header" value="1" /><input type="hidden" name="action" value="Insert Page Details" /><input type="hidden" name="special[1]" value="1" /><input type="hidden" name="newpage_dialog" value="1" /><input type="hidden" name="parent" value="'+p+'" /><table><tr><th>Name</th><td><input name="name" /></td></tr><tr><th>Page Type</th><td><select name="type"><option value="0">normal</option></select></td></tr><tr><th>Associated Date</th><td><input name="associated_date" class="date-human" id="newpage_date" /></td></tr></table></form>').dialog({
		modal:true,
		buttons:{
			'Create Page': function() {
				if($('#newpage_dialog input[name="name"]').val()=='')return alert('Name must be provided');
				document.getElementById('newpage_dialog').submit();
			},
			'Cancel': function() {
				$(this).remove();
			}
		}
	});
	$('#newpage_dialog select[name=type]').remoteselectoptions({url:'/ww.admin/pages/get_types.php'});
	$('#newpage_date').each(convert_date_to_human_readable);
	return false;
}
function pages_delete(node,tree){
	if(!confirm("Are you sure you want to delete this page?"))return;
	$.getJSON('/ww.admin/pages/delete.php?id='+node[0].id.replace(/.*_/,''),function(){
		document.location=document.location.toString();
	});
}
function pages_validate(){
	var ok=pages_validate_name();
	if(ok)return true;
	alert('Your form has errors. Hover the mouse over any inputs marked as errors to see explanations of those errors.');
	return false;
}
function pages_validate_name(){
	var $name=$('#name');
	var name=$name.val();
	var errors=[];
	if(name.length<4)errors.push('name must be at least 4 characters in length');
	else{
		if(/^[^a-zA-Z0-9]/.test(name))errors.push('Begins with non-alphanumeric character');
		if(/[^a-zA-Z0-9]$/.test(name))errors.push('Ends with non-alphanumeric character');
		if(/[^a-zA-Z0-9_ -]/.test(name))errors.push('Only use alphanumeric characters, spaces, hyphens or underscores');
	}
	if(!errors.length){
		$name[0].className='';
		$name[0].title='';
		return true;
	}
	$name[0].className='error';
	$name[0].title=errors.join('. ');
	return false;
}
$(function(){
	$('#pages-wrapper').tree({
		callback:{
			"onchange":function(node,tree){
				document.location='pages.php?action=edit&id='+node.id.replace(/.*_/,'');
			},
			"onmove":function(node){
				var p=$.tree.focused().parent(node);
				var new_order=[],nodes=node.parentNode.childNodes;
				for(var i=0;i<nodes.length;++i)new_order.push(nodes[i].id.replace(/.*_/,''));
				$.getJSON('/ww.admin/pages/move_page.php?id='+node.id.replace(/.*_/,'')+'&parent_id='+(p==-1?0:p[0].id.replace(/.*_/,''))+'&order='+new_order);
			}
		},
		plugins:{
			'contextmenu':{
				'items':{
					'create' : {
						'label'	: "Create Page", 
						'icon'	: "create",
						'visible'	: function (NODE, TREE_OBJ) { 
							if(NODE.length != 1) return 0; 
							return TREE_OBJ.check("creatable", NODE); 
						}, 
						'action':pages_add_subpage,
						'separator_after' : true
					},
					'rename': false,
					'remove' : {
						'label'	: "Delete Page", 
						'icon'	: "remove",
						'visible'	: function (NODE, TREE_OBJ) { 
							if(NODE.length != 1) return 0; 
							return TREE_OBJ.check("deletable", NODE); 
						}, 
						'action':pages_delete,
						'separator_after' : true
					}
				}
			}
		}
	});
	var div=$('<div><i>right-click for options</i><br /><br /></div>');
	$('<button>add main page</button>')
		.click(pages_add_main_page)
		.appendTo(div);
	div.appendTo('#pages-wrapper');
	$('#pages_form select[name=type]').remoteselectoptions({url:'/ww.admin/pages/get_types.php'});
	$('#pages_form select[name=parent]').remoteselectoptions({
		url:'/ww.admin/pages/get_parents.php',
		other_GET_params:page_menu_currentpage
	});
	$('#pages_form').submit(pages_validate);
	$('#name').keyup(pages_validate_name);
});
