function pages_save_attrs(ret){
	$.post('/ww.admin/pages/save-page-attrs.php?id='+window.selected_page,{
		"name"   :$('#pc_edit_name').val(),
		"enabled":$('#pc_edit_enabled').val()
	},pages_show_attrs,'json');
}
function pages_show_attrs(ret){
	window.selected_page=ret.attrs.id;
	var table=$('#products-pages-attrs>table');
	if(!table.length){
		table='<table style="width:100%">';
		// { name
		table+='<tr><th>Name</th><td><input id="pc_edit_name" /></td></tr>';
		// }
		// { enabled
		table+='<tr><th>Enabled</th><td><select id="pc_edit_enabled"><option value="1">Yes</option><option value="0">No</option></td></tr>';
		// }
		// { products
		table+='<tr><th>Products</th><td><form><select name="pc_edit_products[]" id="pc_edit_products" multiple="multiple">';
		for(var i=0;i<window.product_names.length;++i){
			table+='<option value="'+window.product_names[i][1]+'">'+window.product_names[i][0]+'</option>';
		}
		table+='</select></form></td></tr>';
		// }
		// { delete
		table+='<tr><th>Delete</th><td><a href="javascript:pages_delete()">[x]</a></td></tr>';
		// }
		table+='</table>';
		table=$(table).appendTo('#products-pages-attrs');
		$('#pc_edit_products').inlinemultiselect({
			"endSeparator":", ",
			"onClose":function(){
				var selected=[];
				$('#pc_edit_products input:checked').each(function(i, opt){
			    selected.push($(opt).val());
				});
				$.post('/ww.admin/pages/save-page-products.php?id='+window.selected_page,{
					"s[]":selected
				},pages_show_attrs,'json');
			}
		});
	}
	if(!document.getElementById('page_'+ret.attrs.id)){
		$.tree.focused().create(
			{
				data:ret.attrs.name,
				attributes: { id :'page_'+ret.attrs.id}
			},
			ret.attrs.parent_id?'#page_'+ret.attrs.parent_id:-1,
			ret.attrs.parent_id?'inside':-1
		);
	}
	$('#page_'+ret.attrs.id+' a').text(ret.attrs.name);
	$('#pc_edit_name').val(ret.attrs.name);
	switch(ret.attrs.enabled){
		case '0': // disabled
			$('#page_'+ret.attrs.id+' a').addClass('disabled');
			break;
		default:  // enabled
			$('#page_'+ret.attrs.id+' a').removeClass('disabled');
	}
	$('#pc_edit_enabled').val(ret.attrs.enabled);
	var selected_names=[];
	$('#pc_edit_products input').each(function(i,opt){
		opt.checked=false;
		for(var i=0;i<ret.products.length;++i){
			if(opt.value==ret.products[i]){
				opt.checked='checked';
				selected_names.push($(opt.parentNode).text());
			}
		}
	});
	$('#pc_edit_productschoices').text(selected_names.join(', '));
	if($.tree.focused().selected[0].id!='page_'+ret.attrs.id)$.tree.focused().select_branch('#page_'+ret.attrs.id);
}
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
$('#pc_edit_name, #pc_edit_enabled').live('change',pages_save_attrs);
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
						label	: "Create Page", 
						icon	: "create",
						visible	: function (NODE, TREE_OBJ) { 
							if(NODE.length != 1) return 0; 
							return TREE_OBJ.check("creatable", NODE); 
						}, 
						action:pages_add_subpage,
						separator_after : true
					},
					'rename': false,
					'remove' : {
						label	: "Delete Page", 
						icon	: "remove",
						visible	: function (NODE, TREE_OBJ) { 
							if(NODE.length != 1) return 0; 
							return TREE_OBJ.check("deletable", NODE); 
						}, 
						action:pages_delete,
						separator_after : true
					},
				}
			}
		}
	});
	var div=$('<div><i>right-click for options</i><br /><br /></div>');
	$('<button>add main page</button>').click(pages_add_main_page).appendTo(div);
	div.appendTo('#pages-wrapper');
});
