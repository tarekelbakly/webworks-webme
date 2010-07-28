function createPopup (defaultName, id, what) {
	var html
		= '<div id="dialog">Name'+
			'<input id="products_page_name" type="text"'+
			'value="'+defaultName+'"/><br/>'+
			'Parent <select id="products_page_parent">'+
						'<option value="0" selected="selected">'+
							'--none--'+
						'</option>'+
					'</select></div>'

	$(html).dialog(
		{
			modal:true,
			buttons:{
				'Create Page': function () {
					var name= $('#products_page_name').val();
					var parentPage= $('#products_page_parent').val();
					if(name=='') {
						name= defaultName;
					}
					while (name.lastIndexOf(" ")>-1) {
						name= name.replace(" ", "_");
					}
					$.getJSON (
						'/ww.plugins/products/admin/insert-page.php?id='+id+
						'&what='+what+
						'&name='+name+
						'&parent='+parentPage,
						confirm_create
					);
				},
				'Cancel': function () {
					$(this).remove();
				}
			}
		}
	);
	$('#products_page_parent').remoteselectoptions(
		{
			url:'pages/get_parents.php'
		}
	);
}
function confirm_create (data) {
	if (data) {
		alert(data.message);
	}
	if (data.status) {
		$(
			'<a href="'+data.url+'"target=_blank>'+
		  	'Click here to view this on the front end'+
		  	'</a>'
		)
		.insertBefore($('#page_create_link'));
		$('#page_create_link').remove();
	}
	$('#dialog').dialog('close');
}
