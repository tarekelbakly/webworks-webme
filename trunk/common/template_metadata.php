<?php
function template_get_metadata($template,$PAGEDATA){
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
	$c.='<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"></script>';
	$c.='<script type="text/javascript" src="/js/'.$modified.'"></script>';
	$c.='<script type="text/javascript" src="/js_lang/'.$_SESSION['webme_language'].'/'.$modified.'"></script>';
	$c.='<script type="text/javascript">var pagedata={id:'.$PAGEDATA->id.',url:"'.$PAGEDATA->getRelativeURL().'",country:"'.(isset($_SESSION['os_country'])?$_SESSION['os_country']:'').'"},';
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
	$c.='<style type="text/css">.loggedin{display:'.(is_logged_in()?'block':'none').'} .loggedinCell{display:'.(is_logged_in()?'table-cell':'none').'}</style>';
	return $c;
}
