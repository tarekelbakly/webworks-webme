function publisher_start() {
	document.getElementById('publisher-start').href="javascript:alert('only click once')";
	$('#publisher-wrapper')
		.html('<ol>'
			+'<li id="publisher-1">getting page HTML</li>'
			+'<li id="publisher-2"></li>'
			+'<li id="publisher-3"></li>'
			+'<li id="publisher-4"></li>'
			+'<li id="publisher-5"></li>'
			+'<li id="publisher-6">zip it all up</li>'
			+'</ol>'
		);
	publisher_step1();
}
function publisher_step1(){
	$('#publisher-1')
		.css('text-decoration','blink')
		.append(' ... ');
	$.post('/ww.plugins/publisher/admin/step1-getHTML.php',publisher_step2);
}
function publisher_step2(){
	$('#publisher-1')
		.css({
			'text-decoration':'none',
			'color':'#666'
		})
		.append(' completed');
	$('#publisher-2')
		.text('correcting page links... ')
		.css('text-decoration','blink');
	$.post('/ww.plugins/publisher/admin/step2-correctPageLinks.php',publisher_step3);
}
function publisher_step3(){
	$('#publisher-2')
		.css({
			'text-decoration':'none',
			'color':'#666'
		})
		.append(' completed');
	$('#publisher-3')
		.css('text-decoration','blink')
		.text('getting list of CSS, Images, JavaScript files... ');
	$.post('/ww.plugins/publisher/admin/step3-getCSSandJavaScript.php',publisher_step4);
}
function publisher_step4(){
	$('#publisher-3')
		.css({
			'text-decoration':'none',
			'color':'#666'
		})
		.append(' completed');
	$('#publisher-4')
		.css('text-decoration','blink')
		.text('downloading CSS, Images, JavaScript files ...');
	$.post('/ww.plugins/publisher/admin/step4-getCSSandJavaScriptFiles.php',publisher_step5);
}
function publisher_step5(){
	$('#publisher-4')
		.css({
			'text-decoration':'none',
			'color':'#666'
		})
		.append(' completed');
	$('#publisher-5')
		.css('text-decoration','blink')
		.text('getting list of image references in CSS files');
	$.post('/ww.plugins/publisher/admin/step5-getImageReferencesInCssFiles.php',publisher_step6);
}
function publisher_step6(){
	$('#publisher-5')
		.css({
			'text-decoration':'none',
			'color':'#666'
		})
		.append(' completed');
	$('#publisher-6')
		.css('text-decoration','blink')
		.text('download images referenced in CSS files');
	$.post('/ww.plugins/publisher/admin/step6-downloadImageReferencesInCssFiles.php',publisher_step7);
}
function publisher_step7(){

}
