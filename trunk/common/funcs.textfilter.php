<?php
function textObjectsFilter($d){
	if($d=='')return '';
	$parseHL=explode(' ',str_replace('/',' ',getVar('search')));
	# get search terms to highlight
	if(!isset($_SERVER['HTTP_REFERER']))$_SERVER['HTTP_REFERER']='';
	if(ereg('google',$_SERVER['HTTP_REFERER'])){
		$parts=explode('&',preg_replace('/.*\?/','',$_SERVER['HTTP_REFERER']));
		foreach($parts as $b){
			if($b[0]='q'){
				$parts2=explode('=',$b);
				if($parts2[0]=='q')$parseHL=explode(' ',str_replace('"','',$parts2[1]));
			}
		}
	}
	if(count($parseHL)){
		$c=$d;
		foreach($parseHL as $HL){
			if($HL!=''){
				$d=preg_replace('/(>(|[^<]*[^a-zA-Z]))('.addslashes($HL).')([^a-zA-Z][^<]*)/im','$1<span class="hl">$3</span>$4',$d);
			}
		}
		if($c!=$d)$d=preg_replace('/(<[^>]*)<[^>]*>([^<]*)<[^>]*>/','$1$2',$d);
	}
	return $d;
}
