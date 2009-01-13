<?php
function textObjectsFilter($d){
	if($d=='')return '';
	$parseHL=explode(' ',getVar('search'));
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
	$d=str_replace("\n",'%LINERETURN%',$d);
	if(ereg('%TABSTART%',$d)){ # add in tab pages
		$d=str_replace('%TABSTART%','<div class="tabs"><div class="tabPage">',$d);
		if(ereg('%TABEND%',$d))$d=str_replace('%TABEND%','</div></div>',$d);
		else $d.='</div></div>';
		$d=str_replace('%TABPAGE%','</div><div class="tabPage">',$d);
	}
	$replacements=array(
		'ADS'                 => 'adsDisplay',
		'COUNTRIES'           => 'countriesDisplay',
		'DYNAMICTABLE'        => 'database_display_old',
		'DATABASE'            => 'database_display',
		'EVENTCALENDAR'       => 'eventCalendarDisplay',
		'EZINE_SUBSCRIPTION'  => 'ezineSubscriptionDisplay', // DEPRECATED
		'EZINE_SUBSCRIBE'     => 'ezineSubscribeDisplay',
		'EZINE_SUBSCRIBE_PERMISSION'=>'ezineSubscribePermissionDisplay',
		'FORM'                => 'formDisplay',
		'IMG'                 => 'imageDisplay',
		'LANGUAGE_FLAGS'      => 'languageFlagsDisplay',
		'LOGIN_BOX'           => 'loginBox',
		'SITE_SKINS'          => 'siteSkinThumbs',
		'OS_BASKET'           => 'osBasketDisplay',
    'OS_LIST_ALL_PRODUCTS'=> 'products_list',
		'OS_QUICKFIND'        => 'osQuickFindDisplay',
		'PANEL'               => 'panelDisplay',
		'SCROLLINGNEWS'       => 'scrollingNewsDisplay',
		'SCROLLINGEVENTS'     => 'scrollingEventsDisplay',
		'SMS_SUBSCRIBE'       => 'smsSubscribeDisplay',
		'GALLERY'             => 'imageGalleryDisplay'
	);
	$include_files=array(
		'ADS'                 => 'common/ads.php',
		'COUNTRIES'           => 'common/countries.php',
		'DATABASE'            => 'common/databases.php',
		'DYNAMICTABLE'        => 'common/databases.php',
		'EZINE_SUBSCRIPTION'  => 'common/ezine.subscription.php',
		'LANGUAGE_FLAGS'      => 'common/languages.php',
		'LOGIN_BOX'           => 'common/user.login.and.registration.php',
		'SMS_SUBSCRIBE'       => 'common/sms_subscribe.php',
		'EZINE_SUBSCRIBE'     => 'common/ezine_subscribe.php',
		'EZINE_SUBSCRIBE_PERMISSION'=>'common/ezine_subscribe.php',
		'SITE_SKINS'          => 'common/site.skins.php',
		'FORM'                => 'common/funcs.forms.php',
		'GALLERY'             => 'common/funcs.image.gallery.php',
		'OS_BASKET'           => 'common/online_stores.php',
    'OS_LIST_ALL_PRODUCTS'=> 'common/products.php',
		'OS_QUICKFIND'        => 'common/online_stores.php'
	);
	do{ 
		$a=$d; 
		if(preg_match('/%\([^\)]*\)%/',$d)){ 
			$b=preg_replace('/.*%\(([^)]*)\)%.*/m','\1',$d); 
			$d=str_replace('%('.$b.')%',__($b),$d,$count); 
		} 
	}while($a!=$d); 
	foreach($replacements as $code=>$function){
		$d=preg_replace('#<p>'.$code.'{([^}^<^>]*)}</p>#',$code.'{\1}',$d);
		do{
			$a=$d;
			if(ereg($code.'{[^}^<^>]*}',$d)){
				if(isset($include_files[$code]))require_once(BASEDIR.$include_files[$code]);
				$b=preg_replace('/.*'.$code.'{([^}^<^>]*)}.*/','\1',$d);
				$c=$function($b);
				$d=str_replace('%'.$code.'{'.$b.'}%',$c,$d,$count);
				if(!$count)$d=str_replace($code.'{'.$b.'}',$c,$d);
				$d=str_replace(array("\r","\n"),'%LINERETURN%',$d);
			}
		}while($a!=$d);
	}
	foreach($parseHL as $HL){
		if($HL!=''){
			$d=preg_replace('/(>(|[^<]*[^a-zA-Z]))('.addslashes($HL).')([^a-zA-Z][^<]*)/i',"$1<span class=\"hl\">$3</span>$4",$d);
		}
	}
	$d=str_replace('%LANGUAGE%',$_SESSION['webme_language'],$d);
	$d=str_replace('%SKIN%',$_SESSION['viewing_skin'],$d);
	if(isset($_SESSION['os_country']))$d=str_replace('%COUNTRY%',$_SESSION['os_country'],$d);
	$d=str_replace('%CURRENCY%',isset($_SESSION['os_currency'])?$_SESSION['os_currency']:'NO_CURRENCY_SET',$d);
	$d=str_replace('%DOMAIN%',str_replace('www.','',$_SERVER['HTTP_HOST']),$d);
	return str_replace('%LINERETURN%',"\n",$d);
}
