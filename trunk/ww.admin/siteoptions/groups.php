<?php
echo '<h2>'.__('Groups').'</h2>';
$groups=array();
// { handle actions
if (isset($_REQUEST['action'])) {
	$id=(int)$_REQUEST['id'];
	if($action=='delete'){
		if ($id==1) {
			echo '<em>cannot delete administrator group</em>';
		}
		else {
			dbQuery("delete from groups where id=$id");
			dbQuery("delete from users_groups where user_accounts_id=$id");
		}
		unset($_REQUEST['id']);
	}
	if ($action=='Save') {
		$name='';
		if ($id!=1) {
			$name='name="'.addslashes($_REQUEST['name']).'",';
		}
		$sql='update groups set '.$name
			.'meta="'.addslashes(json_encode($_REQUEST['meta'])).'"'
			.' where id='.$id;
		dbQuery($sql);
		echo '<em>group updated</em>';
	}
}
// }
// { form
if (isset($_REQUEST['id']) && 1==(int)$_REQUEST['id']) {
	echo '<em>you cannot edit the administrator group</em>';
	unset($_REQUEST['id']);
}
if (isset($_REQUEST['id'])) {
	// { form start
	$id=(int)$_REQUEST['id'];
	$r=dbRow("select * from groups where id=$id");
	echo '<form action="siteoptions.php?page=groups&amp;id='.$id.'" method="post">';
	echo '<input type="hidden" name="id" value="'.$id.'" />';
	// { get Meta-data
	if (strpos($r['meta'], '{') !== 0) {
		$r['meta']='{}';
	}
	$meta=json_decode($r['meta'], true);
	// }}
	echo '<table>';
	// }
	// { name
	echo '<tr><th>Name</th><td><input name="name" value="'.htmlspecialchars($r['name']).'"';
	if ($id==1) {
		echo ' disabled="disabled"';
	}
	echo '/></td></tr>';
	// }
	// { user profile message
	echo '<tr><th>A message to show in the user profile.</th><td>'.ckeditor('meta[user-profile-message]', @$meta['user-profile-message'], 100).'</td></tr>';
	// }
	// { paid memberships
	echo '<tr><td colspan="2"><fieldset><legend>paid memberships</legend><table>';
	// { does this group require paid membership?
	echo '<tr><th style="width:30%">Does this group require paid membership?</th><td><select name="meta[paid-membership]"><option value="">No</option><option value="yes"';
	if ('yes'==@$meta['paid-membership']) {
		echo ' selected="selected"';
	}
	echo '>Yes</option></td></tr>';
	// }
	// { membership period
	$periods=array('Day', 'Week', 'Month', 'Year');
	$num=(int)@$meta['paid-membership-subscription-period-num'];
	if (!$num) {
		$num=7;
	}
	echo '<tr><th>Subscription recurring cycle</th><td><input name="meta[paid-membership-subscription-period-num]" class="small" value="'.$num.'"/>'
		.'<select name="meta[paid-membership-subscription-period]">';
	foreach ($periods as $v) {
		echo '<option';
		if ($v==@$meta['paid-membership-subscription-period']) {
			echo ' selected="selected"';
		}
		echo '>'.$v.'</option>';
	}
	echo '</select></td></tr>';
	// }
	// { paypal address
	echo '<!-- tr><th>PayPal recipient email address</th><td><input name="meta[paid-membership-paypal-recipient]" value="'.htmlspecialchars(@$meta['paid-membership-paypal-recipient']).'"/></td></tr -->';
	// }
	// { paypal recurring code
	echo '<tr><th>Paypal code for recurring payments button.'
		.'<div style="font-size:9px;text-align:left;">log into <a href="http://paypal.ie/" target="_blank">PayPal</a>.<br/>then go to <a href="https://www.paypal.com/ie/cgi-bin/webscr?cmd=_xclick-sub-factory" target="_blank">subscription page</a>.<br/>"Item Name" is "paid-membership-group-id".<br/>"Subscription ID" is '
		.$id.'.<br/>Enter the subscription fee in "Billing amount each cycle".<br/>"Billing cycle" should match the subscription period above.<br/>open the "Advanced Features" section.<br/>'
		.'tick the "advanced variables" checkbox.<br />in "Advanced Variables", put "notify_url=http://'.$_SERVER['HTTP_HOST'].'/ww.incs/group-subscription-update.php".<br/>click "Create Button".</div>'
		.'</th><td><textarea name="meta[paid-membership-paypal-recurring-payments]" style="width:100%">'.htmlspecialchars(@$meta['paid-membership-paypal-recurring-payments']).'</textarea></td></tr>';
	// }
	echo '</table></fieldset></td></tr>';
	// }
	// { mailing list registration
	echo '<tr><th>Mailing list registration</th><td><select id="userlogin-mailinglist" name="meta[mailinglist]"><option value=""> -- none -- </option>';
	$arr=array('Mailchimp');
	foreach ($arr as $a) {
		echo '<option';
		if ($a==@$meta['mailinglist']) {
			echo ' selected="selected"';
		}
		echo '>'.$a.'</option>';
	}
	echo '</select>';
	switch (@$meta['mailinglist']) {
		case 'Mailchimp': // {
			echo '<table>'
				.'<tr><th>API Key</th><td><input name="meta[mailinglist_apikey]" value="'.htmlspecialchars(@$meta['mailinglist_apikey']).'"/></td></tr>'
				.'<tr><th>List</th><td>';
			if (@$meta['mailinglist_apikey']) {
				$apikey=$meta['mailinglist_apikey'];
				$data = array(
					'apikey'=>$apikey
				);
				$payload = json_encode($data);
				$submit_url='http://'.preg_replace('/.*-/', '', $apikey).'.api.mailchimp.com/1.3/?method=lists';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $submit_url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, urlencode($payload));
				$result = curl_exec($ch);
				curl_close ($ch);
				$data=json_decode($result);
				if (!count($data->data)) {
					echo 'you have not created any Malchimp Lists';
				}
				else {
					echo '<select name="meta[mailinglist_listid]"><option value=""> -- please choose -- </option>';
					foreach ($data->data as $list) {
						echo '<option value="'.$list->id.'"';
						if ($list->id==@$meta['mailinglist_listid']) {
							echo ' selected="selected"';
						}
						echo '>'.htmlspecialchars($list->name).' ('.$list->stats->member_count.' members)</option>';
					}
					echo '</select>';
				}
			}
			else {
				echo 'fill in the API Key first, and update the page.';
			}
			echo '</td></tr>';
			echo '</table>';
		break; // }
	}
	echo '</td></tr>';
	// }
	// { form end
	echo '</table>';
	echo '<input type="submit" name="action" value="Save" />';
	echo '</form>';
	// }
}
// }
// { list all groups
$groups=dbAll('select id,name from groups where id>0 order by name');
echo '<table style="min-width:50%"><tr><th>Group</th><th>Users</th><th>Actions</th></tr>';
foreach($groups as $group){
	echo '<tr><th><a href="siteoptions.php?page=groups&amp;id='.$group['id'].'">'.htmlspecialchars($group['name']).'</a></th>';
	// { number of users
	echo '<td>'.dbOne('select count(user_accounts_id) as users from users_groups where groups_id='.$group['id'], 'users').'</td>';
	// }
	echo '<td><a href="siteoptions.php?page=groups&amp;id='.$group['id'].'">edit</a> <a href="siteoptions.php?page=groups&amp;id='.$group['id'].'&amp;action=delete" onclick="return confirm(\'are you sure you want to delete this group?\')">[x]</a></td></tr>';
}
echo '</table>';
// }
