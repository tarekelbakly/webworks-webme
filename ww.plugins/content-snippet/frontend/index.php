<?php
function show_content_snippet($vars){
	if(!is_array($vars) && isset($vars->id) && $vars->id){
		$html=dbOne('select html from content_snippets where id='.$vars->id,'html');
		if($html)return $html;
	}
	return '<p>this Content Snippet is not yet defined.</p>';
}
