<?php
$diameter=280;
$cats=dbAll(
	'select id,name,associated_colour as col from products_categories where parent_id=3 and enabled'
);

$id='products_categories_'.md5(rand());
echo '<div id="'.$id.'" class="products-widget" style="width:'.$diameter
	.'px;height:'.($diameter+30).'px">loading...</div>'
	.'<script>$(function(){'
	.'products_widget("'.$id.'",'.json_encode($cats).');'
	.'});</script>';
echo '<!--[if IE]><script src="/ww.plugins/products/frontend/excanvas.js">'
	.'</script><![endif]-->';
WW_addScript('/ww.plugins/products/frontend/jquery.canvas.js');
WW_addScript('/ww.plugins/products/frontend/widget.js');
