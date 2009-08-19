<?php
$translation=0;
if(!allowedToEditPage($id)){
	if($id)echo '<em>'.__('You do not have Edit rights for this page').'</em>';
	else echo '<em>'.__('You cannot create a top-level page. Please choose a page to edit from the menu on the left.').'</em>';
}
else{
	if($id && $edit){ // check that page exists
		$page=dbRow("SELECT * FROM pages WHERE id=$id");
		if(!$page)$edit=false;
	}
	$page_vars=array();
	echo '<form id="pages_form" class="pageForm" method="post" action="'.$_SERVER['PHP_SELF'].'">';
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
		$page=array('parent'=>$parent,'type'=>'0','body'=>'','name'=>'','title'=>'','ord'=>0,'description'=>'','id'=>0,'keywords'=>'','special'=>$special,'template'=>'','stylesheet'=>'','category'=>'','importance'=>0.5);
		$id=0;
	}
	echo wInput('id','hidden',$page['id']);
	echo '<div class="tabs" style="clear:right">';
	// { Common Details
	echo '<div class="tabPage"><h2>'.__('Common Details').'</h2><table style="clear:right">';
	// { name, title, url
	echo '<tr>';
	// { name
	echo '<th width="5%">'.__('name').'</th><td width="23%"><input id="name" name="name" value="'.htmlspecialchars($page['name']).'" /></td>';
	// }
	// { title
	echo '<th width="10%">'.__('title').'</th><td width="23%">'.wInput('title','',htmlspecialchars($page['title'])).'</td>';
	// }
	// { url 
	if($edit){
		$u='/'.str_replace(' ','-',$page['name']);
		$u='<a href="'.$u.'">'.urldecode($u).'</a>';
	}
	else $u=__('not available yet');
	echo '<th width="10%">'.__('URL').'</th><td width="23%">'.$u.'</td>';
	// }
	echo '</tr>';
	// }
	// { page type and parent 
	// { type
	echo '<tr><th>'.__('type').'</th><td><select name="type">';
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
	echo '<th>',__('parent'),'</th><td>',"\n\n",'<select name="parent">';
	if($page['parent']){
		$parent=Page::getInstance($page['parent']);
		echo '<option value="',$parent->id,'">',htmlspecialchars($parent->name),'</option>';
	}
	else echo '<option value="0"> -- ',__('none'),' -- </option>';
	echo '</select>',"\n\n",'</td>';
	// }
	// { template
	echo '<th>'.__('template').'</th><td>';
	$ex='ls '.THEME_DIR.'/'.THEME.'/h/*html';
	$d=`$ex`;
	$d=explode("\n",$d);array_pop($d);
	if(count($d)>1){
		echo '<select name="template">';
		foreach($d as $f){
			$f=preg_replace('/^\.\.\/|\n|\r|$/','',$f);
			$name=preg_replace('/.*skins\/[^\/]*\/h\/|\.html/','',$f);
			echo '<option ';
			if($name==$page['template'])echo ' selected="selected"';
			echo '>'.$name.'</option>';
		}
		echo '</select>';
	}else echo htmlspecialchars(preg_replace('/.*skins\/[^\/]*\/h\/|\.html/','',$d[0]));
	echo '</td></tr>';
	// }
	// }
	// { page-type-specific data
	// { generate FCKeditor CSS
	$cssurl=fckeditor_generateCSS($page['id']);
	// }
	switch($page['type']){
		case '0': case '1': case '5': case '6': case '9': case '10': // { normal
		echo '<tr><th>'.__('body').'</th><td colspan="5">';
		echo fckeditor('body',$page['body'],0,$cssurl);
		echo '</td></tr>';
		break;
		// }
		case '2': // { events 
		echo '<tr><td colspan="6" style="height:290px" class="eventsAdmin" id="eventsAdmin">'.__('please wait - loading...').'</td></tr>';
		$plugins_to_load[]='"eventsAdmin":1';
		break;
		// }
		case '3': // { user login 
		echo '<tr><th>'.__('Visibility').'</th><td>'.wInput('page_vars[userlogin_visibility]','select',array(
			'3'=>__('Login and Register forms'),
			'1'=>__('Login form'),
			'2'=>__('Register form')
		),$page_vars['userlogin_visibility']).'</td>';
		echo '<th>'.__('Registration type:').'</th><td><select name="page_vars[userlogin_registration_type]"><option>Moderated</option>';
		echo '<option';
		if(isset($page_vars['userlogin_registration_type']) && $page_vars['userlogin_registration_type']=='Email-verified')echo ' selected="selected"';
		echo '>Email-verified</option>';
		echo '</select></td>';
		echo '<th>'.__('redirect on login:').'</th><td>';
		echo '<select name="page_vars[userlogin_redirect_to]">';
		$tmp=(!$page_vars['userlogin_redirect_to'])?' selected="selected"':'';
		echo '<option value="0"'.$tmp.'>--  '.__('none').'  --</option>';
		selectkiddies(0,0,$page_vars['userlogin_redirect_to'],$id);
		echo '</select></td>';
		echo '</td></tr>';
		echo '<tr><th>'.__('body').'</th><td colspan="5">';
		echo fckeditor('body',$page['body'],false,$cssurl);
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
		case '7': // { news 
		echo '<tr><td colspan="6" style="height:290px" class="newsAdmin" id="newsAdmin">'.__('please wait - loading...').'</td></tr>';
		$plugins_to_load[]='"newsAdmin":1';
		break;
		// }
		case '8': // { products
		echo '<tr><th>'.__('content header').'</th><td colspan="4">';
		echo fckeditor('page_vars[content_header]',$page_vars['content_header'],false,$cssurl);
		echo '</textarea></td>';
		echo '<td>';
		// { specify a product category
		echo __('category to show').'<br /><select name="page_vars[category_to_show]"><option value="0">'.__('all categories').'</option>';
		$r3=ProductCategories::getAll();
		foreach($r3 as $r2){
			echo '<option value="'.$r2->id.'"';
			if($r2->id==$page_vars['category_to_show'])echo' selected="selected"';
			echo '>'.htmlspecialchars($r2->name).'</option>';
		}
		echo '</select><input type="checkbox" name="page_vars[category_to_show_searchable]" title="searchable"';
		if(isset($page_vars['category_to_show_searchable']) && $page_vars['category_to_show_searchable'])echo ' "checked="checked"';
		echo '" /><br />';
		// }
		// { specify a product
		echo __('product to show').'<br /><select name="page_vars[product_to_show]"><option value="0">'.__('all products').'</option>';
		$r3=Products::getAll(false);
		foreach($r3 as $r2){
			echo '<option value="'.$r2->id.'"';
			if($r2->id==$page_vars['product_to_show'])echo' selected="selected"';
			echo '>'.htmlspecialchars($r2->name).'</option>';
		}
		echo '</select><br />';
		// }
		// { product template
		echo __('product template').'<br /><select name="page_vars[product_type]"><option value="0">'.__('all types').'</option>';
		$r3=ProductTypes::getAll();
		foreach($r3 as $r2){
			echo '<option value="'.$r2->id.'"';
			if($r2->id==$page_vars['product_type'])echo' selected="selected"';
			echo '>'.htmlspecialchars($r2->name).'</option>';
		}
		echo '</select>';
		// }
		echo '</td></tr>';
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
	echo '<td width="33%">';
	// { metadata 
	echo '<h4>'.__('MetaData').'</h4><table>';
	echo '<tr><th>'.__('keywords').'</th><td>'.wInput('keywords','',htmlspecialchars($page['keywords'])).'</td></tr>';
	echo '<tr><th>'.__('description').'</th><td>'.wInput('description','',htmlspecialchars($page['description'])).'</td></tr>';
	echo '<tr title="'.__('used by Google. importance of page relative to other pages on site. values 0.0 to 1.0').'"><th>importance</th><td>'.wInput('importance','',htmlspecialchars($page['importance'])).'</td></tr>';
	echo '<tr><th title="'.__('This is especially useful for large sites').'">category</th><td>';
	$categories=dbAll('select distinct category from pages where category!="" order by category desc');
	if(count($categories)){
		$arr=array();
		foreach($categories as $cat){
			$sel=$cat['category']==$page['category']?' selected="selected"':'';
			$arr[]='<option'.$sel.'>'.htmlspecialchars($cat['category']).'</option>';
		}
		echo '<select name="category1"><option value=""> -- '.__('none').' -- </option>'.join('',$arr).'</select> '.__('or').' <input style="width:50% !important" onclick="this.value=\'\';" value="'.__('add another').'" />';
	}
	else echo wInput('category2');
	echo '</td></tr>';
	echo '</table>';
	// }
	echo '</td><td width="33%">';
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
	echo '<tr><td width="30%"></td><td width="50%"></td><td></td></tr>';
	echo '<tr><th>'.__('Page Order').'</th><td colspan="2"><select name="page_order"><option value="-1">'.__('end of navigation').'</option><option value="0">'.__('start of navigation').'</option></select></td></tr>';
	echo '<tr><th colspan="2">'.__('Recursively update page templates').'</th><td><input type="checkbox" name="recursively_update_page_templates" /></td></tr>';
	echo '</table>';
	// }
	echo '</td><td width="34%">';
	// { page not visible in
	echo '<h4>'.__('Hide this page in ...').'</h4>';
	$c=new CountriesList();
	if(isset($page_vars['banned_countries']) && $page_vars['banned_countries']!='')$c->setSelected(explode(',',$page_vars['banned_countries']));
	echo $c->getSelectbox('banned_countries[]',30,8);
	// }
	echo '</td></tr></table></div>';
	// }
	echo '</div>';
	if($edit)echo '<a href="javascript:admin_editPermissions(1,'.$id.')" class="pagePermissions"></a>';
	echo wInput('action','submit',($edit?__('Update Page Details'):__('Insert Page Details')));
	echo '</div>';
	echo '</form>';
}
