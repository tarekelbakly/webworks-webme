<?php

$aid=(int)$vars->sms_addressbook_id;
$r=dbRow('select name from sms_addressbooks where id='.$aid);
if(count($r)){
	$html='<div id="sms-subscribe-'.$aid.'" class="sms-subscribe">'
		.'<p>Subscribe to our '.htmlspecialchars($r['name']).' SMS list.</p>'
		.'<table>'
		.'<tr><th>Name</th><td><input class="sms-name" /></td></tr>'
		.'<tr><th>Mobile</th><td><input class="sms-phone" /></td></tr>'
		.'<tr><th colspan="2"><button>Subscribe</button></th></tr>'
		.'</table>'
		.'</div>';
	$html.='<script src="/ww.plugins/sms/frontend/widget.js"></script>';
}
else $html='<em>Missing SMS addressbook.</em>';
