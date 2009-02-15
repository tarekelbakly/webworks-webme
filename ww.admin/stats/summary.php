<?php
update_stats();
echo '<!--[if IE]><script language="javascript" type="text/javascript" src="/j/flot-0.5/excanvas.pack.js"></script><![endif]-->';
echo '<script type="text/javascript" src="/j/flot-0.5/jquery.flot.pack.js"></script>';

echo '<table><tr><td>visitors</td><td><div id="placeholder" style="width:600px;height:300px;"></div></td><td>page requests</td></tr></table>';
echo '<p>Use the bar below to select a range</p><div id="overview" style="margin-left:50px;margin-top:20px;width:400px;height:50px"></div>';

$requests_per_days=dbAll('select unix_timestamp(date(log_date)) as log_d,count(log_date) as page_requests from logs group by log_d order by log_date');
$unique_visitors=dbAll('select unix_timestamp(log_d2) as log_d,count(log_d2) as visitors from (select distinct date(log_date) as log_d2,ip_address from logs) as d1 group by log_d order by log_d');

echo '<script type="text/javascript">window.visitors=[';
$max_visitors=0;
foreach($unique_visitors as $visitor){
	echo '['.$visitor['log_d'].'000,'.$visitor['visitors'].'],';
	if($max_visitors<$visitor['visitors'])$max_visitors=$visitor['visitors'];
}
echo '];window.page_requests=[';
$max_page_requests=0;
foreach($requests_per_days as $requests_per_day){
	echo '['.$requests_per_day['log_d'].'000,'.$requests_per_day['page_requests'].'],';
	if($max_page_requests<$requests_per_day['page_requests'])$max_page_requests=$requests_per_day['page_requests'];
}
echo "];window.max_visitors=$max_visitors;window.max_page_requests=$max_page_requests;</script>";

echo '<script type="text/javascript" src="stats/summary.js"></script>';
