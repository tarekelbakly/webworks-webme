<?php
$translation=0;
echo '<h3>'.($edit?__('Edit Page'):__('Create Page')).'</h3>';
if(!allowedToEditPage($id)){
	if($id)echo '<em>'.__('You do not have Edit rights for this page').'</em>';
	else echo '<em>'.__('You cannot create a top-level page. Please choose a page to edit from the menu on the left.').'</em>';
}
else{
	$page_vars=array();
	echo '<form class="pageForm" method="post" action="'.$_SERVER['PHP_SELF'].'">';
	if($edit){
		$page=dbRow("SELECT * FROM pages WHERE id=$id");
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
		$page=array('parent'=>$id,'type'=>0,'body'=>'','name'=>'','title'=>'','ord'=>0,'description'=>'','id'=>0,'keywords'=>'','special'=>0,'template'=>'','stylesheet'=>'','category'=>'','importance'=>0.5);
		$id=0;
	}
	echo wInput('id','hidden',$page['id']);
	echo '<div class="tabs" style="clear:right">';
	// { Common Details
	echo '<div class="tabPage"><h2>'.__('Common Details').'</h2><table style="clear:right">';
	{ # name, title, url
		echo '<tr>';
		{ # name
			echo '<th width="10%">'.__('name').'</th><td width="23%"><input id="name" name="name" value="'.htmlspecialchars($page['name']).'" /></td>';
		}
		{ # title
			echo '<th width="10%">'.__('title').'</th><td width="23%">'.wInput('title','',htmlspecialchars($page['title'])).'</td>';
		}
		{ # url 
			if($edit){
				$u='/'.str_replace(' ','-',$page['name']);
				$u='<a href="'.$u.'">'.urldecode($u).'</a>';
			}
			else $u=__('not available yet');
			echo '<th width="10%">'.__('URL').'</th><td width="23%">'.$u.'</td>';
		}
		echo '</tr>';
	}
	{ # page type and parent 
		{ # type
			echo '<tr><th>'.__('type').'</th><td><select name="type">';
			foreach($pagetypes as $a){
				if(has_page_permissions($a[2]) || !$a[2]){
					$tmp=($a[0]==$page['type'])?' selected="selected"':'';
					echo '<option value="'.$a[0].'"'.$tmp.'>'.htmlspecialchars($a[1]).'</option>';
				}
			}
			echo '</select></td>';
		}
		{ # parent
			echo '<th>'.__('parent').'</th><td><select name="parent">';
			$tmp=($page['parent']==0)?' selected="selected"':'';
			echo '<option value="0"'.$tmp.'>--  '.__('none').'  --</option>';
			selectkiddies(0,0,$page['parent'],$id);
			echo '</select></td>';
		}
		{ # template
			echo '<th>'.__('template').'</th><td>';
			$ex='ls '.BASEDIR.'ww.skins/'.$_SESSION['viewing_skin'].'/h/*html';
			$d=`$ex`;
			$d=explode("\n",$d);array_pop($d);
			if(count($d)>1){
				echo '<select name="template">';
				foreach($d as $f){
					$f=preg_replace('/^\.\.\/|\n|\r|$/','',$f);
					echo '<option value="'.$f.'"';
					if($f==$page['template'])echo ' selected="selected"';
					echo '>'.preg_replace('/.*skins\/[^\/]*\/h\/|\.html/','',$f).'</option>';
				}
				echo '</select>';
			}else echo htmlspecialchars(preg_replace('/.*skins\/[^\/]*\/h\/|\.html/','',$d[0]));
			echo '</td></tr>';
		}
	}
	// { page-type-specific data
	// { generate FCKeditor CSS
	$cssurl=fckeditor_generateCSS($page['id']);
	// }
	switch($page['type']){
		case 2: { # events 
			echo '<tr><td colspan="6" style="height:290px" class="eventsAdmin" id="eventsAdmin">'.__('please wait - loading...').'</td></tr>';
			$plugins_to_load[]='"eventsAdmin":1';
			break;
		}
		case 3: { # user login 
			echo '<tr><th>'.__('Visibility').'</th><td>'.wInput('page_vars[userlogin_visibility]','select',array(
				'3'=>__('Login and Register forms'),
				'1'=>__('Login form'),
				'2'=>__('Register form')
			),$page_vars['userlogin_visibility']).'</td>';
			echo '<th colspan="3">'.__('On login, redirect to:').'</th><td>';
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
		}
		case 4: { # blog indexes 
			$tmp=($page['parent']==0)?' selected="selected"':'';
			echo '<tr><th>'.__('index blogs from').'</th><td><select name="blog_parent"><option value="0"'.$tmp.'>--  '.__('none').'  --</option>';
			$r2=dbRow('select parent,amount_to_show from blog_indexes where pageid="'.$id.'"');
			if(count($r2)){
				$blog_pageid=$r2['parent'];
			}else $blog_pageid=$id;
			selectkiddies(0,0,$blog_pageid,-1);
			echo '</select></td><th>'.__('Amount To Show').'</th><td><select name="blog_amount_to_show">';
			$arr=array(__('300 Characters, plain text'),__('600 Characters, plain text'),__('Everything, including formats'));
			foreach($arr as $k=>$v){
				echo '<option value="'.$k.'"';
				if(count($r2)&&$k==$r2['amount_to_show'])echo ' selected="selected"';
				echo '>'.htmlspecialchars($v).'</option>';
			}
			echo '</select></td></tr>';
			break;
		}
		case 7: { # news 
			echo '<tr><td colspan="6" style="height:290px" class="newsAdmin" id="newsAdmin">'.__('please wait - loading...').'</td></tr>';
			$plugins_to_load[]='"newsAdmin":1';
			break;
		}
		case 8: { # products
			echo '<tr><th>'.__('content header').'</th><td colspan="4">';
			echo fckeditor('page_vars[content_header]',$page_vars['content_header'],false,$cssurl);
			echo '</textarea></td>';
			echo '<td>';
			// { specify a product category
			echo __('category to show').'<br /><select name="page_vars[category_to_show]"><option value="0">'.__('all categories').'</option>';
			$r3=dbAll('select id,name from product_category order by name');
			foreach($r3 as $r2){
				echo '<option value="'.$r2['id'].'"';
				if($r2['id']==$page_vars['category_to_show'])echo' selected="selected"';
				echo '>'.htmlspecialchars($r2['name']).'</option>';
			}
			echo '</select>';
			// }
			// { specify a product
			echo __('product to show').'<br /><select name="page_vars[product_to_show]"><option value="0">'.__('all products').'</option>';
			$r3=dbAll('select id,name from products order by name');
			foreach($r3 as $r2){
				echo '<option value="'.$r2['id'].'"';
				if($r2['id']==$page_vars['product_to_show'])echo' selected="selected"';
				echo '>'.htmlspecialchars($r2['name']).'</option>';
			}
			echo '</select>';
			// }
			// { product template
			echo __('product template').'<br /><select name="page_vars[product_type]"><option value="0">'.__('all types').'</option>';
			$r3=dbAll('select id,name from product_types order by name');
			foreach($r3 as $r2){
				echo '<option value="'.$r2['id'].'"';
				if($r2['id']==$page_vars['product_type'])echo' selected="selected"';
				echo '>'.htmlspecialchars($r2['name']).'</option>';
			}
			echo '</select>';
			// }
			echo '</td></tr>';
			break;
		}
		default: // { body
			echo '<tr><th>'.__('body').'</th><td colspan="5">';
			echo fckeditor('body',$page['body'],0,$cssurl);
			echo '</td></tr>';
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
	echo '<tr><th title="'.__('This is especially useful for large sites or blogs').'">category</th><td>';
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
	$specials=array(__('Is Home Page'),__('Does not appear in navigation'),'','','',__('Not indexed by blogs'),__('Allow public comments'));
	for($i=0;$i<count($specials);++$i){
		if($specials[$i]!=''){
			echo wInput('special['.$i.']','checkbox',($page['special']&pow(2,$i))?1:0).$specials[$i].'<br />';
		}
	}
	// }
	// { other
	echo '<h4>'.__('Other').'</h4>';
	echo '<table><tr><th>'.__('Page Order').'</th><td><select name="page_order"><option value="-1">'.__('end of navigation').'</option><option value="0">'.__('start of navigation').'</option></select></td></tr></table>';
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
