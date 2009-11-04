<?php
$html='<tr><th>'.__('Visibility').'</th><td>'.wInput('page_vars[userlogin_visibility]','select',array(
	'3'=>__('Login and Register forms'),
	'1'=>__('Login form'),
	'2'=>__('Register form')
),$page_vars['userlogin_visibility']).'</td>';
$html.='<th>'.__('Registration type:').'</th><td><select name="page_vars[userlogin_registration_type]"><option>Moderated</option>';
$html.='<option';
if(isset($page_vars['userlogin_registration_type']) && $page_vars['userlogin_registration_type']=='Email-verified')$html.=' selected="selected"';
$html.='>Email-verified</option>';
$html.='</select></td>';
$html.='<th>'.__('redirect on login:').'</th><td>';
$html.='<select id="page_vars_userlogin_redirect_to" name="page_vars[userlogin_redirect_to]">';
if($page_vars['userlogin_redirect_to']){
	$parent=Page::getInstance($page_vars['userlogin_redirect_to']);
	$html.='<option value="'.$parent->id.'">'.htmlspecialchars($parent->name).'</option>';
}
else{
	$page_vars['userlogin_redirect_to']=0;
	$html.='<option value="0"> -- '.__('none').' -- </option>';
}
$html.='</select>';
$html.='<script>var page_vars_userlogin_redirect_to='.$page_vars['userlogin_redirect_to'].';
$(document).ready(function(){
	$("#page_vars_userlogin_redirect_to").remoteselectoptions({
		url:"/ww.admin/pages/get_parents.php"
	});
});</script>';
$html.='</td>';
$html.='</td></tr>';
$html.='<tr><th>'.__('body').'</th><td colspan="5">';
$html.=ckeditor('body',$page['body'],false,$cssurl);
$html.='</td></tr>';
