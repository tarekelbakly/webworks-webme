$('#os_discount_code').click(function(){
	var code=prompt('Please enter your Discount Code');
	if(!code)return;
	document.location=document.location.toString().replace(/\?.*/,'')+'?os_discount_code='+escape(code);
}).css({'cursor':'pointer'});
