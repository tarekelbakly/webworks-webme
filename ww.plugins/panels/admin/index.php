<?php
echo '<div class="right-column"><h3>Panels</h3><p>Click a header to open it.</p><div id="panels"></div></div>';
echo '<div class="has-right-column"><h3>Widgets</h3><p>Drag a widget into a panel on the right.</p><div id="widgets"></div><br style="clear:both" /></div>';
echo '<link rel="stylesheet" type="text/css" href="/ww.plugins/panels/c/admin.css" />';
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
// echo '<script src="http://inlinemultiselect.googlecode.com/files/jquery.inlinemultiselect.min.js"></script>';
echo '<script src="/ww.plugins/panels/j/jquery.inlinemultiselect.js"></script>';
// }
