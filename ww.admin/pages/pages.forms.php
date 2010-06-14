<?php
$translation=0;
if(!allowedToEditPage($id)){
	if($id)$msgs.='<em>'.__('You do not have Edit rights for this page').'</em>';
	else $msgs.='<em>'.__('You cannot create a top-level page. Please choose a page to edit from the menu on the left.').'</em>';
	echo $msgs;
}
else{
	if($id && $edit){ // check that page exists
		$page=dbRow("SELECT * FROM pages WHERE id=$id");
		if(!$page)$edit=false;
	}
	$page_vars=array();
	if(isset($msgs) && $msgs!='')echo $msgs;
	echo '<form id="pages_form" class="pageForm" method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo '<div style="float:right">'.wInput('action','submit',($edit?__('Update Page Details'):__('Insert Page Details'))).'</div>';
	if($page['special']&2 && !isset($_REQUEST['newpage_dialog']))echo '<em>NOTE: this page is currently hidden from the front-end navigation. Use the "Advanced Options" to un-hide it.</em>';
	if($edit){
		if(isset($_REQUEST['newpage_dialog']) && $page['special']&2)$page['special']-=2;
		$pvq=dbAll("SELECT * FROM page_vars WHERE page_id=$id");
		foreach($pvq as $pvr)$page_vars[$pvr['name']]=$pvr['value'];
		// { language
		if(getVar('__editing_language'))$_SESSION['editing_language']=getVar('__editing_language');
		$r=dbRow("SELECT * FROM site_vars WHERE name='languages'");
		if($r['value'])$langs=json_decode($r['value']);
		else $langs=array();
		if(count($langs)>1){
			if(!isset($_SESSION['editing_language']))$_SESSION['editing_language']=$langs[0]->iso;
			if($langs[0]->iso!=$_SESSION['editing_language'])$translation=1;
			echo '<select name="__editing_language" style="float:right" onchange="if(confirm(\''.__('change Editing Language without saving?').'\'))document.location=\'pages.php?id='.$id.'&amp;__editing_language=\'+this.value;">';
			foreach($langs as $lang){
				echo '<option value="'.htmlspecialchars($lang->iso).'"';
				if($lang->iso==$_SESSION['editing_language'])echo ' selected="selected"';
				echo '>'.htmlspecialchars($lang->name).'</option>';
			}
			echo '</select>';
		}
		if($translation){
			$rs=dbAll("SELECT * FROM translations WHERE object_type='page' AND object_id=$id AND lang='".addslashes($_SESSION['editing_language'])."'");
			foreach($rs as $r){
				if($r['value']!='')$page[$r['name']]=$r['value'];
			}
		}
		// }
	}
	else{
		$parent=isset($_REQUEST['parent'])?(int)$_REQUEST['parent']:0;
		$special=0;
		if(isset($_REQUEST['hidden']))$special+=2;
		$page=array('parent'=>$parent,'type'=>'0','body'=>'','name'=>'','title'=>'','ord'=>0,'description'=>'','id'=>0,'keywords'=>'','special'=>$special,'template'=>'','stylesheet'=>'','importance'=>0.5);
		$id=0;
	}
	echo wInput('id','hidden',$page['id']);
	echo '<div class="tabs">';
	// { Common Details
	echo '<div class="tabPage"><h2>'.__('Common Details').'</h2><table style="clear:right">';
	// { name, title, url
	echo '<tr>';
	// { name
	echo '<th width="6%"><div class="help name"></div>'.__('name').'</th><td width="23%"><input id="name" name="name" value="'.htmlspecialchars($page['name']).'" /></td>';
	// }
	// { title
	echo '<th width="10%"><div class="help title"></div>'.__('title').'</th><td width="23%">'.wInput('title','',htmlspecialchars($page['title'])).'</td>';
	// }
	// { url 
	echo '<th colspan="2">';
	if($edit){
		$u='/'.str_replace(' ','-',$page['name']);
		echo '<a style="font-weight:bold;color:red" href="'.$u.'" target="_blank">VIEW PAGE</a>';
	}
	else echo '&nbsp;';
	echo '</th>';
	// }
	echo '</tr>';
	// }
	// { page type, parent, associated date
	// { type
	echo '<tr><th><div class="help type"></div>'.__('type').'</th><td><select name="type">';
	if(preg_match('/^[0-9]*$/',$page['type']))foreach($pagetypes as $a){
		if(has_access_permissions($a[2]) || !$a[2]){
			if($a[0]==$page['type'])echo '<option value="',$a[0],'" selected="selected">',htmlspecialchars($a[1]),'</option>';
		}
	}
	$plugin=false;
	if(!preg_match('/^[0-9]*$/',$page['type']))foreach($PLUGINS as $n=>$p){
		if(isset($p['admin']['page_type']) && $page['type']==$n){
			echo '<option value="',htmlspecialchars($n),'" selected="selected">',htmlspecialchars($n),'</option>';
			$plugin=$p;
		}
	}
	echo '</select></td>';
	// }
	// { parent
	echo '<th><div class="help parent"></div>',__('parent'),'</th><td>',"\n\n",'<select name="parent">';
	if($page['parent']){
		$parent=Page::getInstance($page['parent']);
		echo '<option value="',$parent->id,'">',htmlspecialchars($parent->name),'</option>';
	}
	else echo '<option value="0"> -- ',__('none'),' -- </option>';
	echo '</select>',"\n\n",'</td>';
	// }
	// { associated date
	if(!isset($page['associated_date']) || !preg_match('/^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/',$page['associated_date']) || $page['associated_date']=='0000-00-00')
		$page['associated_date']=date('Y-m-d');
	echo '<th><div class="help associated-date"></div>Associated Date</th><td><input name="associated_date" class="date-human" value="'.$page['associated_date'].'" /></td>';
	echo '</tr>';
	// }
	// }
	// { page-type-specific data
	$page['body']=html_unfixImageResizes($page['body']);
	switch($page['type']){
		case '0': case '5': // { normal
			echo '<tr><th><div class="help body"></div>'.__('body').'</th><td colspan="5">';
			echo ckeditor('body',$page['body']);
			echo '</td></tr>';
			break;
		// }
		case '4': // { page summaries
			echo '<tr><th>pages summarised from</th><td><select name="page_summary_parent"><option value="0">--  none  --</option>';
			$r2=dbRow('select parent_id from page_summaries where page_id="'.$id.'" limit 1');
			if(count($r2)){
				$page_summary_pageid=$r2['parent_id'];
			}
			else $page_summary_pageid=$id;
			selectkiddies(0,0,$page_summary_pageid,-1);
			echo '</select></td>'.
			'<td colspan="4">Where do you want to start summarising your pages from? If you want this summary to list excerpts from all '.
			'the pages on your site, then choose "<strong>none</strong>". Otherwise, choose the page which <strong>contains</strong> '.
			'the pages you want summarised.</td></tr>';
			break;
		// }
		case '9': // { table of contents
			echo '<tr><td colspan="6"><div class="tabs">';
			echo '<div class="tabPage"><h2>Header</h2><p>This will appear above the table of contents.</p>'.ckeditor('body',$page['body']).'</div>';
			echo '<div class="tabPage"><h2>Footer</h2><p>This will appear below the table of contents.</p>'.ckeditor('page_vars[footer]',$page_vars['footer']).'</div>';
			echo '</div></td></tr>';
			break;
		// }
		default: // { plugin
			if($plugin && isset($plugin['admin']['page_type']) && function_exists($plugin['admin']['page_type'])){
				echo '<tr><td colspan="6">'.$plugin['admin']['page_type']($page,$page_vars).'</td></tr>';
			}
		// }
	}
	// }
	echo '</table></div>';
	// }
	// { Advanced Options
	echo '<div class="tabPage"><h2>'.__('Advanced Options').'</h2>';
	echo '<table>';
	echo '<td>';
	// { metadata 
	echo '<h4>'.__('MetaData').'</h4><table>';
	echo '<tr><th>'.__('keywords').'</th><td>'.wInput('keywords','',htmlspecialchars($page['keywords'])).'</td></tr>';
	echo '<tr><th>'.__('description').'</th><td>'.wInput('description','',htmlspecialchars($page['description'])).'</td></tr>';
	echo '<tr title="'.__('used by Google. importance of page relative to other pages on site. values 0.0 to 1.0').'"><th>importance</th><td>'.wInput('importance','',htmlspecialchars($page['importance'])).'</td></tr>';
	echo '<tr><th>Google Site Verification</th><td><input name="page_vars[google-site-verification]" value="'.htmlspecialchars(@$page_vars['google-site-verification']).'" /></td></tr>';
	echo '<tr>';
	// { template
	echo '<th>'.__('template').'</th><td>';
	$d=array();
	if(!file_exists(THEME_DIR.'/'.THEME.'/h/')){
		echo 'SELECTED THEME DOES NOT EXIST<br />Please <a href="/ww.admin/siteoptions.php?page=themes">select a theme</a>';
	}
	else{
		$dir=new DirectoryIterator(THEME_DIR.'/'.THEME.'/h/');
		foreach($dir as $f){
			if($f->isDot())continue;
			$n=$f->getFilename();
			if(preg_match('/\.html$/',$n))$d[]=preg_replace('/\.html$/','',$n);
		}
		asort($d);
		if(count($d)>1){
			echo '<select name="template">';
			foreach($d as $name){
				echo '<option ';
				if($name==$page['template'])echo ' selected="selected"';
				echo '>'.$name.'</option>';
			}
			echo '</select>';
		}
		else echo 'no options available';
	}
	echo '</td>';
	// }
	echo '</tr>';
	echo '</table>';
	// }
	echo '</td><td>';
	// { special
	echo '<h4>'.__('Special').'</h4>';
	$specials=array(__('Is Home Page'),__('Does not appear in navigation'),'','','','',__('Allow public comments'));
	for($i=0;$i<count($specials);++$i){
		if($specials[$i]!=''){
			echo wInput('special['.$i.']','checkbox',($page['special']&pow(2,$i))?1:0).$specials[$i].'<br />';
		}
	}
	// }
	// { other
	echo '<h4>'.__('Other').'</h4>';
	echo '<table>';
	// { order of sub-pages
	echo '<tr><th>Order of sub-pages</th><td><select name="page_vars[order_of_sub_pages]">';
	$arr=array('as shown in admin menu','alphabetically','by associated date');
	foreach($arr as $k=>$v){
		echo '<option value="'.$k.'"';
		if(isset($page_vars['order_of_sub_pages']) && $page_vars['order_of_sub_pages']==$k)echo ' selected="selected"';
		echo '>'.$v.'</option>';
	}
	echo '</select><select name="page_vars[order_of_sub_pages_dir]"><option value="0">ascending (a-z, 0-9)</option>';
	echo '<option value="1"';
	if(isset($page_vars['order_of_sub_pages_dir']) && $page_vars['order_of_sub_pages_dir']=='1')echo ' selected="selected"';
	echo '>descending (z-a, 9-0)</option></select></td></tr>';
	// }
	echo '<tr><th>'.__('Recursively update page templates').'</th><td><input type="checkbox" name="recursively_update_page_templates" /></td></tr>';
	echo '</table>';
	// }
	echo '</td></tr></table></div>';
	// }
	// { tabs added by plugins
	foreach($PLUGINS as $n=>$p){
		if(isset($p['admin']['page_panel'])){
			echo '<div class="tabPage"><h2>'.$p['admin']['page_panel']['name'].'</h2>';
			$p['admin']['page_panel']['function']($page,$page_vars);
			echo '</div>';
		}
	}
	// }
	echo '</div>';
	echo wInput('action','submit',($edit?__('Update Page Details'):__('Insert Page Details')));
	if(isset($_REQUEST['frontend-admin'])){
		echo '<input type="hidden" name="frontend-admin" value="1" />';
	}
	echo '</form>';
}
