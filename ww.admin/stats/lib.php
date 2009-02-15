<?php
function update_stats(){
	$f=file(USERBASE.'log.txt');
	foreach($f as $l){
		list($tmp,$type_data,$user_agent,$referer,$ram_used,$bandwidth,$time_to_render,$db_calls)=explode("	",$l);
		$ram_used=(int)$ram_used;
		$bandwidth=(int)$bandwidth;
		$time_to_render=(float)$time_to_render;
		$db_calls=(int)$db_calls;
		$bits=explode(' ',$tmp);
		list($log_date,$log_type,$ip_address)=array($bits[0].' '.$bits[1],$bits[2],$bits[4]);
		dbQuery("insert into logs values('$log_date','$log_type','$ip_address','".addslashes($type_data)."','".addslashes($user_agent)."','".addslashes($referer)."',$ram_used,$bandwidth,$time_to_render,$db_calls)");
	}
	file_put_contents(USERBASE.'log.txt','');
}
