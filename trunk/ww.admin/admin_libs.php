<?php
// { defines
define('ACL_PAGES',           1);
define('ACL_PANELS',          2);
define('ACL_EZINES',          4);
define('ACL_MATRICES',       32);
define('ACL_USERS',          64);
define('ACL_FORMS',         256);
define('ACL_SMS',          2048);
define('ACL_ONLINESTORES', 4096);
define('ACL_PRODUCTS',    16384);
define('ACL_ADS',         32768);
// }
function addMenuItem(&$arr,$file,$nav){
	if(ereg('>',$nav)){
		return;
		$bits=explode(' > ',$nav);
		if(!isset($arr[$bits[0]]))$arr[$bits[0]]=array();
		addMenuItem($arr[$bits[0]],$file,str_replace($bits[0].' > ','',$nav));
	}else{
		$arr[$nav]=$file;
	}
}
function admin_menu($list,$this=''){
	$arr=array();
	foreach($list as $key=>$val){
		if($val==$this)$arr[]='<a href="'.$val.'" class="thispage">'.$key.'</a>';
		else $arr[]='<a href="'.$val.'">'.$key.'</a>';
	}
	return '<div class="left-menu">'.join('',$arr).'</div>';
}
function admin_verifypage($validlist,$default,$val){
	foreach($validlist as $v)if($v==$val)return $val;
	return $default;
}
function wInput($name,$type='text',$value='',$class=''){
	switch($type){
		case 'checkbox': {
			$tmp=($value)?' checked="checked"':'';
			return '<input name="'.$name.'" type="checkbox"'.$tmp.' />';
		}
		case 'select': {
			$ret='';
			foreach($value as $key=>$val){
				$selected=($key==$class)?' selected="selected"':'';
				$ret.='<option value="'.$key.'"'.$selected.'>'.htmlspecialchars($val).'</option>';
			}
			return '<select name="'.$name.'">'.$ret.'</select>';
		}
		case 'textarea': {
			$tmp=($class!='')?' class="'.$class.'"':'';
			return '<textarea name="'.$name.'"'.$tmp.'>'.$value.'</textarea>';
		}
		default: {
			$tmp=($value!='')?' value="'.$value.'"':'';
			return '<input name="'.$name.'" id="'.$name.'" type="'.$type.'"'.$tmp.' class="'.$class.'" />';
		}
	}
}
function wFormRow($title,$input){
	echo '<tr><th>';
	if(is_array($title)){
		echo htmlspecialchars($title[0]);
	}else{
		echo htmlspecialchars($title);
	}
	echo '</th><td>';
	if(is_array($input)){
		for($i=0;$i<4;++$i)if(!isset($input[$i]))$input[$i]=null;
		echo wInput($input[0],$input[1],$input[2],$input[3]);
	}else{
		echo $input;
	}
	echo '</td></tr>';
}
function drawMenu($menuArray){
	$c='';
	foreach($menuArray as $name=>$item){
		if(is_array($item)){
			$c.='<a href="#">'.htmlspecialchars($name).'</a>';
			$c.='<ul>'.drawMenu($item).'</ul>';
		}else{
			$c.='<a href="'.$item.'">'.htmlspecialchars($name).'</a>';
		}
	}
	return $c;
}
function getAdminVar($name,$default=''){
	if(isset($GLOBALS['admin_vars'][$name]))return $GLOBALS['admin_vars'][$name];
	$r=dbRow('select varvalue from admin_vars where varname=\''.$name.'\' and admin_id='.get_userid());
	if(count($r)){
		$GLOBALS['admin_vars'][$name]=$r['varvalue'];
		return $r['varvalue'];
	}
	return $default;
}
function ckeditor($name,$value='',$fullpage=false,$css='',$height=250){
	return '<textarea style="width:100%;height:'.$height.'px" name="'.addslashes($name).'">'.htmlspecialchars($value).'</textarea><script>$(document).ready(function(){CKEDITOR.replace("'.addslashes($name).'",{filebrowserBrowseUrl:"/j/kfm/",menu:"WebME"});});</script>';
}
function sanitise_html($html) {
	$html = preg_replace('/<font([^>]*)>/', '<span\1>', $html);
	$html = preg_replace('/<([^>]*)color="([^"]*)"([^>]*)>/', '<\1style="color:\2"\3>', $html);
	$html = str_replace('</font>', '</span>', $html);
	$html = html_fixImageResizes($html);
	$html=str_replace('&quot;','"',$html);
	return $html;
}
function setAdminVar($name,$value){
	dbQuery("delete from admin_vars where varname='".$name."' and admin_id=".get_userid());
	dbQuery("insert into admin_vars (varname,varvalue,admin_id) values('".addslashes($name)."','".addslashes($value)."',".get_userid().")");
	$GLOBALS['admin_vars'][$name]=$value;
}
