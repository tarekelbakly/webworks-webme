<?php
$c='<div class="tabs">';
// { form
$c.='<div class="tabPage"><h2>Form</h2>';
$c.='<p>This is the form that will be presented as the checkout.</p>';
if($page['body']=='' || $page['body']=='<h1>'.htmlspecialchars($page['name']).'</h1><p>&nbsp;</p>'){
	$page['body']=file_get_contents(dirname(__FILE__).'/body_template_sample.html');
}
$c.=ckeditor('body',$page['body']);
$c.='</div>';
// }
// { invoice details
$c.='<div class="tabPage"><h2>Invoice</h2>';
$c.='<p>This is what will be sent out to the buyer after the payment succeeds.</p>';
if(!isset($vars['online_stores_invoice']) || $vars['online_stores_invoice']==''){
	$vars['online_stores_invoice']=file_get_contents(dirname(__FILE__).'/invoice_template_sample.html');
}
$c.=ckeditor('page_vars[online_stores_invoice]',$vars['online_stores_invoice']);
$c.='</div>';
// }
// { payment types
$c.='<div class="tabPage"><h2>Payment Types</h2>';
$c.='<p>Fill in the details of those you wish to use. Leave the rest empty.</p>';
$c.='<div class="tabs">';
// { paypal
$c.='<div class="tabPage"><h2>PayPal</h2>';
$c.='<table width="100%"><th>PayPal email address</th><td><input class="email" name="page_vars[online_stores_paypal_address]"';
if(isset($vars['online_stores_paypal_address']))$c.=' value="'.htmlspecialchars($vars['online_stores_paypal_address']).'"';
$c.=' /></td></tr></table></div>';
// }
$c.='</div>';
$c.='</div>';
// }
$c.='</div>';

if(!file_exists(USERBASE.'ww.cache/online-store'))mkdir(USERBASE.'ww.cache/online-store');
if(file_exists(USERBASE.'ww.cache/online-store/'.$page['id']))unlink(USERBASE.'ww.cache/online-store/'.$page['id']);
file_put_contents(USERBASE.'ww.cache/online-store/'.$page['id'],$vars['online_stores_invoice']);
