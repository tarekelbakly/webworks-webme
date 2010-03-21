<?php
function show_content_snippet($vars){
	if(!is_array($vars) && isset($vars->id) && $vars->id){
		$html=cache_load('content_snippets',$vars->id.'-html');
		if($html===false){
			$html=dbOne('select html from content_snippets where id='.$vars->id,'html');
			cache_save('content_snippets',$vars->id.'-html',$html);
		}
		if($html)return $html;
	}
	return '<p>this Content Snippet is not yet defined.</p>';
}
