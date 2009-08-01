<?php
function template_get_metadata($template,$PAGEDATA){
	global $DBVARS;
	$title=($PAGEDATA->title!='')?$PAGEDATA->title:str_replace('www.','',$_SERVER['HTTP_HOST']).' > '.$PAGEDATA->name;
	$c='<title>'.htmlspecialchars($title).'</title>';
	// { generate plugins list for those that were not already figured out (this should be optimised as soon as possible)
	if(strpos($template,'class="tabs"')!==false)$GLOBALS['plugins_to_load'][]='"tabs":1';
	if(strpos($template,'class="showhide"')!==false)$GLOBALS['plugins_to_load'][]='"showhide":1';
	if(strpos($template,'class="fontsize_controls"')!==false)$GLOBALS['plugins_to_load'][]='"fontsize_controls":1';
	if(strpos($template,'class="imagefader"')!==false)$GLOBALS['plugins_to_load'][]='"imagefader":1';
	if(strpos($template,'class="inputdate"')!==false)$GLOBALS['plugins_to_load'][]='"inputdate":1';
	if(strpos($template,'class="removeRowIfEmpty"')!==false)$GLOBALS['plugins_to_load'][]='"removeRowIfEmpty":1';
	if(strpos($template,'class="sc_ssearch"')!==false)$GLOBALS['plugins_to_load'][]='"sc_ssearch":1';
	if(strpos($template,'class="scrollingEvents"')!==false)$GLOBALS['plugins_to_load'][]='"scrollingEvents":1';
	if(strpos($template,'class="scrollingNews"')!==false)$GLOBALS['plugins_to_load'][]='"scrollingNews":1';
	// }
	$dir=SCRIPTBASE.'j';
	$modified=md5(`ls -l $dir`);
	$c.='<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"></script>';
	$c.='<script src="/js/'.$modified.'"></script>';
	$c.='<script src="/js_lang/'.$_SESSION['webme_language'].'/'.$modified.'"></script>';
	$c.='<script>var pagedata={id:'.$PAGEDATA->id.',url:"'.$PAGEDATA->getRelativeURL().'",country:"'.(isset($_SESSION['os_country'])?$_SESSION['os_country']:'').'"},';
	$c.='userdata={isAdmin:'.(is_admin()?1:0);
	if(isset($_SESSION['userdata']) && isset($_SESSION['userdata']['discount']))$c.=',discount:'.(int)$_SESSION['userdata']['discount'];
	$c.='},';
	$c.='plugins_to_load={'.join(',',$GLOBALS['plugins_to_load']).'};';
	$c.='document.write("<"+"style type=\'text/css\'>a.nojs{display:none !important}<"+"/style>");';
	$c.='</script>';
	$c.='<style type="text/css">@import "/css";</style>';
	$c.='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	if($PAGEDATA->keywords)$c.='<meta http-equiv="keywords" content="'.$PAGEDATA->keywords.'" />';
	if($PAGEDATA->description)$c.='<meta http-equiv="description" content="'.$PAGEDATA->description.'" />';
	if($DBVARS['theme_variant'])$c.='<style type="text/css">@import "/ww.skins/'.$DBVARS['theme'].'/cs/'.$DBVARS['theme_variant'].'.css";</style>';
	$c.='<style type="text/css">.loggedin{display:'.(is_logged_in()?'block':'none').'} .loggedinCell{display:'.(is_logged_in()?'table-cell':'none').'}</style>';
	return $c;
}
function logoDisplay($vars){
	$vars=array_merge(array('width'=>64,'height'=>64),$vars);
	if(!file_exists(USERBASE.'/f/skin_files/logo.png'))return '';
	$x=(int)$vars['width'];
	$y=(int)$vars['height'];
	$geometry=$x.'x'.$y;
	$image_file=USERBASE.'/f/skin_files/logo-'.$geometry.'.png';
	if(!file_exists($image_file)){
		$from=addslashes(USERBASE.'/f/skin_files/logo.png');
		$to=addslashes($image_file);
		`convert $from -geometry $geometry $to`;
	}
	return '<img src="/f/skin_files/logo-'.$geometry.'.png" />';
}
function show_page($template,$pagecontent,$PAGEDATA){
	include SCRIPTBASE . 'common/Smarty/Smarty.class.php';
	global $DBVARS,$PLUGINS;
	$smarty = new Smarty;
	$smarty->compile_dir=USERBASE . 'templates_c';

	// { some straight replaces
	$smarty->assign('PAGECONTENT','<div id="__webmePageContent">'.$pagecontent.'</div>');
	$smarty->assign('WEBSITE_TITLE',htmlspecialchars($DBVARS['site_title']));
	$smarty->assign('WEBSITE_SUBTITLE',htmlspecialchars($DBVARS['site_subtitle']));
	$smarty->assign('PAGEDATA',$PAGEDATA);
//	$pagename=($PAGEDATA->title=='')?$PAGEDATA->name:$PAGEDATA->title;
//	$template=str_replace('%PAGENAME%',htmlspecialchars($pagename),$template);
//	$template=str_replace('%PAGEID%','page'.$PAGEDATA->id,$template);
//	$template=str_replace('%DATE%',date('D d, M Y'),$template);
//	$template=str_replace('%LOGOUT%',is_logged_in()?'<a href="/?logout=1" id="logout">Log Out</a>':'',$template);
//	if(eregi('%BREADCRUMBS%',$template))$template=str_replace('%BREADCRUMBS%',breadcrumbs($PAGEDATA->id),$template);
	// }
//	$template=webmeParse($template);
	$smarty->register_function('LOGO', 'logoDisplay');
	$smarty->register_function('MENU', 'menuDisplay');
	foreach($PLUGINS as $pname=>$plugin){
		if(isset($plugin['frontend']['template_functions'])){
			foreach($plugin['frontend']['template_functions'] as $fname=>$vals){
				$smarty->register_function($fname,$vals['function']);
			}
		}
	}
	$smarty->assign('METADATA',template_get_metadata($template,$PAGEDATA));
	// { display the document
	ob_start();
	$smarty->display($template);
	ob_show_and_log('page','Content-type: text/html; Charset=utf-8');
	// }
}
