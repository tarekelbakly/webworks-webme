<?php
function TableOfContents_getContent(&$PAGEDATA) {
	$kids=Pages::getInstancesByParent($PAGEDATA->id);
	$c=$PAGEDATA->render();
	if (!count($kids->pages)) {
		$c.='<em>no sub-pages</em>';
	}
	else{
		$c.='<ul class="subpages">';
		foreach ($kids->pages as $kid) {
			$c.='<li><a href="'.$kid->getRelativeURL().'">'
				.htmlspecialchars($kid->name).'</a></li>';
		}
		$c.='</ul>';
	}
	if (isset($PAGEDATA->vars['footer'])) {
		$c.=$PAGEDATA->vars['footer'];
	}
	return $c;
}
