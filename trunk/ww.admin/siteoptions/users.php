<?php
echo '<h2>'.__('Users').'</h2>';
$groups=array();
// { handle actions
if(isset($_REQUEST['action'])){
	$id=(int)$_REQUEST['id'];
	if($action=='delete'){
		dbQuery("delete from user_accounts where id=$id");
		dbQuery("delete from users_groups where user_accounts_id=$id");
		unset($_REQUEST['id']);
	}
	if($action=='Save'){
		$sql='set email="'.addslashes($_REQUEST['email']).'",name="'.addslashes($_REQUEST['name']).'",phone="'.addslashes($_REQUEST['phone']).'",active="'.(int)$_REQUEST['active'].'",address="'.addslashes($_REQUEST['address']).'"';
		if(isset($_REQUEST['password']) && $_REQUEST['password']!=''){
			if($_REQUEST['password']!==$_REQUEST['password2'])echo '<em>Password not updated. Must be entered the same twice.</em>';
			else $sql.=',password=md5("'.addslashes($_REQUEST['password']).'")';
		}
		if($id==-1){
			dbQuery('insert into user_accounts '.$sql);
			$id=dbOne("select last_insert_id() as id limit 1",'id');
		}
		else{
			dbQuery('update user_accounts '.$sql.' where id='.$id);
		}
		dbQuery("delete from users_groups where user_accounts_id=$id");
		// { first, create new groups if required
		if(isset($_REQUEST['new_groups'])){
			foreach($_REQUEST['new_groups'] as $ng){
				$n=addslashes($ng);
				dbQuery("insert into groups values(0,'$n',0)");
				$_REQUEST['groups'][dbOne('select last_insert_id() as id','id')]=true;
			}
		}
		// }
		foreach($_REQUEST['groups'] as $k=>$n)dbQuery("insert into users_groups set user_accounts_id=$id,groups_id=".(int)$k);
		// { now remove any groups other than Administrator that are not used at all
		$rs=dbAll('select id from (select groups.id,groups_id from groups left join users_groups on groups.id=groups_id) as derived where groups_id is null');
		foreach($rs as $r)if($r['id']!='1')dbRow('delete from groups where id='.$r['id']);
		// }
		echo '<em>'.__('users updated').'</em>';
	}
}
// }
// { form
if(isset($_REQUEST['id'])){
	$id=(int)$_REQUEST['id'];
	$r=dbRow("select * from user_accounts where id=$id");
	if(!is_array($r) || !count($r)){
		$r=array('id'=>-1,'email'=>'','name'=>'','phone'=>'','active'=>0,'address'=>'','parent'=>$_SESSION['userdata']['id']);
	}
	echo '<form action="siteoptions.php?page=users&amp;id='.$id.'" method="post">';
	echo '<input type="hidden" name="id" value="'.$id.'" />';
	echo '<table><tr><th>Name</th><td><input name="name" value="'.htmlspecialchars($r['name']).'" /></td><th>Password</th><td><input name="password" type="password" /></td></tr>';
	echo '<tr><th>Email</th><td><input name="email" value="'.htmlspecialchars($r['email']).'" /></td><th>(repeat)</th><td><input name="password2" type="password" /></td></tr>';
	echo '<tr><th rowspan="3">Address</th><td rowspan="3"><textarea name="address" style="height:100px;width:200px">'.htmlspecialchars($r['address']).'</textarea></td><th>Phone</th><td><input name="phone" value="'.htmlspecialchars($r['phone']).'" /></td></tr>';
	// { groups
	echo '<tr><th>Groups</th><td class="groups">';
	$grs=dbAll('select id,name from groups');
	$gms=array();
	foreach($grs as $g){
		$groups[$g['id']]=$g['name'];
	}
	$grs=dbAll("select groups_id from users_groups where user_accounts_id=$id");
	foreach($grs as $g)$gms[$g['groups_id']]=true;
	foreach($groups as $k=>$g){
		echo '<input type="checkbox" name="groups['.$k.']"';
		if(isset($gms[$k]))echo ' checked="checked"';
		echo ' />',htmlspecialchars($g),'<br />';
	}
	echo '</td></tr>';
	// }
	echo '<tr><th>Active</th><td><select name="active"><option value="0">No</option><option value="1"'.($r['active']?' selected="selected"':'').'>Yes</option></select></td></tr>';
	echo '</table>';
	echo '<input type="submit" name="action" value="Save" />';
	echo '</form>';
}
// }
// { list all users
$users=dbAll('select id,email from user_accounts order by name');
echo '<table style="min-width:50%"><tr><th>User</th><th>Groups</th><th>Actions</th></tr>';
foreach($users as $user){
	echo '<tr><th><a href="siteoptions.php?page=users&amp;id='.$user['id'].'">'.htmlspecialchars($user['email']).'</a></th>';
	// { groups
	echo '<td>';
	$grs=dbAll("select * from users_groups where user_accounts_id=$user[id]");
	$garr=array();
	foreach($grs as $gr){
		if(!isset($groups[$gr['groups_id']])){
			$groups[$gr['groups_id']]=dbOne("select name from groups where id=$gr[groups_id] limit 1",'name');
		}
		$garr[]=$groups[$gr['groups_id']];
	}
	echo join(', ',$garr);
	echo '</td>';
	// }
	echo '<td><a href="siteoptions.php?page=users&amp;id='.$user['id'].'">edit</a> <a href="siteoptions.php?page=users&amp;id='.$user['id'].'&amp;action=delete" onclick="return confirm(\'are you sure you want to delete this user?\')">[x]</a></td></tr>';
}
echo '<tr><td colspan="2"></td><td><a href="siteoptions.php?page=users&amp;id=-1">Create User</a></td></tr>';
echo '</table>';
// }
// { javascript
echo '<script src="/ww.admin/siteoptions/users.js"></script>';
// }