<?php
echo admin_menu(array(
	'Products'=>'/ww.admin/plugin.php?_plugin=products&amp;_page=products',
	'Categories'=>'/ww.admin/plugin.php?_plugin=products&amp;_page=categories',
	'Types'=>   '/ww.admin/plugin.php?_plugin=products&amp;_page=types'
),$_url);
