<?php
global $online_store_currencies,$DBVARS;
if(isset($_REQUEST['online_store_currency']) && isset($online_store_currencies[$_REQUEST['online_store_currency']])){
	$DBVARS['online_store_currency']=$_REQUEST['online_store_currency'];
	config_rewrite();
}
$csym=$online_store_currencies[$DBVARS['online_store_currency']][0];
$c='<div class="tabs">';
// { orders
$c.='<div class="tabPage"><h2>Orders</h2>';
if(!isset($_SESSION['online-store']))$_SESSION['online-store']=array();
if(!isset($_SESSION['online-store']['status']))$_SESSION['online-store']['status']=1;
if(isset($_REQUEST['online-store-status']))$_SESSION['online-store']['status']=(int)$_REQUEST['online-store-status'];
$c.='<p>This list shows orders with the status: <select id="online-store-status">';
$arr=array('Unpaid','Paid','Paid and Delivered');
foreach($arr as $k=>$v){
	$c.='<option value="'.$k.'"';
	if($k==$_SESSION['online-store']['status'])$c.=' selected="selected"';
	$c.='">'.htmlspecialchars($v).'</option>';
}
$c.='</select>.</p>';
$rs=dbAll('select status,id,total,date_created from online_store_orders where status='.((int)$_SESSION['online-store']['status']).' order by date_created desc');
if(is_array($rs) && count($rs)){
	$c.='<div style="margin:0 20%"><table width="100%" class="datatable"><thead><tr><th>Date</th><th>Amount</th><th>Invoice</th><th>Checkout Form</th><th>Status</th></tr></thead><tbody>';
	foreach($rs as $r){
		$c.='<tr><td><span style="display:none">'.$r['date_created'].'</span>'.date_m2h($r['date_created']).'</td>'
			.'<td>'.$csym.$r['total'].'</td><td><a href="javascript:os_invoice('.$r['id'].')">Invoice</a></td>'
			.'<td><a href="javascript:os_form_vals('.$r['id'].')">Checkout Form</a></td>'
			.'<td><a href="javascript:os_status('.$r['id'].','.(int)$r['status'].')" id="os_status_'.$r['id'].'">'.htmlspecialchars($arr[(int)$r['status']]).'</a></td></tr>';
	}
	$c.='</tbody></table></div>';
}
else $c.='<em>No orders with this status exist.</em>';
$c.='</div>';
/*
mysql> describe online_store_orders;
+--------------+-------------+------+-----+---------+----------------+
| Field        | Type        | Null | Key | Default | Extra          |
+--------------+-------------+------+-----+---------+----------------+
| id           | int(11)     | NO   | PRI | NULL    | auto_increment | 
| form_vals    | text        | YES  |     | NULL    |                | 
| invoice      | text        | YES  |     | NULL    |                | 
| total        | float       | YES  |     | NULL    |                | 
| date_created | datetime    | YES  |     | NULL    |                | 
| items        | text        | YES  |     | NULL    |                | 
| status       | smallint(6) | YES  |     | 0       |                | 
+--------------+-------------+------+-----+---------+----------------+
7 rows in set (0.00 sec)
*/
// }
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
// { payment details
$c.='<div class="tabPage"><h2>Payment Details</h2>';
$c.='<table width="100%">';
// { paypal
$c.='<tr><th>PayPal email address</th><td><input class="email" name="page_vars[online_stores_paypal_address]"';
if(isset($vars['online_stores_paypal_address']))$c.=' value="'.htmlspecialchars($vars['online_stores_paypal_address']).'"';
$c.=' /></td></tr>';
// }
// { currency
$c.='<tr><th>Currency</th><td><select name="online_store_currency">';
foreach($online_store_currencies as $key=>$val){
	$c.= '<option value="'.$key.'"';
	if($key==$DBVARS['online_store_currency'])$c.= ' selected="selected"';
	$c.= '>'.$val[0].': '.htmlspecialchars($val[1]).'</option>';
}
$c.= '</select></td></tr>';
// }
$c.='</table></div>';
// }
$c.='</div>';
$c.='<script src="/ww.plugins/online-store/j/admin.js"></script>';

if(!file_exists(USERBASE.'ww.cache/online-store'))mkdir(USERBASE.'ww.cache/online-store');
if(file_exists(USERBASE.'ww.cache/online-store/'.$page['id']))unlink(USERBASE.'ww.cache/online-store/'.$page['id']);
file_put_contents(USERBASE.'ww.cache/online-store/'.$page['id'],$vars['online_stores_invoice']);
