<?php
/*
        Webme News Plugin v0.1
        File: admin/display.php
        Developers:
					Conor Mac Aoidh http://macaoidh.name/
					Kae Verens      http://verens.com/
        Report Bugs:
					conor@macaoidh.name
					kae@verens.com
*/

$html.='<p><a href="javascript:;" onclick="pages_new('.$GLOBALS['page']['id'].');">New News Item</a></p>'
	.'<p>To create a new news item simply click on the link above, create a new page with this news page as parent. It will then appear in the frontend as a news item.</p>';
