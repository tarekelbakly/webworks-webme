<?php
$plugin=array(
	'name'=>'Protected Files',
	'description'=>'Protect files by requiring either a login or an email address',
	'admin'=>array(
		'menu'=>array(
			'Misc>Protected Files'=>'index'
		)
	),
	'frontend'=>array(
		'file_hook'=>'protected_files_check'
	),
	'version'=>1
);
function protected_files_check($vars){
	$fname=$vars['requested_file'];
	$protected_files=cache_load('protected_files','all');
	if(!$protected_files){
		$protected_files=dbAll('select * from protected_files');
		cache_save('protected_files','all',$protected_files);
	}
	foreach($protected_files as $pr){
		if(strpos($fname,$pr['directory'].'/')===0){
			$email='';
			if(isset($_SESSION['protected_files_email']) && $_SESSION['protected_files_email'])$email=$_SESSION['protected_files_email'];
			else if(isset($_SESSION['userdata']) && $_SESSION['userdata'])$email=$_SESSION['userdata']['email'];
			else if(isset($_REQUEST['email']) && filter_var($_REQUEST['email'],FILTER_VALIDATE_EMAIL))$email=$_REQUEST['email'];
			if($email){
				require_once SCRIPTBASE.'ww.incs/common.php';
				$_SESSION['protected_files_email']=$email;
				webmeMail($pr['recipient_email'], $pr['recipient_email'], '['.$_SERVER['HTTP_HOST'].'] protected file downloaded', 'protected file "'.addslashes($fname).'" was downloaded by "'.addslashes($email).'"'); 
			}
			else{
				echo '<html><head><body>Please provide your email before downloading the file. Your email will not be passed to a third party.<form method="post" action="/f'.htmlspecialchars($fname).'"><input name="email" /><input type="submit" /></form></html></body>';
				exit;
			}
		}
	}
}
