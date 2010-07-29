function products_categories_save_attrs(ret){
	$.post('/ww.plugins/products/admin/save-category-attrs.php?id='+window.selected_cat,{
		"name"   :$('#pc_edit_name').val(),
		"enabled":$('#pc_edit_enabled').val()
	},products_categories_show_attrs,'json');
}
function products_categories_show_attrs(ret){
	window.selected_cat=ret.attrs.id;
	// { Remove the links so that they don't get added twice
	$('#create_link,#frontend_link').remove();
	// }
	var table=$('#products-categories-attrs>table');
	if(!table.length){
		table='<table style="width:100%">';
		// { name
		table+='<tr><th>Name</th><td><input id="pc_edit_name" /></td></tr>';
		// }
		// { enabled
		table+='<tr><th>Enabled</th><td><select id="pc_edit_enabled"><option value="1">Yes</option><option value="0">No</option></td></tr>';
		// }
		// { products
		table+='<tr id="products"><th>Products</th><td><form><select name="pc_edit_products[]" id="pc_edit_products" multiple="multiple">';
		for(var i=0;i<window.product_names.length;++i){
			table+='<option value="'+window.product_names[i][1]+'">'+window.product_names[i][0]+'</option>';
		}
		table+='</select></form></td></tr>';
		// }
		// { delete
		table+='<tr><th>Delete</th><td><a href="javascript:products_categories_delete()">[x]</a></td></tr>';
		// }
		table+='</table>';
		table=$(table).appendTo('#products-categories-attrs');
		$('#pc_edit_products').inlinemultiselect({
			"endSeparator":", ",
			"onClose":function(){
				var selected=[];
				$('#pc_edit_products input:checked').each(function(i, opt){
			    selected.push($(opt).val());
				});
				$.post('/ww.plugins/products/admin/save-category-products.php?id='+window.selected_cat,{
					"s[]":selected
				},products_categories_show_attrs,'json');
			}
		});
	}
	if(!document.getElementById('cat_'+ret.attrs.id)){
		$.tree.focused().create(
			{
				data:ret.attrs.name,
				attributes: { id :'cat_'+ret.attrs.id}
			},
			ret.attrs.parent_id?'#cat_'+ret.attrs.parent_id:-1,
			ret.attrs.parent_id?'inside':-1
		);
	}
	$('#cat_'+ret.attrs.id+' a').text(ret.attrs.name);
	$('#pc_edit_name').val(ret.attrs.name);
	switch(ret.attrs.enabled){
		case '0': // disabled
			$('#cat_'+ret.attrs.id+' a').addClass('disabled');
			$('#create_link').remove();
			break;
		default:  // enabled
			$('#cat_'+ret.attrs.id+' a').removeClass('disabled');
			if (ret.page==null) {
				$(
					'<tr id="create_link"><th>Link</th>'+
					'<td><a href="javascript:;" id="page_create_link"'+
					'onClick='+
					'"createPopup(\''+ret.attrs.name+'\', '+ret.attrs.id+', 2);"'
					+'>Create a page for this category</a></td></tr>'
				).insertAfter($('#products'));
			}
	}
	if (ret.page!=null) {
		$(
			'<tr id="frontend_link"><th>Link</th>'+
			'<td><a href="'+ret.page+'" target=_blank>'+
			'View this category on the frontend</a></td></tr>'
		).insertAfter('#products');
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
	if($.tree.focused().selected[0].id!='cat_'+ret.attrs.id)$.tree.focused().select_branch('#cat_'+ret.attrs.id);
}
function products_categories_add_subcategory(){
	var name=prompt('what do you want to name this sub-category?');
	if(!name)return;
	$.getJSON('/ww.plugins/products/admin/add-new-category.php',{
		"parent_id":$.tree.focused().selected[0].id.replace(/.*_/,''),
		"name":name
	},products_categories_show_attrs);
}
function products_categories_add_main_category(){
	var name=prompt('what do you want to name this category?');
	if(!name)return;
	$.getJSON('/ww.plugins/products/admin/add-new-category.php',{
		"parent_id":0,
		"name":name
	},function(){
		document.location=document.location;
	});
}
function products_categories_delete(){
	if(!confirm("Are you sure you want to delete this category?"))return;
	$.getJSON('/ww.plugins/products/admin/delete-category.php?id='+window.selected_cat,function(){
		document.location="/ww.admin/plugin.php?_plugin=products&_page=categories";
	});
}
$('#pc_edit_name, #pc_edit_enabled').live('change',products_categories_save_attrs);
$(function(){
	$('#categories-wrapper').tree({
		selected:'cat_'+window.selected_cat,
		types:{
			"default":{
				icon:{
					image: false
				}
			}
		},
		callback:{
			"onchange":function(node,tree){
				$.getJSON('/ww.plugins/products/admin/get-category-attrs.php?id='+node.id.replace(/.*_/,''),products_categories_show_attrs);
			},
			"onmove":function(node){
				var p=$.tree.focused().parent(node);
				$.getJSON('/ww.plugins/products/admin/move-category.php?id='+node.id.replace(/.*_/,'')+'&parent_id='+(p==-1?0:p[0].id.replace(/.*_/,'')),products_categories_show_attrs);
			}
		}
	});
	var div=$('<div style="clear:both;padding-top:20px;" />');
	$('<button>add sub-category</button>').click(products_categories_add_subcategory).appendTo(div);
	$('<button>add main category</button>').click(products_categories_add_main_category).appendTo(div);
	div.appendTo('#categories-wrapper');
	$.getJSON('/ww.plugins/products/admin/get-category-attrs.php?id='+window.selected_cat,products_categories_show_attrs);
});
