<?php
if(isset($_REQUEST['action']) && $_REQUEST['action']==__('remind')){
	$email=$_REQUEST['email'];
	if(filter_var($email,FILTER_VALIDATE_EMAIL)){
		$u=dbRow("SELECT * FROM user_accounts WHERE email='$email'");
		if(count($u)){
			$passwd=Password::getNew();
			dbQuery("UPDATE user_accounts SET password=md5('$passwd') WHERE email='$email'");
			echo 'sending';
			mail($email,'['.$sitedomain.'] admin password reset','Your new password is "'.$passwd.'". Please log into the admin area and change it to something else.',"Reply-to: $email\nFrom: $email");
			echo 'sent';
		}
	}
}
?>
<html>
 <head>
  <title><?php echo __('Login'); ?></title>
  <link rel="stylesheet" type="text/css" href="/ww.admin/theme/login.css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
		<script type="text/javascript" src="/j/tabs.js"></script>
 </head>
 <body onload="document.getElementById('email').focus();tabs_init();">
 	<div id="wrapper">
	
	<div id="header"><div id="topImage"></div></div>
	
	<div id="mainContent">
	<div class="paragraph">
		<p>
		<?php echo __('To access the administrative features of your website, you will need to enter the username and password below and click "login".'); ?>
		</p>
	</div>
	<div class="tabs" style="width:400px;text-align:left;margin:0 auto">
		<div class="tabPage">
   		<h2><?php echo __('Login'); ?></h2>
	   	<form method="post" action="<?=$_SERVER['PHP_SELF'];?>">
				<table cols="3">
			   	<tr><th colspan="1"><?php echo __('email'); ?></th><td colspan="2"><input id="email" name="email" /></td></tr>
			   	<tr><th colspan="1"><?php echo __('password'); ?></th><td colspan="2"><input type="password" name="password" /></td></tr>
				<tr><th colspan="3" align="right"><input name="action" type="submit" value="<?php echo __('login'); ?>" class="login" /></th></tr>
				</table>
	   	</form>
		</div>
		<div class="tabPage">
   		<h2><?php echo __('Reminder'); ?></h2>
	   	<form method="post" action="<?=$_SERVER['PHP_SELF'];?>">
				<table cols="3">
			   	<tr><th colspan="1"><?php echo __('email'); ?></th><td colspan="2"><input id="email" type="text" name="email" /></td></tr>
					<tr><th colspan="3" align="right"><input name="action" type="submit" value="<?php echo __('remind'); ?>" class="login" /></th></tr>
				</table>
				<p><?php echo __('Use this form to create a new password for yourself.'); ?></p>
	   	</form>
		</div>
	</div>

	</div>
 </body>
</html>
