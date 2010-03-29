<?php
/*
	Webme Mailing List Plugin v0.2
	File: admin/form_options.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/

if(isset($_POST)) require_once('actions.save.php');

$options=dbAll('select name,value from mailing_list_options');
foreach($options as $option){
	$OPT[$option['name']]=$option['value'];
}

if($FIELD['use_js']==1)
echo '
<script type="text/javascript">
$(document).ready(form_valid);
function form_valid(){
	$("#form").submit(function{confirm("test")});
} 
function delete_row(){
	var id=this.href.replace(/.*!/,"");
	if(confirm("Are you sure you want to delete this email from the list?")){
		$.getJSON("/ww.admin/plugin.php?_plugin=mailing-list&mailing_list=delete&_page=index&id="+id);
		$(this).parent().parent().fadeOut("slow",function(){
			$(this).remove();
		});
	}
	return false;
}
</script>
';

echo 	'<h3>Form Options</h3>';

if(isset($updated)) echo '<em>'.$updated.'</em>';

echo '<div class="tabs">';

$sub_val = ($OPT['dis_sub']==1)?'checked':'';
$name_val = ($OPT['col_name']==1)?'checked':'';

echo '
<div class="tabPage"><h2>Frontend</h2>
	<form method="post">
		<table>
			<tr><td>Display submit button:</td><td><input type="checkbox" value="1" name="dis_sub"'.$sub_val.'/></td></tr>
			<tr><td>Submit input:</td><td><input type="text" value="'.$OPT['inp_sub'].'" name="inp_sub"/></td></tr>
			<tr><td>Email input:</td><td><input type="text" value="'.$OPT['inp_em'].'" name="inp_em"/></td></tr>
			<tr><td>Collect name:</td><td><input type="checkbox" value="1" name="col_name"'.$name_val.'/></td></tr>
			<tr><td>Name input:</td><td><input type="text" value="'.$OPT['inp_nm'].'" name="inp_nm"/></td></tr>
			<tr><td colspan="2"><input type="submit" name="front_sub" value="Save"/></td></tr>
		</table>
	</form>
</div>
';

$pend_val = ($OPT['dis_pend']==1)?'checked':'';
$bcc_val = ($OPT['use_bcc']==1)?'checked':'';
$js_val = ($OPT['use_js']==1)?'checked':'';

echo '
<div class="tabPage"><h2>Admin</h2>
	<form method="post">
		<table>
			<tr><td>Display pending emails:</td><td><input type="checkbox" value="1" name="dis_pend"'.$pend_val.'/></td></tr>
			<tr><td>Use BCC for sending emails:</td><td><input type="checkbox" value="1" name="use_bcc"'.$bcc_val.'/></td></tr>
			<tr><td>Use unobstructive jQuery:</td><td><input type="checkbox" value="1" name="use_js"'.$js_val.'/></td></tr>
			<tr><td>Send emails from:</td><td><input type="text" name="email" value="'.$OPT['email'].'"/></td></tr>
			<tr><td colspan="2"><input type="submit" name="admin_sub" value="Save"/></td></tr>
		</table>
	</form>
</div>
';

echo '
<div class="tabPage"><h2>Verification Email Layout</h2>
	<form method="post" id="form">
		<table>
			<tr><td>From:</td><td><input type="text" name="from" value="'.$OPT['from'].'"/></td><td>Email address to send from.</td></tr>
			<tr><td>Subject:</td><td><input type="text" name="subject" value="'.$OPT['subject'].'"/></td><td>Subject of verification email.</td></tr>
			<tr><td>Body:</td><td><textarea name="body">'.htmlspecialchars($OPT['body']).'</textarea></td><td>Body of verification email. You must put %link% in where you would like the activation link to appear. Also please use \n to indicate a line break.</td></tr>
			<tr><td colspan="2"><input type="submit" id="save" value="Save Details" name="vermail_save"/></td><td><input type="submit" value="Restore Defaults" name="vermail_restore" id="restore" /></td></tr>
		</table>
	</form>
</div>
';

echo '</div>';

?>
