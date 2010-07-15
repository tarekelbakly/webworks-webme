function sms_change_type(){
	var val=$('#sms_send_type').val();
	if(val=='Phone Number'){
		$('#sms_addressbook_id').css('display','none');
		$('#sms_to').css('display','inline-block');
	}
	else{
		$('#sms_addressbook_id').css('display','inline-block');
		$('#sms_to').css('display','none');
	}
}
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
	sms_check_msg();
	var msg=$('#sms_msg').val();
	if(msg=='')return alert('no message!');
	if($('#sms_send_type').val()=='Phone Number'){
		sms_check_to();
		$.post('/ww.plugins/sms/admin/send.php',{
			"to":$('#sms_to').val(),
			"msg":msg
		},sms_sent,'json');
	}
	else{
		var aid=$('#sms_addressbook_id').val();
		if(aid==0)return alert('please choose an addressbook');
		$.post('/ww.plugins/sms/admin/send-bulk.php',{
			"to":aid,
			"msg":msg
		},sms_sent_bulk,'json');
	}
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
function sms_sent_bulk(ret){
	var msg='';
	if(!ret.status){
		msg='<p><i>'+ret.error+'</i></p>';
	}
	else{
		msg='<p>smses sent to addressbook #'+$('#sms_addressbook_id').val()+'</p>';
	}
	$('#sms_log').append(msg);
}
$(function(){
	$('#sms-send-table button').click(sms_send);
	$('#sms_to').keyup(sms_check_to);
	$('#sms_msg').keyup(sms_check_msg);
	$('#sms_send_type').change(sms_change_type);
});
