<?php
$scripts=array();
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
function html_fixImageResizes($src){
	// checks for image resizes done with HTML parameters or inline CSS
	//   and redirects those images to pre-resized versions held elsewhere

	preg_match_all('/<img [^>]*>/im',$src,$matches);
	if(!count($matches))return $src;
	foreach($matches[0] as $match){
		$width=0;
		$height=0;
		if(preg_match('#width="[0-9]*"#i',$match) && preg_match('/height="[0-9]*"/i',$match)){
			$width=preg_replace('#.*width="([0-9]*)".*#i','\1',$match);
			$height=preg_replace('#.*height="([0-9]*)".*#i','\1',$match);
		}
		else if(preg_match('/style="[^"]*width: *[0-9]*px/i',$match) && preg_match('/style="[^"]*height: *[0-9]*px/i',$match)){
			$width=preg_replace('#.*style="[^"]*width: *([0-9]*)px.*#i','\1',$match);
			$height=preg_replace('#.*style="[^"]*height: *([0-9]*)px.*#i','\1',$match);
		}
		if(!$width || !$height)continue;
		$imgsrc=preg_replace('#.*src="([^"]*)".*#i','\1',$match);
		$dir=str_replace('/','@_@',$imgsrc);

		// get absolute address of img (naive, but will work for most cases)
		if(!preg_match('/^http/i',$imgsrc))$imgsrc=USERBASE.'/'.$imgsrc;

		list($x,$y)=getimagesize($imgsrc);
		if(!$x || !$y || ($x==$width && $y==$height))continue;

		// create address of resized image and update HTML
		$newURL=WORKURL_IMAGERESIZES.$dir.'/'.$width.'x'.$height.'.jpg';
		$newImgHTML=preg_replace('/(.*src=")[^"]*(".*)/i',"$1$newURL$2",$match);
		$src=str_replace($match,$newImgHTML,$src);

		// create cached image
		$imgdir=WORKDIR_IMAGERESIZES.$dir;
		@mkdir(WORKDIR_IMAGERESIZES);
		@mkdir($imgdir);
		$imgfile=$imgdir.'/'.$width.'x'.$height.'.jpg';
		if(file_exists($imgfile))continue;
		$str='convert "'.addslashes($imgsrc).'" -geometry '.$width.'x'.$height.' "'.$imgfile.'"';
		exec($str);
	}

	return $src;
}
function html_unfixImageResizes($src){
	// replace resized images with their originals
	$count=preg_match_all('#/f/.files/image_resizes/(@_@[^"]*)(/[^"]*)"#',$src,$matches);
	if(!$count)return $src;
	foreach($matches[1] as $key=>$match){
		$src=str_replace('/f/.files/image_resizes/'.$match.$matches[2][$key],str_replace('@_@','/',$match),$src);
	}
	return $src;
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
function WW_addCSS($url){
	global $css_urls;
	if(in_array($url,$css_urls))return;
	$css_urls[]=$url;
}
function WW_addScript($url){
	global $scripts;
	if(in_array($url,$scripts))return;
	$scripts[]=$url;
}
function WW_getCSS(){
	global $css_urls;
	if (!is_array($css_urls)) {
		return;
	}
	$url='/css/';
	foreach($css_urls as $s)$url.='|'.$s;
	return '<link rel="stylesheet" type="text/css" href="'.htmlspecialchars($url).'" />';
}
function WW_getScripts(){
	global $scripts;
	if(!count($scripts))return '';
	return '<script src="'.join('"></script><script src="',$scripts).'"></script>';
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
function ckeditor($name,$value='',$height=250){
	return '<textarea style="width:100%;height:'.$height.'px" name="'.addslashes($name).'">'.htmlspecialchars($value).'</textarea>'
		."<script>//<![CDATA[\n"
		.'$(function(){window.ckeditor_'.preg_replace('/[^a-zA-Z_]/','',$name)
		.'=CKEDITOR.replace("'
		.str_replace(array('[',']'),array('\[','\]'),addslashes($name))
		.'",{filebrowserBrowseUrl:"/j/kfm/",menu:"WebME",scayt_autoStartup:false});});'
		."//]]></script>";
}
function sanitise_html($html) {
	$html = preg_replace('/<font([^>]*)>/', '<span\1>', $html);
	$html = preg_replace('/<([^>]*)color="([^"]*)"([^>]*)>/', '<\1style="color:\2"\3>', $html);
	$html = str_replace('</font>', '</span>', $html);
	$html = preg_replace("/<p>[\s]*(<img[^>]*>)<\/p>/",'\1',$html);
	$html = html_fixImageResizes($html);
	// { clean skype crap from page
	$html = str_replace('<span class="skype_pnh_left_span" skypeaction="skype_dropdown">&nbsp;&nbsp;</span>','',$html);
	$html = str_replace('<span class="skype_pnh_dropart_flag_span" skypeaction="skype_dropdown" style="background-position: -1999px 1px ! important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>','',$html);
	$html = str_replace('<span class="skype_pnh_dropart_span" skypeaction="skype_dropdown" title="Skype actions">&nbsp;&nbsp;&nbsp;</span>','',$html);
	$html = str_replace('<span class="skype_pnh_right_span">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>','',$html);
	$html = preg_replace('#<span class="skype_pnh_print_container">([^<]*)</span>#','\1',$html);
	$html = preg_replace('#<span class="skype_pnh_text_span">([^<]*)</span>#','\1',$html);
	$html = preg_replace('#<span class="skype_pnh_mark">[^<]*</span>#','',$html);
	$html = preg_replace('#<span class="skype_pnh_textarea_span">([^<]*)</span>#','\1',$html);
	$html = preg_replace('#<span class="skype_pnh_highlighting_inactive_common" dir="ltr"[^>]*>([^<]*)</span>#','\1',$html);
	$html = preg_replace('#<span class="skype_pnh_container"[^>]*>([^<]*)</span>#','\1',$html);
	$html = preg_replace('#<span class="skype_pnh_text_span">([^<]*)</span>#','\1',$html);
	$html = preg_replace('#<span class="skype_pnh_print_container">([^<]*)</span>#','\1',$html);
	// }
	$html=str_replace('&quot;','"',$html);
	return $html;
}
