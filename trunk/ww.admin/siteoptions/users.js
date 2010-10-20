$(function(){
	var active=$('select[name=active]').val();
	$('<a href="javascript:;" style="float:right;text-decoration:none" title="add a new group">[+]</a>')
		.click(function(){
			$('<input name="new_groups[]" />').appendTo('.groups');
		})
		.prependTo('.groups');
	$('select[name=active],input[name=password],input[name=email]').change(function(){
		var val=+$('select[name=active]').val(),msg;
		$('#users-email-to-send-holder').empty();
		$('#users-email-to-send').css('display','none');
		if(val==active){
			return;
		}
		var name=$('input[name=name]').val(),email=$('input[name=email]').val(),password=$('input[name=password]').val();
		msg=val
			?'Dear '+name+',\n\nWe have activated your account.\n\nYou can log in using your email address "'+email+'" and the password "'+password+'"\n\nThank you.'
			:'Dear '+name+',\n\nWe have de-activated your account.\n\nThank you.'
		$('<textarea name="email-to-send">'+msg+'</textarea>')
			.appendTo('#users-email-to-send-holder');
		$('#users-email-to-send')
			.css('display','table-row');
	});
});
