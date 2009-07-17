<?php
/*
	Webme Dynamic Search Plugin v0.1
	File: frontend/search.php
	Developer: Conor Mac Aoidh <http://macaoidh.name>
	Report Bugs: <conor@macaoidh.name>
*/

$html='
<link rel="stylesheet" type="text/css" href="/ww.plugins/dynamic-search/files/style.css"/>
<script type="text/javascript" src="/ww.plugins/dynamic-search/files/general.js"></script>

<h1>Search</h1>

<form method="get" id="dynamic_search">
	<table id="dynamic_search_table">
		<tr>
			<td><select name="dynamic_category" id="dynamic_search_select">
			<option>Site Wide</option>';
$catags=explode(',',$SS['cat']);
foreach($catags as $catag){
	if($catag!='') $html.='<option>'.$catag.'</option>';
}
$html.='
                        </select></td>
			<td><input type="text" name="dynamic_search" value="Enter Keywords..." id="dynamic_searchfield"/></td>
			<td><input type="submit" value="Search" id="dynamic_search_submit" name="dynamic_search_submit"/></td>
		</tr>
	</table>
</form>
';

?>
