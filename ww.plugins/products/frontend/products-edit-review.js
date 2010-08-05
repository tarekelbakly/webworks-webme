function edit_review (id, text, rating) {
	while (text.indexOf('<br/>')>=0) {
		text = text.replace('<br/>', "\n");
	}
	var form = '<div style="text-align:left" id="form">'
	form = form + '<b>Rating: </b>';
	form = form + '<small><i>The higher the rating the better </i></small>';
	form = form + '<select id="rating">';
	for (i=1; i<=5; i++) {
		form = form + '<option';
		if (i==rating) {
			form = form + ' selected="selected"';
		}
		form = form+'>'+i+'</option>';
	}
	form = form + '</select>';
	form = form + '<textarea cols="50" rows="10" id="text">';
	form = form + text;
	form = form + '</textarea>';
	form = form + '<center>';
	form = form + '<input type="button" name="submit"';
	form = form + 'value="Save Review" onClick="submit_vals('+id+')";/></div>';
	$(form).insertBefore('#'+id);
	$('#'+id).remove();
}
function submit_vals(id) {
	$.post(
		'ww.plugins/products/frontend/update-review.php',
		{
			"id":id,
			"text":$('#text').val(),
			"rating":$('#rating').val()
		},
		display,
		"json"
	);
}
function display(data) {
	if (!data.status) {
		return alert ('Could not edit this review');
	}
	var averageText = '<div id="avg">';
	averageText = averageText + 'The average rating for this product over ';
	averageText = averageText + data.total;
	averageText = averageText + ' review';
	if (data.total!=1) {
		averageText = averageText + 's';
	}
	averageText = averageText + ' was ' + data.avg;
	averageText = averageText + '<br/><br/></div>';
	$(averageText).insertBefore('#average'+data.product);
	var body = data.body
	while (body.indexOf("\n")>=0) {
		body = body.replace("\n", '<br/>');
	}
	var reviewText = '<div id="'+data.id+'">Posted by ' + data.user;
	reviewText = reviewText + ' on ' + data.date;
	reviewText = reviewText + ' <b>Rated: </b>' + data.rating;
	reviewText = reviewText + '<br/>' + body + '<br/>';
	reviewText = reviewText + '<a href="javascript:'
		+'edit_review('+data.id+', \''+body+'\', '+data.rating+')">edit</a> ';
	reviewText = reviewText + '<a href="javascript:'
		+'delete_review('+data.id+','+data.user_id+' ,'+data.product+')">'
	reviewText = reviewText + '[x]</a>';
	reviewText = reviewText + '</div>';
	$(reviewText).insertAfter('#form');
	$('#form').remove();
	$('#average'+data.product).remove();
	$('#avg').attr('id', 'average'+data.product);
}
