<?php
function form_show($page,$vars){
	return(getVar('funcFormInput')=='submit')?formDisplaySend($page,$vars):formDisplayShow($page,$vars);
}
function formDisplaySend($page,$vars){
	global $recipientEmail;
	$c=''; $err=''; $msg=''; $plaintext='';
	$values=array();
	$q2=dbAll('select * from forms_fields where formsId="'.$page['id'].'" order by id');
	foreach($q2 as $r2){
		$name=preg_replace('/[^a-zA-Z0-9_]/','',$r2['name']);
		$separator="\n------------------------------------------------------------------------------\n";
		$val='';
		switch($r2['type']){
			case 'checkbox':{
				$val=getVar($name);
				$values[$r2['name']]=($val=='on')?'yes':'no';
				if($val=='on')$plaintext.='selected option: '.htmlspecialchars($r2['name']).$separator;
				break;
			}
			case 'date':case 'ccdate':{
				$val=date_m2h(selectDate($name,'get'));
				if($r2['type']=='ccdate')$val=preg_replace('#.* ([a-zA-Z]*, [0-9]+)#',"$1",$val);
				$values[$r2['name']]=$val;
				$plaintext.=htmlspecialchars($r2['name'])."\n".htmlspecialchars($val).$separator;
				break;
			}
			default:{
				$val=getVar($name);
				$values[$r2['name']]=$val;
				$val=nl2br($val);
				$plaintext.=htmlspecialchars($r2['name'])."\n".htmlspecialchars($val).$separator;
			}
		}
		if($r2['isrequired']&&$val==''){
			$err.='<em>'.__('You must fill in the <strong>%1</strong> field.',__($r2['name'])).'</em><br />';
		}
		if($r2['type']=='email' && !filter_var($val, FILTER_VALIDATE_EMAIL)){
			$err.='<em>'.__('You must provide a valid email address in the <strong>%1</strong> field.',__($r2['name'])).'</em><br />';
		}
	}
	if($vars['forms_captcha_required']){
		if(!isset($_SESSION['security_code'])||getVar('captcha')!=$_SESSION['security_code']){
			$err.='<em>'.__('You must fill in the captcha (image text).').'</em>';
		}
	}
	$form=formDisplayShow($page,$vars,$err);
	if($err!='')$c.=formDisplayShow($page,$vars,$err);
	else{
		if($vars['forms_send_as_email']){
			$form=formDisplayShow($page,$vars,$err,true);
			$from=preg_replace('/^FIELD{|}$/','',$vars['forms_replyto']);
			if($vars['forms_replyto']!=$from)$from=$_REQUEST[preg_replace('/[^a-zA-Z]/','',$from)];
			$to=preg_replace('/^FIELD{|}$/','',$vars['forms_recipient']);
			if($vars['forms_recipient']!=$to)$to=$_REQUEST[preg_replace('/[^a-zA-Z]/','',$to)];
			$form=str_replace(array(
				'<input type="submit" value="Submit Form" />',
				'<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="ww_form" enctype="multipart/form-data">',
				'</form>'
			),'',$form);
			webmeMail($to,$from,$page['name'],'<html><head></head><body>'.$form.'</body></html>',$_FILES);
		}
		if($vars['forms_save_in_database'])form_saveValues($vars['forms_id'],$values);
		$c.='<div id="thankyoumessage">'.$vars['forms_successmsg'].'</div>';
	}
	return $c;
}
function formDisplayShow($page,$vars,$err='',$only_show_contents=false,$show_submit=1){
	global $plugins_to_load;
	if(!isset($_SESSION['forms']))$_SESSION['forms']=array();
	$c='';
	if(!$only_show_contents && $show_submit){
		$c.='<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="ww_form formvalidation" enctype="multipart/form-data">';
	}
	else if(!$only_show_contents)$plugins_to_load[]='"formvalidation":1';
	$c.='<fieldset>';
	if($err)$c.='<div class="errorbox">'.$err.'</div>';
	if(!$vars['forms_template']||$vars['forms_template']=='&nbsp;')$c.='<table>';
	$required=array();
	$q2=dbAll('select * from forms_fields where formsId="'.$page['id'].'" order by id');
	$cnt=0;
	foreach($q2 as $r2){
		if($r2['type']=='hidden' && !$only_show_contents)continue;
		$name=preg_replace('/[^a-zA-Z0-9_]/','',$r2['name']);
		$class='';
		if($r2['isrequired']){
			$required[]=$name.','.$r2['type'];
			$class=' required';
		}
		if(isset($_REQUEST[$name]))$_SESSION['forms'][$name]=$_REQUEST[$name];
		$val=getVar($name);
		if(!$val && isset($_SESSION['userdata']) && $_SESSION['userdata']){
			switch($name){
				case 'Email': case '__ezine_subscribe': // {
					$val=$_SESSION['userdata']['email'];
					break;
				// }
				case 'FirstName': // {
					$val=preg_replace('/ .*/','',$_SESSION['userdata']['name']);
					break;
				// }
				case 'Street': // {
					$val=$_SESSION['userdata']['address1'];
					break;
				// }
				case 'Street2': // {
					$val=$_SESSION['userdata']['address2'];
					break;
				// }
				case 'Surname': // {
					$val=preg_replace('/.* /','',$_SESSION['userdata']['name']);
					break;
				// }
				case 'Town': // {
					$val=$_SESSION['userdata']['address3'];
					break;
				// }
			}
		}
		switch($r2['type']){
			case 'checkbox': {
				if($only_show_contents)$d=$_REQUEST[$name];
				else{
					$d='<input type="checkbox" id="'.$name.'" name="'.$name.'"';
					if($_REQUEST[$name])$d.=' checked="'.$_REQUEST[$name].'"';
					$d.=' class="'.$class.' checkbox" />';
				}
				break;
			}
			case 'ccdate': {
				$d=$only_show_contents?selectdate($name,'get',0):selectdate($name,'ccdate',selectdate($name,'get'));
				break;
			}
			case 'date': {
				$d=$only_show_contents?date_m2h(selectdate($name,'get')):selectdate($name,'date',selectdate($name,'get'));
				break;
			}
			case 'email':{
				$d=$only_show_contents?$_REQUEST[$name]:'<input id="'.$name.'" name="'.$name.'" value="'.$val.'" class="email'.$class.' text" />';
				break;
			}
			case 'file': {
				$d=$only_show_contents?'<i>files attached</i>':'<input id="'.$name.'" name="'.$name.'" type="file" />';
				break;
			}
			case 'hidden': {
				$d=$only_show_contents?htmlspecialchars($r2['extra']):'<textarea id="'.$name.'" name="'.$name.'" class="'.$class.' hidden">'.htmlspecialchars($r2['extra']).'</textarea>';
				break;
			}
			case 'selectbox': {
				if($only_show_contents)$d=$_REQUEST[$name];
				else{
					$d='<select id="'.$name.'" name="'.$name.'">';
					$arr=explode("\n",htmlspecialchars($r2['extra']));
					foreach($arr as $li){
						if($_REQUEST[$name]==$li)$d.='<option selected="selected">'.rtrim($li).'</option>';
						else $d.='<option>'.rtrim($li).'</option>';
					}
					$d.='</select>';
				}
				break;
			}
			case 'textarea': {
				$d=$only_show_contents?$_REQUEST[$name]:'<textarea id="'.$name.'" name="'.$name.'" class="'.$class.'">'.$_REQUEST[$name].'</textarea>';
				break;
			}
			default:{ # input boxes, and anything which was not handled already
				$d=$only_show_contents?$_REQUEST[$name]:'<input id="'.$name.'" name="'.$name.'" value="'.$val.'" class="'.$class.' text" />';
				break;
			}
		}
		if($vars['forms_template']&&$vars['forms_template']!='&nbsp;'){
			$vars['forms_template']=str_replace('%'.$cnt.'%',$d,$vars['forms_template']);
			$vars['forms_template']=str_replace('%'.htmlspecialchars($r2['name']).'%',$d,$vars['forms_template']);
		}
		else{
			$c.='<tr><th>'.htmlspecialchars(__($r2['name']));
			if($r2['isrequired'])$c.='<sup>*</sup>';
			$c.="</th>\n\t<td>".$d."</td></tr>\n\n";
		}
		$cnt++;
	}
	if($vars['forms_template'])$vars['forms_template']=webmeParse($vars['forms_template']);
	if($vars['forms_captcha_required'] && !$only_show_contents){
		$row='<tr><td><script type="text/javascript">document.write("<img"+" src=\"/p/cap"+"tcha.php\" />");</script></td><td>'.__('Please type the word you see on the left.').'<br /><input name="captcha" /></td></tr>';
		if($vars['forms_template'])$vars['forms_template'].='<table>'.$row.'</table>';
		else $c.=$row;
	}
	if($vars['forms_template']&&$vars['forms_template']!='&nbsp;')$c.=$vars['forms_template'];
	else $c.='<tr><th colspan="2" class="submitrow">';
	if($only_show_contents)return $c.'</fieldset>';
	if($show_submit)$c.='<input type="submit" />'
		.'<input type="hidden" name="funcFormInput" value="submit" />'
		.'<input type="hidden" name="requiredFields" value="'.join(',',$required).'" />';
	if(count($required))$c.='<br />'.__('* indicates required fields');
	if(!$vars['forms_template']||$vars['forms_template']=='&nbsp;')$c.='</th></tr></table>';
	$c.='</fieldset>';
	if(!$only_show_contents && $show_submit)$c.='</form>';
	return $c;
}
function form_saveValues($formid,$values){
	dbQuery("insert into forms_saved (forms_id,date_created) values($formid,now())");
	$id=dbOne('select last_insert_id() as id','id');
	foreach($values as $key=>$val)dbQuery("insert into forms_saved_values (forms_saved_id,name,value) values(".$id.",'".addslashes($key)."','".addslashes($val)."')");
}
function form_getErrors($form_id){
	$r=dbRow('select * from forms where id="'.$form_id.'"');
	$err='';
	$q2=dbAll('select * from forms_fields where formsId="'.$form_id.'" order by id');
	foreach($q2 as $r2){
		$name=preg_replace('/[^a-zA-Z0-9_]/','',$r2['name']);
		$separator="\n------------------------------------------------------------------------------\n";
		$val='';
		switch($r2['type']){
			case 'checkbox':{
				$val=getVar($name);
				break;
			}
			case 'date':case 'ccdate':{
				$val=date_m2h(selectDate($name,'get'));
				if($r2['type']=='ccdate')$val=preg_replace('#.* ([a-zA-Z]*, [0-9]+)#',"$1",$val);
				break;
			}
			default:{
				$val=getVar($name);
				$val=nl2br($val);
			}
		}
		if($r2['isrequired']&&$val==''){
			$err.='<em>'.__('You must fill in the <strong>%1</strong> field.',__($r2['name'])).'</em><br />';
		}
	}
	if($r['captcha_required']){
		if(!isset($_SESSION['security_code'])||getVar('captcha')!=$_SESSION['security_code']){
			$err.='<em>'.__('You must fill in the captcha (image text).').'</em>';
		}
	}
	return $err;
}
function form_getValues($formid){
	$values=array();
	$q2=dbAll("select * from forms_fields where formsId=$formid order by id");
	foreach($q2 as $r2){
		$name=preg_replace('/[^a-zA-Z0-9_]/','',$r2['name']);
		$val='';
		switch($r2['type']){
			case 'checkbox':{
				$val=getVar($name);
				$values[$r2['name']]=($val=='on')?'yes':'no';
				break;
			}
			case 'date':case 'ccdate':{
				$val=date_m2h(selectDate($name,'get'));
				if($r2['type']=='ccdate')$val=preg_replace('#.* ([a-zA-Z]*, [0-9]+)#',"$1",$val);
				$values[$r2['name']]=$val;
				break;
			}
			default:{
				$val=getVar($name);
				$values[$r2['name']]=$val;
			}
		}
	}
	return $values;
}
