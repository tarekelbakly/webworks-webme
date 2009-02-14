<?php
update_stats();
echo '<!--[if IE]><script language="javascript" type="text/javascript" src="/j/flot-0.5/excanvas.pack.js"></script><![endif]-->';
echo '<script type="text/javascript" src="/j/flot-0.5/jquery.flot.pack.js"></script>';

echo '<div id="placeholder" style="width:600px;height:300px;"></div><p>Use the bar below to select a range</p><div id="overview" style="margin-left:50px;margin-top:20px;width:400px;height:50px"></div>';

$visitors=dbAll('select unix_timestamp(date(log_date)) as log_d,count(log_date) as visitors from logs group by log_d order by log_date');
echo '<script type="text/javascript">window.visitors=[';
foreach($visitors as $visitor){
	echo '['.$visitor['log_d'].'000,'.$visitor['visitors'].'],';
}
echo '];</script>';

echo '<script type="text/javascript" src="stats/summary.js"></script>';
