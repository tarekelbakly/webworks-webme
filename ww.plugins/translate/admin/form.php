<?php
// { page to translate
$c='<tr><th>Page to translate</th><td>';
$c.='<select id="page_vars_translate_page_id" name="page_vars[translate_page_id]">';
if($vars['translate_page_id']){
	$parent=Page::getInstance($vars['translate_page_id']);
	$c.='<option value="'.$parent->id.'">'.htmlspecialchars($parent->name).'</option>';
}
else{
	$vars['translate_page_id']=0;
	$c.='<option value=""> -- choose -- </option>';
}
$c.='</select></td>';
// }
// { what language
$c.='<th>Language</th><td>';
$c.='<select id="page_vars_translate_language" name="page_vars[translate_language]">';
if($vars['translate_language']){
	$c.='<option value="'.htmlspecialchars($vars['translate_language']).'">'
		.htmlspecialchars($vars['translate_language'])
		.'</option>';
}
else{
	$vars['translate_language']=0;
	$c.='<option value="0"> -- choose -- </option>';
}
$c.='</select></td>';
// }
$c.='<td colspan="2">&nbsp;</td></tr>';
// { body
$c.='<tr><th>Body</th><td colspan="5">';
$c.=ckeditor('body',$page['body'],false);
$c.='</td></tr>';
// }
WW_addScript('/ww.plugins/translate/admin/form.js');