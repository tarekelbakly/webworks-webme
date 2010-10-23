<?php
/**
  * admin page for managing forums
  *
  * PHP Version 5
  *
  * @category   Whatever
  * @package    WebworksWebme
  * @subpackage Forum
  * @author     Kae Verens <kae@webworks.ie>
  * @license    GPL Version 2
  * @link       www.webworks.ie
 */

// { tabs nav
$c.= '<div class="tabs">'
	.'<ul>'
	.'<li><a href="#t-dashboard">Dashboard</a></li>'
	.'<li><a href="#t-forums">Forums</a></li>'
	.'<li><a href="#t-header">Header</a></li>'
	.'<li><a href="#t-footer">Footer</a></li>'
	.'</ul>';
// }
// { dashboard
$c.= '<div id="t-dashboard">please wait - loading</div>';
// }
// { forums
$c.= '<div id="t-forums">';
$c.= '</div>';
// }
// { header
$c.='<div id="t-header"><p>Text to be shown above the form</p>'
	.ckeditor('body', $page['body'])
	.'</div>';
// }
// { footer
$c.='<div id="t-footer"><p>Text to appear below the form.</p>';
$c.=ckeditor(
	'page_vars[footer]',
	(isset($vars['footer'])?$vars['footer']:''),
	0,
	$cssurl
);
$c.='</div>';
// }
$c.='</div>';
