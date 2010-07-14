function sms_show_paypal_button(res){
	if(res.status)$('#sms_paypal_button_holder').html(res.message);
	else $('#sms_paypal_button_holder').html(res.error);
}
$(function(){
	$('#sms_purchase_amt').change(function(){
		var amt=+$('#sms_purchase_amt').val();
		if(!amt)return $('#sms_paypal_button_holder').empty();
		$.post('/ww.plugins/sms/admin/get-paypal-button.php',{
			"amt":amt
		},sms_show_paypal_button,'json');
	});
});
