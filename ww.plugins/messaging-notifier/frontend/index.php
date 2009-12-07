<?php
function show_messaging_notifier($vars){
	if(!is_array($vars) && isset($vars->id) && $vars->id){
		$data=dbOne('select data from messaging_notifier where id='.$vars->id,'data');
		if($data)return parse_messaging_notifier(json_decode($data));
	}
	return '<p>this Messaging Notifier is not yet defined.</p>';
}
function parse_messaging_notifier($data){
	$altogether=array();
	foreach($data as $r){
		$md5=md5($r->url);
		$f=cache_load('messaging-notifier',$md5);
		if($f===false || (file_exists(USERBASE.'ww.cache/messaging-notifier/'.$md5) && filectime(USERBASE.'ww.cache/messaging-notifier/'.$md5)+$r->refresh*60 < time())){
			switch($r->type){
				case 'Twitter': // {
					$f=messaging_notifier_get_twitter($r);
					break;
				// }
				case 'phpBB3': // {
					$f=messaging_notifier_get_phpbb3($r);
					break;
				// }
				case 'email': // {
					$f=messaging_notifier_get_email($r);
					break;
				// }
			}
		}
		$altogether=array_merge($altogether,$f);
	}
	$html='<ul class="messaging-notifier">';
	$i=0;
	$ordered=array();
	foreach($altogether as $r){
		$ordered[$r['unixtime']]=$r;
	}
	krsort($ordered);
	foreach($ordered as $r){
		if(++$i > 10)continue;
		$html.='<li class="messaging-notifier-'.$r['type'].'"><a href="'.$r['link'].'">'.htmlspecialchars($r['title']).'</a><br /><i>'.date('Y M jS H:i',$r['unixtime']).'</i></li>';
	}
	$html.='</ul><style type="text/css">@import "/ww.plugins/messaging-notifier/c/styles.css";</style>';
	return $html;
}
function messaging_notifier_get_twitter($r){
	$f=file_get_contents($r->url);
	$dom=DOMDocument::loadXML($f);
	$items=$dom->getElementsByTagName('item');
	$arr=array();
	foreach($items as $item){
		$i=array();
		$i['type']='Twitter';
		$title=$item->getElementsByTagName('title');
		$i['title']=$title->item(0)->nodeValue;
		$link=$item->getElementsByTagName('link');
		$i['link']=$link->item(0)->nodeValue;
		$unixtime=$item->getElementsByTagName('pubDate');
		$i['unixtime']=strtotime($unixtime->item(0)->nodeValue);
		$arr[]=$i;
	}
	cache_save('messaging-notifier',md5($r->url),$arr);
	return $arr;
}
function messaging_notifier_get_phpbb3($r){
	$f=file_get_contents($r->url);
	$urlbase=preg_replace('#/[^/]*$#','/',$r->url);
	$dom=@DOMDocument::loadHTML($f);
	$lists=$dom->getElementsByTagName('ul');
	$arr=array();
	foreach($lists as $list){
		$class=$list->getAttribute('class');
		if($class!='topiclist topics')continue;
		$items=$list->getElementsByTagName('li');
		foreach($items as $item){
			$i=array();
			$i['type']='phpBB3';
			$str=$item->getElementsByTagName('dt');
			$tmp_doc=new DOMDocument();
			$tmp_doc->appendChild($tmp_doc->importNode($str->item(0),true));
			$str=preg_replace('/[ 	]+/',' ',str_replace(array("\n","\r"),' ',$tmp_doc->saveHTML()));
			$i['title']=
				preg_replace('#^.*<a href="./memb[^>]*>([^<]*)<.*#','\1',$str)
				.' wrote a post in: '
				.preg_replace('#^<dt[^>]*> <a href=[^>]*>([^<]*)<.*#','\1',$str);
			$i['link']=$urlbase.preg_replace('#^<dt[^>]*> <a href="([^"]*)".*#','\1',$str);
			if(strpos($i['link'],'&amp;sid=')!==false){ // strip session id
				$i['link']=preg_replace('/&amp;sid=.*/','',$i['link']);
			}
			$i['unixtime']=strtotime(preg_replace('#.*raquo; (.*) </dt>#','\1',$str));
			$arr[]=$i;
		}
	}
	cache_save('messaging-notifier',md5($r->url),$arr);
	return $arr;
}
function messaging_notifier_get_email($r){
	$bs=explode('|',$r->url);
	$username=$bs[0];
	$password=$bs[1];
	$hostname=$bs[2];
	$link_url=isset($bs[3])?$bs[3]:'';
	$mbox=imap_open('{'.$hostname.':143/novalidate-cert}INBOX',$username,$password);
	$emails=imap_search($mbox,'ALL');
	$arr=array();
	if($emails && is_array($emails))foreach($emails as $email_number){
		$overview=imap_fetch_overview($mbox,$email_number,0);
		$subject=$overview[0]->subject;
		$from=trim(preg_replace('/<[^>]*>/','',$overview[0]->from));
		$arr[]=array(
			'type'  => 'email',
			'title' => $from.' wrote an email: '.$subject,
			'link' => $link_url,
			'unixtime'=>strtotime($overview[0]->date)
		);
		imap_delete($mbox,$email_number);
	}
	imap_expunge($mbox);
	imap_close($mbox);
	$md5=md5($r->url);
	$c=cache_load('messaging-notifier',$md5);
	if($c===false)$c=array();
	$arr=array_merge($arr,$c);
	krsort($arr);
	$arr=array_slice($arr,0,10);
	cache_save('messaging-notifier',$md5,$arr);
	return $arr;
}
