<?php
require $_SERVER['DOCUMENT_ROOT'].'/ww.incs/basics.php';
if(!is_admin())die('access denied');

if(isset($_REQUEST['sms_addressbook_id']))$sms_addressbook_id=$_REQUEST['sms_addressbook_id'];
else $sms_addressbook_id='';

// { Addressbook
echo '<strong>Addressbook</strong><br />';
$cs=dbAll('select id,name from sms_addressbooks order by name');
if(!count($cs)){
	echo 'no sms_addressbook_ids created. please <a href="/ww.admin/plugin.php?_plugin=sms&_page=sms_addressbook_ids">create one</a> first.';
	exit;
}
else{
	echo '<select name="sms_addressbook_id"><option value=""> -- choose -- </option>';
	foreach($cs as $v){
		echo '<option value="'.$v['id'].'"';
		if($sms_addressbook_id==$v['id'])echo ' selected="selected"';
		echo '>'.htmlspecialchars($v['name']).'</option>';
	}
	echo '</select><br />';
}
// }
