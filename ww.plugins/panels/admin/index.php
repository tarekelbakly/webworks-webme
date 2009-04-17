<?php
$id=(int)@$_REQUEST['id'];
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=='Save Panel'){
		$q='name="'.addslashes(@$_REQUEST['name']).'",body="'.addslashes(@$_REQUEST['body']).'"';
		if($id)dbQuery("update panels set $q where id=$id");
		else{
			dbQuery("insert into panels set $q");
			$id=dbOne("select last_insert_id() as id",'id');
		}
	}
	else if($_REQUEST['action']=='delete'){
		dbQuery("delete from panels where id=$id");
		$id=0;
	}
}
$panels=dbAll('select id,name from panels order by name');
echo '<div id="leftmenu">';
foreach($panels as $p){
	echo '<a href="/ww.admin/plugin.php?_plugin=panels&id=',$p['id'],'"';
	if($p['id'] == $id)echo ' class="thispage"';
	echo '>'.htmlspecialchars($p['name']).'</a>';
}
echo '<a href="/ww.admin/plugin.php?_plugin=panels">New Panel</a></div>';
$r=dbRow('select * from panels where id='.$id);
echo '<div id="hasleftmenu"><h2>Panel</h2>';
echo '<form method="post" action="/ww.admin/plugin.php?_plugin=panels"><table style="width:90%">';
echo '<tr><th>Name</th><td><input name="name" value="',htmlspecialchars(@$r['name']),'" /></td></tr>';
echo '<tr><th>Body</th><td>',fckeditor('body',@$r['body']),'</td></tr>';
echo '<tr><th colspan="2"><input type="hidden" name="id" value="',$id,'" />';
echo '<input type="submit" name="action" value="Save Panel" />';
if($id)echo '<a style="margin-left:20px;" href="/ww.admin/plugin.php?_plugin=panels&amp;id='.$id.'&amp;action=delete" onclick="return confirm(\'are you sure you want to remove this panel?\')" title="delete">[x]</a>';
echo '</th></tr></table></form>';
