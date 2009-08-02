<?php
require('../common.php');
function comments_delete($id){
    if(!is_admin())exit('permission denied!');
    dbRow('delete from comments where id='.$id);
    return comments_getAll();
}
function comments_getAll($lastId=0){
    return dbAll("select id,name,md5(email) as email,homepage,comment,cdate from comments where objectid=".getVar('pageid')." and id>".$lastId." and isvalid order by cdate");
}
function comments_submit($name='',$email='',$homepage='',$comment='',$password='',$lastId=0){
    global $DBVARS,$sitedomain;
    $pageid=getVar('pageid');
    $PAGEDATA=Page::getInstance($pageid);
    if(!$PAGEDATA)return 'error: page doesn\'t exist?';
    if($comment=='')return 'error: nothing to say?';
    if($homepage=='http://')$homepage='';
    if(!isset($_SESSION['comment_password'])||!isset($_SESSION['comment_password'][$pageid])||$_SESSION['comment_password'][$pageid]==''||$password!=$_SESSION['comment_password'][$pageid])return 'error: session password not set or does not match comment password';
    $r=dbAll("select id from comments where objectid=".$pageid." and email='".addslashes($email)."' and cdate>now()-interval 15 second");
    if(count($r))return 'error: please wait at least 15 seconds between posts';
    $app='';
    $trusted=0;
    if($_SESSION['email_verified'] || is_admin())$trusted=1;
    else if($_COOKIE['comment_verification']){
    	list($email,$hash)=explode('|',$_COOKIE['comment_verification']);
    	$r=dbRow("select id from comments where email='".addslashes($email)."' and verificationhash='".addslashes($hash)."'");
    	if(count($r)){
        $_SESSION['email_verified']=1;
        $trusted=1;
    	}
    	else setcookie('comment_verification','',time()-1000);
    }
    if($trusted){
    	$app.=',isvalid=1';
    }
    else{
    	$hash=base64_encode(sha1(rand(0,65000),true));
    	$app.=',verificationhash="'.$hash.'",isvalid=0';
    	mail($email,'['.$sitedomain.'] comment verification',"This email is to verify that you, or someone claiming to be you, posted a comment on the $sitedomain website.\n\nIf you did not, then please delete this email. Otherwise, please click the following URL to verify your email address with us. Thank you.\n\nhttp://$sitedomain/common/comment_verification.php?hash=".urlencode($hash)."&email=".urlencode($email),"From: ".$DBVARS['recipientEmail']);
    }
    dbQuery("insert into comments set objectid=".$pageid.",name='".addslashes($name)."',email='".addslashes($email)."',homepage='".addslashes($homepage)."',comment='".htmlspecialchars($comment,ENT_QUOTES)."',cdate=now()$app");
    mail($DBVARS['recipientEmail'],'['.$sitedomain.'] comment on "'.$PAGEDATA->name.'"',
        'name:     '.$name."\n".
        'email:    '.$email."\n".
        'homepage: '.$homepage."\n".
        "comment:\n".$comment."\n\n".
        'http://'.$sitedomain.$PAGEDATA->getRelativeURL(),
        'From: "'.$name.'" <'.$email.">\nReply-to: \"".$name.'" <'.$email.'>');
    return array(comments_getAll($lastId),$trusted);
}
require('../common/kaejax.php');
kaejax_export('comments_delete','comments_getAll','comments_submit');
kaejax_handle_client_request();
kaejax_show_javascript();
$pageid=getVar('pageid');
if(!isset($_SESSION['comment_password']))$_SESSION['comment_password']=array();
if(isset($_SESSION['comment_password'][$pageid]))$pass=$_SESSION['comment_password'][$pageid];
else{
    $pass=Password::getNew();
    $_SESSION['comment_password'][$pageid]=$pass;
}
echo 'var comment_password="'.$pass.'",pageid='.$pageid.';comment_lastId=0;';
echo file_get_contents('comments.js');
