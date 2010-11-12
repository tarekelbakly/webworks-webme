<?php
/*
	Webme Mailing List Plugin v0.2
	File: frontend/mailing-list.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/
function falert($text){
	return '<script>fAlert(\''.$text.'\');</script>';
}
function check_details($email,$name){
	if($name=='') return false;
	elseif(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,20})$",$email)) return false;
	else return true;
}
function add_database($email,$name){
	if($name=='__empty__')$name='not collected';
	$hash=mt_rand().mt_rand().mt_rand();
	dbQuery('insert into mailing_list values("","'.addslashes($email).'","'.$name.'","Pending","'.$hash.'")');
	return $hash;
}
function send_confirmation($email,$hash){
	$data=dbAll('select name,value from mailing_list_options');
	foreach($data as $d){
		$EMAIL[$d['name']]=$d['value'];
	}
	if($_SERVER['HTTPS']=='on')$http='https';else$http='http';
	$url = $http.'://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	$EMAIL['body']=str_replace('%link%',$url.'?mailing_list_hash='.$hash,$EMAIL['body']);
  $EMAIL['headers']='From: '.$EMAIL['from'];
  mail($email,htmlspecialchars($EMAIL['subject']),htmlspecialchars($EMAIL['body']),$EMAIL['headers']);
}
function create_form(){
		$f='';
		$fields=dbAll('select name,value from mailing_list_options');
		foreach($fields as $field){
			$FIELD[$field['name']]=$field['value'];
		}
		$f='<form id="mailing_list" method="post">'.
					'<input onfocus="if(this.value==\''.$FIELD['inp_em'].'\')this.value=\'\'" onblur="if(this.value==\'\')this.value=\''.$FIELD['inp_em'].'\'" value="'.$FIELD['inp_em'].'" id="email" type="text" name="mailing_email"/>';
		if($FIELD['col_name']==1)$f.='<input type="text" name="name" value="'.$FIELD['inp_nm'].'" id="mailing_name" onfocus="if(this.value==\''.$FIELD['inp_nm'].'\')this.value=\'\'" onblur="if(this.value==\'\')this.value=\''.$FIELD['inp_nm'].'\'"/>';
		if($FIELD['dis_sub']==1)$f.='<input type="submit" name="submit" value="'.$FIELD['inp_sub'].'" id="mailing_submit"/>';
		$f.='</form>';
		return $f;
}
function show_form(){
	WW_addScript('/ww.plugins/mailing-list/files/impromptu.jquery.min.js');
	WW_addScript('/ww.plugins/mailing-list/files/general.js');
	WW_addCSS('/ww.plugins/mailing-list/files/mailing-list.css');
	$html=create_form();
	if(isset($_GET['mailing_list_hash'])){
		$hash=$_GET['mailing_list_hash'];
		$email=dbQuery('select email from mailing_list where hash="'.$hash.'"');
		if(count($email)!=1) $html.=falert('Error. Invalid link provided');
		else{
			dbQuery('update mailing_list set status="Activated" where hash="'.$hash.'"');
			$html.=falert('Thank You, Email added to the list.');
		}
	}
	elseif(isset($_POST['submit'])){
		$email.=$_POST['mailing_email'];
		if(isset($_POST['name']))$name=$_POST['name'];
		else $name='__empty__';
		$valid=check_details($email,$name);
		if($valid==true){
			$hash=add_database($email,$name);
			send_confirmation($email,$hash);
			$html.=falert('Thank You! A confirmation email has been sent to '.$email);
		}
		else $html.=falert('Error. Invalid details.');
	}
	return $html;
}
