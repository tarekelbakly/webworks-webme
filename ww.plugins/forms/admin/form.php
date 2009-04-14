<?php
if(!$edit && $replytoid)$c.= wInput('replytoid','hidden',$replytoid);
$c.= '<div class="tabs">';
// { header
$c.='<div class="tabPage"><h2>Header</h2><p>Text to be shown above the form</p>';
$c.=fckeditor('body',$page['body'],0,$cssurl);
$c.='</div>';
// }
// { main details
$c.= '<div class="tabPage"><h2>Main Details</h2><table>';
// { send as email, recipient
$c.= '<tr><th>'.__('Send as Email').'</th><td>'.wInput('page_vars[forms_send_as_email]','select',array('1'=>'Yes','0'=>'No'),@$vars['forms_send_as_email']).'</td>';
$c.= '<th>'.__('Recipient').'</th><td>'.wInput('page_vars[forms_recipient]','',htmlspecialchars(@$vars['forms_recipient'])).'</td></tr>';
// }
// { save in database, reply-to
$c.= '<tr><th>'.__('Save in Database').'</th><td>'.wInput('page_vars[forms_save_in_database]','select',array('0'=>'No','1'=>'Yes'),@$vars['forms_save_in_database']);
if(@$vars['forms_save_in_database'])$c.= ' <a href="forms.saved.php?forms_id='.$id.'">view saved data</a>';
$c.= '</td>';
$c.= '<th>Reply-To</th><td>'.wInput('page_vars[forms_replyto]','',htmlspecialchars(@$vars['forms_replyto'])).'</td></tr>';
// }
// { captcha required
$c.= '<tr><th>Captcha Required</th><td colspan="3">'.wInput('page_vars[forms_captcha_required]','select',array('1'=>'Yes','0'=>'No'),@$vars['forms_captcha_required']);
$c.= '&lt;-- this is for spam prevention. We recommend you leave this as "Yes".</td></tr>';
// }
$c.= '</table></div>';
// }
// { form fields
$c.= '<div class="tabPage"><h2>Form Fields</h2>';
$c.= '<table id="formfieldsTable"><tr><th>Name</th><th>Type</th><th>Required</th><th id="extrasColumn"><a href="javascript:formfieldsAddRow()">add field</a></th></tr>';
if($edit){
$q2=dbAll('select * from forms_fields where formsId="'.$id.'" order by id');
$i=0;
$arr=array('email'=>__('email'),'input box'=>__('input box'),'textarea'=>__('textarea'),'date'=>__('date'),
'checkbox'=>__('checkbox'),'selectbox'=>__('selectbox'),'hidden'=>__('hidden message'),'ccdate'=>__('credit card expiry date'));
foreach($q2 as $r2){
$c.= '<tr><td>'.wInput('formfieldElementsName['.$i.']','',htmlspecialchars($r2['name'])).'</td><td>'
.wInput('formfieldElementsType['.$i.']','select',$arr,$r2['type']).'</td><td>'
.wInput('formfieldElementsIsRequired['.($i).']','checkbox',$r2['isrequired']).'</td><td>';
switch($r2['type']){
case 'selectbox':case 'hidden':{
$c.= wInput('formfieldElementsExtra['.($i++).']','textarea',$r2['extra'],'small');
break;
}
default:{
$c.= wInput('formfieldElementsExtra['.($i++).']','hidden',$r2['extra']);
}
}
$c.= '</td></tr>';
}
}
$c.= '</table></div>';
// }
// { success message
$c.= '<div class="tabPage"><h2>Success Message</h2>';
$c.= '<p>What should be displayed on-screen when the message is sent.</p>';
$c.= fckeditor('page_vars[forms_successmsg]',@$vars['forms_successmsg']);
$c.= '</div>';
// }
// { template
$c.= '<div class="tabPage"><h2>Template</h2>';
$c.= '<p>Leave blank to have an auto-generated template displayed.</p>';
$c.= fckeditor('page_vars[forms_template]',@$vars['forms_template']);
$c.= '</div>';
// }
$c.= '</div>';
if($edit)$c.= '<script type="text/javascript">var formfieldElements='.$i.';</script>';
$c.='<script type="text/javascript" src="/ww.plugins/forms/j/admin.fields.js"></script>';
