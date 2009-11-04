<?php
function userloginandregistrationDisplay(){
	// { variables
		$action=getVar('action');
		$c='';
		global $loggedin,$sitedomain,$DBVARS,$PAGEDATA;
	// }
	if(@$_GET['hash'] && @$_GET['email']){
		if(!isset($_GET['hash']) || !isset($_GET['email']))die('missing value in GET string');
		$r=dbRow("select * from user_accounts where email='".addslashes($_GET['email'])."' and verification_hash='".addslashes($_GET['hash'])."'");
		if(!count($r))die('that hash and email combination does not exist');
		$password=Password::getNew();
		dbQuery("update user_accounts set password=md5('$password'),verification_hash='',active=1 where email='".addslashes($_GET['email'])."' and verification_hash='".addslashes($_GET['hash'])."'");
		$c.='<h1>Thank you</h1><p>Your email address has been verified. Your login password is <strong>'.$password.'</strong>. Please take note of this password, and then log in.</p>';
	}
	if($action=='Login' || $loggedin){
		// { variables
			if($loggedin){
				$email=$_SESSION['userdata']['email'];
				$password=$_SESSION['userdata']['password'];
			}
			else{
				$email=getVar('email');
				$password=getVar('password');
			}
		// }
		$sql='select * from user_accounts where email="'.$email.'" and password=md5("'.$password.'") limit 1';
		$r=dbRow($sql);
		if($r){
			// { update session variables
				$loggedin=1;
				$r['password']=$password;
				$_SESSION['userdata']=$r;
			// }
			$n=$_SESSION['userdata']['name']==''?$_SESSION['userdata']['contactname']:$_SESSION['userdata']['name'];
			if($action=='Login'){
				$redirect_url='';
				if(isset($_POST['login_referer']) && strpos($_POST['login_referer'],'/')===0){
					$redirect_url=$_POST['login_referer'];
				}
				else if($PAGEDATA->vars['userlogin_redirect_to']){
					$p=Page::getInstance($PAGEDATA->vars['userlogin_redirect_to']);
					$redirect_url=$p->getRelativeUrl();
				}
				if($redirect_url!='')redirect($redirect_url);
			}
			return userregistration_showProfile();
		}
		else unset($_SESSION['userdata']);
	}
	if($c=='')$c=webmeParse($PAGEDATA->body);
	if($action=='Remind'){
		// { variables
			$email=getVar('email');
		// }
		$r=dbOne('select id from user_accounts where email="'.$email.'"','id');
		if($r){
			$p=Password::getNew();
			mail($email,'['.$sitedomain.'] user password changed',"Your new password:\n\n".$p,"From: noreply@$sitedomain\nReply-to: noreply@$sitedomain");
			dbQuery('update user_accounts set password=md5("'.$p.'") where email="'.$email.'"');
			$c.='<script>$(document).ready(function(){$("<strong>Please check your email for your new password.</strong>").dialog({modal:true,height:100,width:150});});</script>';
		}else{
			$c.='<script>$(document).ready(function(){$("<strong>No user account with that email address exists.</strong>").dialog({modal:true,height:100,width:150});});</script>';
		}
	}
	if(!$PAGEDATA->vars['userlogin_visibility'])$PAGEDATA->vars['userlogin_visibility']=3;
	if(!$loggedin){ // show login and registration box
		$c.='<div class="tabs"><ul>';
		// { menu
		if($PAGEDATA->vars['userlogin_visibility']&1){
			$c.='<li><a href="#userLoginBoxDisplay">Login</a></li>';
			$c.='<li><a href="#userPasswordReminder">Password reminder</a></li>';
		}
		if($PAGEDATA->vars['userlogin_visibility']&2)$c.='<li><a href="#userregistration">Register</a></li>';
		// }
		$c.='</ul>';
		// { tabs
		if($PAGEDATA->vars['userlogin_visibility']&1){
			$c.=userLoginBoxDisplay();
			$c.=userPasswordReminder();
		}
		if($PAGEDATA->vars['userlogin_visibility']&2)$c.=userregistration();
		// }
		$c.='</div>';
	}
	return $c;
}
function userLoginBoxDisplay(){
	global $PAGEDATA;
	$c='<div id="userLoginBoxDisplay"><h2>Login</h2>';
	if(getVar('action')=='Login')$c.='<em>incorrect email or password given.</em>';
	if($PAGEDATA->vars['userlogin_visibility']&2)$c.='<em>Don\'t have a user account? Please use the Register form (see tabs above)</em>';
	$c.='<form class="userLoginBox" action="'.$GLOBALS['PAGEDATA']->getRelativeUrl().'#tab=Login" method="post"><table>';
	$c.='<tr><th><label for="email">Email</label></th><td><input type="text" name="email" value="'.getVar('email').'" /></td>';
	$c.='<th><label for="password">Password</label></th><td><input type="password" name="password" /></td></tr>';
	$c.='</table><input type="submit" name="action" value="Login" />';
	$c.='</form>';
	$c.='</div>';
	return $c;
}
function userPasswordReminder(){
	$c='<div id="userPasswordReminder"><h2>Password Reminder</h2>';
	$c.='<form class="userLoginBox" action="'.$GLOBALS['PAGEDATA']->getRelativeUrl().'#tab=Password Reminder" method="post"><table>';
	$c.='<tr><th><label for="email">Email</label></th><td><input type="text" name="email" /></td></tr></table>';
	$c.='<input type="submit" name="action" value="Remind" /></form>';
	$c.='</div>';
	return $c;
}
function userregistration(){
	if(getVar('a')=='Register')return userregistration_register();
	return userregistration_form();
}
function userregistration_form($error=''){
	$formid=dbOne('SELECT value FROM site_vars WHERE name="user_form"','value');
	$extraform=$formid?formDisplayShow($formid,'',0,0):'';
	$c='<div id="userregistration"><h2>Register</h2>'.$error.'<form class="userRegistrationBox" action="'.$GLOBALS['PAGEDATA']->getRelativeUrl().'#tab=Register" method="post"><table>'
		.'<tr><th>Name</th><td><input type="text" name="name" value="'.htmlspecialchars(getVar('name')).'" /></td>'
		.'<th>Email</th><td><input type="text" name="email" value="'.htmlspecialchars(getVar('email')).'" /></td></tr></table>';
	$c.=$extraform;
	$c.='<input type="submit" name="a" value="Register" />'
		.'</form></div>';
	return $c;
}
function userregistration_register(){
	global $DBVARS;
	// { variables
		$contactname=getVar('contactname');
		$name=getVar('name');
		$email=getVar('email');
		$phone=getVar('phone');
		$usertype=getVar('usertype');
		$address1=getVar('address1');
		$address2=getVar('address2');
		$address3=getVar('address3');
		$howyouheard=getVar('howyouheard');
	// }
	if(!$name||!$email)return userregistration_form('<em>You must fill in at least your name and email.</em>');
	// { check if the email address is already registered
		$r=dbRow('select id from user_accounts where email="'.$email.'"');
		if($r && count($r))return userregistration_form('<p><em>That email is already registered.</em></p>');
	// }
	// { register the account
		$password=Password::getNew();
		$r=dbRow("SELECT * FROM site_vars WHERE name='user_discount'");
		$discount=(float)$r['value'];
		$hash=base64_encode(sha1(rand(0,65000),true));
		$sql='insert into user_accounts set name="'.$name.'", password=md5("'.$password.'"), email="'.$email.'", verification_hash="'.$hash.'", active=0';
		dbQuery($sql);
		$page=$GLOBALS['PAGEDATA'];
		$sitedomain='www.'.str_replace('www.','',$GLOBALS['sitedomain']);
		$recipientEmail=$DBVARS['recipientEmail'];
		$long_url="http://$sitedomain".$page->getRelativeUrl()."?hash=".urlencode($hash)."&email=".urlencode($email).'#Login';
		$short_url=md5($long_url);
		$lesc=addslashes($long_url);
		$sesc=urlencode($short_url);
		dbQuery("insert into short_urls values(0,now(),'$lesc','$short_url')");
		if(@$page->vars['userlogin_registration_type']=='Email-verified'){
    	mail($email,'['.$sitedomain.'] user registration',"Hello!\n\nThis message is to verify your email address, which has been used to register a user-account on the $sitedomain website.\n\nIf you did not, then please delete this email. Otherwise, please click the following URL to verify your email address with us. Thank you.\n\nhttp://$sitedomain/_s/".$sesc,"From: $recipientEmail\nReply-to: $recipientEmail");
			return '<p><strong>Thank you for registering</strong>. Please check your email for a verification URL. Once that\'s been followed, your account will be activated and a password supplied to you.</p>';
		}
		else{
			mail($recipientEmail,'['.$sitedomain.'] user registration','A new user has registered at '.$sitedomain.". Please review the following information, then log into the site admin area and activate the user.\n\nname: $name\nphone: $phone\naddress:\n$address1\n$address2\n$address3\n\nhow the user heard of the site: $howyouheard",'From: '.$recipientEmail."\nReply-to: ".$recipientEmail);
			return '<p><strong>Thank you for registering</strong>. Our admins will moderate your registration, and you will receive an email with your new password when it is activated.</p>';
		}
	// }
}
function userregistration_showProfile(){
	$ud=$_SESSION['userdata'];
	$name=$ud['name']?$ud['name']:$ud['contactname'];
	$c='<a class="logout" href="/?logout=1">log out</a><h2>User Profile: '.htmlspecialchars($name).'</h2><table>';
	$c.='</table>';
	return $c;
}
function loginBox(){
	$page=Page::getInstanceByType(3);
	if(!$page)return '<em>missing User Registration page</em>';
	global $PAGEDATA;
	if(isset($_SESSION['userdata'])){
		$c='<span class="login_info">Logged in as '.htmlspecialchars($_SESSION['userdata']['name']).'. [<a href="?logout=1">logout</a>]</span>';
	}
	else{
		$c='<form class="login_box" action="'.$page->getRelativeUrl().'" method="post"><table><tr><td><input name="email" value="Email" onclick="if(this.value==\'Email\')this.value=\'\'" /></td><td><input name="password" type="password" /></td><td><input type="submit" name="action" value="Login" /> or <a href="'.$page->getRelativeUrl().'">register</a></td></tr></table><input type="hidden" name="login_referer" value="'.$PAGEDATA->getRelativeUrl().'" /></form>';
	}
	return $c;
}
$html=userloginandregistrationDisplay().'<script>$(document).ready(function(){$(".tabs").tabs()});</script>';
