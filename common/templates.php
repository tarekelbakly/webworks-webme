<?php
function show_page($template,$pagecontent,$PAGEDATA,$plugins_to_load){
	include BASEDIR . 'common/Smarty/Smarty.class.php';
	$smarty = new Smarty;

	// { some straight replaces
	$smarty->assign('PAGECONTENT','<div id="__webmePageContent">'.$pagecontent.'</div>');
//	$pagename=($PAGEDATA->title=='')?$PAGEDATA->name:$PAGEDATA->title;
//	$template=str_replace('%PAGENAME%',htmlspecialchars($pagename),$template);
//	$template=str_replace('%PAGEID%','page'.$PAGEDATA->id,$template);
//	$template=str_replace('%DATE%',date('D d, M Y'),$template);
//	$template=str_replace('%LOGOUT%',is_logged_in()?'<a href="/?logout=1" id="logout">Log Out</a>':'',$template);
//	if(eregi('%BREADCRUMBS%',$template))$template=str_replace('%BREADCRUMBS%',breadcrumbs($PAGEDATA->id),$template);
	// }
//	$template=webmeParse($template);
	$smarty->register_function('MENU', 'menuDisplay');

/*	if(ereg('%MENU{[^}]*}%',$template)){ // %MENU%
		require_once('common/menus.php');
		$template=menu_setup_main_menu($template);
	} */
	require_once BASEDIR . 'common/template_metadata.php';
	$smarty->assign('METADATA',template_get_metadata($template,$PAGEDATA,$plugins_to_load));
	// { display the document
	header('Content-type: text/html; Charset=utf-8');
	$smarty->display($template);
	// }
}
