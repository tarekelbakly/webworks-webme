<?php
function get_subdirs($base,$dir){
	$arr=array();
	$D=new DirectoryIterator($base.$dir);
	$ds=array();
	foreach($D as $dname){
		$d=$dname.'';
		if($d{0}=='.')continue;
		if(!is_dir($base.$dir.'/'.$d))continue;
		$ds[]=$d;
	}
	asort($ds);
	foreach($ds as $d){
		$arr[]=$dir.'/'.$d;
		$arr=array_merge($arr,get_subdirs($base,$dir.'/'.$d));
	}
	return $arr;
}
$id=(int)@$_REQUEST['id'];
if(isset($_REQUEST['action'])){
	if($_REQUEST['action']=='Save Protected Files'){
		$q='directory="'.addslashes(@$_REQUEST['directory']).'",recipient_email="'.addslashes(@$_REQUEST['recipient_email']).'"';
		if($id)dbQuery("update protected_files set $q where id=$id");
		else{
			dbQuery("insert into protected_files set $q");
			$id=dbOne("select last_insert_id() as id",'id');
		}
	}
	else if($_REQUEST['action']=='delete'){
		dbQuery("delete from protected_files where id=$id");
		$id=0;
	}
	cache_clear('protected_files');
}

$r=dbRow('select * from protected_files where id='.$id);
echo '<form method="post" action="',$_url,'"><table style="width:90%">';
echo '<tr><th>Directory containing the files</th><td><select id="directory" name="directory"><option value="/">/</option>';
foreach(get_subdirs(USERBASE.'f','') as $d){
	echo '<option value="',htmlspecialchars($d),'"';
	if($d==@$r['directory'])echo ' selected="selected"';
	echo '>',htmlspecialchars($d),'</option>';
}
echo '</select></td></tr>';
echo '<tr><td>&nbsp;</td><td><a class="button" href="#page_vars[directory]" onclick="javascript:window.open(\'/j/kfm/?startup_folder=\'+$(\'#directory\').attr(\'value\'),\'kfm\',\'modal,width=800,height=600\');">Manage Files</a></td></tr>';
echo '<tr><th>Email to send download alerts to</th><td><input name="recipient_email" value="',htmlspecialchars(@$r['recipient_email']),'" /></td></tr>';
echo '<tr><th colspan="2"><input type="hidden" name="id" value="',$id,'" />';
echo '<input type="submit" name="action" value="Save Protected Files" />';
if($id)echo '<a style="margin-left:20px;" href="/ww.admin/plugin.php?_plugin=protected_files&amp;id='.$id.'&amp;action=delete" onclick="return confirm(\'are you sure you want to remove this?\')" title="delete">[x]</a>';
echo '</th></tr></table></form>';
