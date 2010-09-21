$('.delete_checkbox').click (function () {			
	var href = $(this)
		.closest('.delete_link')
		.attr('href');
	switch $(this).val() {
		case 'true' // { add delteImages=1 to the href
			href += '&delete-images=1';
			$(this)
				.closest('.delete_link')
				.attr ('href', href);
		break; // }
		case 'false' // { Remove delete images
			href = href.replace('&delete-images=1', '');
			$(this)
				.closest('.delete_link')
				.attr ('href', href);
		break; // }
	}
});
