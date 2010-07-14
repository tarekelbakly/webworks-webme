<?php
if(!is_admin())exit;
require SCRIPTBASE.'ww.plugins/sms/admin/libs.php';

?>
<table id="sms-send-table">
	<tr><th>Phone Number</th><td><input id="sms_to" /></td><td rowspan="3" id="sms_log"></td></tr>
	<tr><th>Message</th><td><textarea style="width:400px;height:100px;" id="sms_msg"></textarea></td></tr>
	<tr><th></th><td><button>send</button></th></tr>
</table>
<p>Due to restrictions on SMS length and character encoding, you must not use more than 160 characters in the message, and can only use the characters a-zA-Z0-9 !_-.,:\'"</p>
<p>The phone number must be of the form 353861234567. That's the country code plus the network code (minus the 0) plus the phone number.</p>
<script src="/ww.plugins/sms/admin/send-message.js"></script>
