<?php
function online_store_calculate_total(){
	$total=0;
	foreach($_SESSION['online-store']['items'] as $item)$total+=($item['cost']*$item['amt']);
	$_SESSION['online-store']['total']=$total;
	return $total;
}
