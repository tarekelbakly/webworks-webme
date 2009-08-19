<?php
echo '<div class="right-column"><h3>Panels</h3><p>Click a header to open it.</p><div id="panels"></div></div>';
echo '<div class="has-right-column"><h3>Widgets</h3><p>Drag a widget into a panel on the right.</p><div id="widgets"></div><br style="clear:both" /></div>';
// { css
echo '<style type="text/css">';
echo '.has-right-column .widget-wrapper{margin:5px 10px;width:200px;height:75px;float:left;}';
echo '.widget-wrapper{border:1px solid #416BA7;-moz-border-radius:5px;}.widget-wrapper h4{margin:-1px;-moz-border-radius:5px;}';
echo '.panel-wrapper{border:1px solid #000;-moz-border-radius:5px;margin-bottom:10px}.panel-wrapper h4{background:#000;margin:-1px;-moz-border-radius:5px;}';
echo '.panel-opener{text-align:center;float:right;color:#fff;display:block;width:30px;border-left:1px solid #eee;cursor:pointer}';
echo '.panel-body{min-height:30px}';
echo '.panel-wrapper .widget-wrapper{border:1px solid #416BA7;margin:5px}.panel-wrapper .widget-wrapper h4{background:#416BA7;}';
echo '</style>';
// }
// { panel and widget data
echo '<script>';
// { panels
echo 'ww.panels=[';
$ps=array();
$rs=dbAll('select * from panels order by name');
foreach($rs as $r)$ps[]='{id:'.$r['id'].',name:"'.$r['name'].'",widgets:'.$r['body'].'}';
echo join(',',$ps);
echo '];';
// }
// { widgets
echo 'ww.widgets=[';
$ws=array();
foreach($PLUGINS as $n=>$p){
	if(isset($p['frontend']['widget']))$ws[]='{type:"'.$n.'",description:"'.addslashes($p['description']).'"}';
}
echo join(',',$ws);
echo '];';
// }
// { widget forms
echo 'ww.widgetForms={';
$ws=array();
foreach($PLUGINS as $n=>$p){
	if(isset($p['admin']['widget']) && isset($p['admin']['widget']['form_url']))$ws[]='"'.$n.'":"'.addslashes($p['admin']['widget']['form_url']).'"';
}
echo join(',',$ws);
echo '};';
// }
echo '</script><script src="/ww.plugins/panels/j/admin.js"></script>';
// }
