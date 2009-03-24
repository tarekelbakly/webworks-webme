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
function admin_menu($list){
	$arr=array();
	foreach($list as $key=>$val)$arr[]='<a href="'.$val.'">'.$key.'</a>';
	return '<div id="leftmenu">'.join('',$arr).'</div>';
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
function fckeditor($name,$value='',$fullpage=false,$css=''){
	$oFCKeditor = new FCKeditor($name);
	$oFCKeditor->BasePath = '/j/'.FCKEDITOR.'/';
	$oFCKeditor->Value                   = $value;
	$oFCKeditor->Height                  = 250;
	$oFCKeditor->Config['FullPage']      = $fullpage;
	if($css)$oFCKeditor->Config['EditorAreaCSS'] = $css;
	$oFCKeditor->Create();
}
function fckeditor_cleanup($input){
	$input=str_replace(
		array('<p>%TABSTART%</p>','<p>%TABPAGE%</p>','<p>%TABEND%</p>'),
		array('%TABSTART%','%TABPAGE%','%TABEND%'),
		$input
	);
	return $input;
}
function fckeditor_generateCSS($pageid){
	$page=Page::getInstance($pageid);
	$cssurl='';
	if(isset($page->template) && file_exists($page->template)){
		@mkdir($_SERVER['DOCUMENT_ROOT'].'/f/.files/fckeditorcss');
		$cssurl='/f/.files/fckeditorcss/'.md5($page->template).'.css';
		$cssfile=$_SERVER['DOCUMENT_ROOT'].$cssurl;
		if(!file_exists($cssfile) || filectime($page->template)>filectime($cssfile)){
			$file=str_replace(array("\n","\r"),' ',join('',file($page->template)));
			$file=$_SERVER['DOCUMENT_ROOT'].'/'.preg_replace('/.*(ww.skins[^"]*\.css)".*/','$1',$file);
			$file=str_replace(array("\n","\r"),' ',join('',file($file)));
			// { create the 'body' selector
			$file=preg_replace('/[^}]*(#content|#wrapper)\s*/','html body',$file);
			$bodies=array();
			preg_match_all('/html body{[^}]*}/',$file,$bodies);
			$file=preg_replace('/html body{[^}]*}/','',$file);
			$rules=array('margin'=>'0 !important','padding'=>'0 !important','background-image'=>'none !important');
			foreach($bodies[0] as $group){
				$group=preg_replace('/.*{(.*)}/','$1',$group);
				$lrules=explode(';',$group);
				foreach($lrules as $rule){
					$bits=explode(':',$rule);
					$name=trim($bits[0]);
					if(!$name)continue;
					$value=trim($bits[1]);
					$valid=1;
					switch($name){
						case '':case 'padding':case 'padding-left':case 'padding-top':case 'padding-bottom':case 'padding-right':
						case 'border':case 'border-left':case 'border-top':case 'border-bottom':case 'border-right':case 'background':
						case 'margin':case 'margin-left':case 'margin-top':case 'margin-bottom':case 'margin-right': // {
							$valid=0;
							break;
						// }
						case 'width': case 'height': // {
							if(isset($rules[$name])){
								if(preg_match('/%/',$value) || (!preg_match('/%/',$rules[$name]) && (int)$rules[$name]<(int)$value))$value=$rules[$name];
							}
						// }
					}
					if($valid){
						$rules[$name]=$value;
					}
				}
			}
			$file.='html body{';
			foreach($rules as $name=>$value)$file.=$name.':'.$value.';';
			$file.='}';
			// }
			$file=str_replace('}',"}\n",$file);
			file_put_contents($cssfile,$file);
		}
	}
	return $cssurl;
}
function setAdminVar($name,$value){
	dbQuery("delete from admin_vars where varname='".$name."' and admin_id=".get_userid());
	dbQuery("insert into admin_vars (varname,varvalue,admin_id) values('".addslashes($name)."','".addslashes($value)."',".get_userid().")");
	$GLOBALS['admin_vars'][$name]=$value;
}
