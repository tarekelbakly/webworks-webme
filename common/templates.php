<?php
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
	require_once SCRIPTBASE . 'common/template_metadata.php';
	$smarty->assign('METADATA',template_get_metadata($template,$PAGEDATA));
	// { display the document
	ob_start();
	$smarty->display($template);
	ob_show_and_log('page','Content-type: text/html; Charset=utf-8');
	// }
}
