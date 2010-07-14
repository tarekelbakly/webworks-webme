function sms_check_to(){
	var to=$('#sms_to').val();
	var newto=to.replace(/[^0-9]*/g,'');
	if(to!=newto)$('#sms_to').val(newto);
}
function sms_check_msg(){
	var msg=$('#sms_msg').val();
	var newmsg=msg.replace(/[^a-zA-Z0-9 !_\-.,:'"]*/g,'');
	if(newmsg.length>160)newmsg=newmsg.substring(0,160);
	if(msg!=newmsg)$('#sms_msg').val(newmsg);
}
function sms_send(){
	sms_check_to();
	sms_check_msg();
	$.post('/ww.plugins/sms/admin/send.php',{
		to:$('#sms_to').val(),
		msg:$('#sms_msg').val()
	},sms_sent,'json');
}
function sms_sent(ret){
	var msg='';
	if(!ret.status){
		msg='<p><i>'+ret.error+'</i></p>';
	}
	else{
		msg='<p>sms sent to '+$('#sms_to').val()+'</p>';
	}
	$('#sms_log').append(msg);
}
$(function(){
	$('#sms-send-table button').click(sms_send);
	$('#sms_to').keyup(sms_check_to);
	$('#sms_msg').keyup(sms_check_msg);
});
