<?php
require_once(SCRIPTBASE.'common/menus.php');
$rs=menu_getChildren(0,$id,1);
$c='<script type="text/javascript">var ajaxmenu_expandable_currentPage='.$id.';</script><table class="pagesContents ajaxmenu_expandable ajax_sortabletable" cellspacing="0" id="ajaxmenu_expandable">';
for($i=0;$i<count($rs);++$i){
	$r=$rs[$i];
	$c.='<tr id="ajaxmenu_expandable_row'.$r['id'].'" class="'.$r['classes'].' draggable"><td class="ajaxmenu_menuname">';
	if($r['numchildren'])$c.='<a id="ajaxmenu_expandable_opener'.$r['id'].'" href="javascript:ajaxmenu_expandable_open('.$r['id'].');" class="ajaxmenu_expandable_closed"></a>';
	else if($i==count($rs)-1)$c.='<span class="ajaxmenu_expandable_enditem"></span>';
	else $c.='<span class="ajaxmenu_expandable_item"></span>';
	$c.='<a href="pages.php?action=edit&amp;id='.$r['id'].'" class="fck_droppable navlink">';
	$name=$r['name'];
	if($name=='')$name='*****NO NAME*****';
	$c.=str_replace(' ','&nbsp;',htmlspecialchars($name));
	$c.='</a></td><td>';
	$c.='<a class="newsubpage" href="pages.php?action=new&amp;id='.$r['id'].'">[n]</a>';
	$c.='</td><td>';
	$c.='<a class="deletepage" href="pages.php?action=delete&amp;id='.$r['id'].'" class="pagemenu_delete" onclick="return confirm(\''.__('are you sure you want to delete this?').'\');">[x]</a>';
	$c.='</td></tr>';
}
if(admin_can_create_top_pages()){ // admin can create top-level pages
	$c.='<tr><td colspan="4" class="bottom"><a href="'.$_SERVER['PHP_SELF'].'?action=new" class="newtoppage">'.__('CLICK HERE FOR A NEW TOP-LEVEL PAGE').'</a></td></tr>';
}
$c.='</table>';
echo $c;
