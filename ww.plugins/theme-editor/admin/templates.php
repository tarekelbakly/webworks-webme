<?php

if(isset($_REQUEST['action']) && ($_REQUEST['action']=='save')){
	file_put_contents(THEME_DIR.'/'.THEME.'/h/'.$name.'.html',$_REQUEST['theme-body']);
}
$f=file_get_contents(THEME_DIR.'/'.THEME.'/h/'.$name.'.html');

echo '<form action="/ww.admin/plugin.php" method="post">';
echo '<input type="hidden" name="_plugin" value="theme-editor" />';
echo '<input type="hidden" name="_page" value="index" />';
echo '<input type="hidden" name="name" value="'.$name.'" />';
echo '<input type="hidden" name="type" value="h" />';
echo '<textarea id="theme-body" name="theme-body">',htmlspecialchars($f),'</textarea>';
echo '<br /><input type="submit" onclick="document.getElementById(\'theme-body\').value=editor.getCode();" name="action" value="save" /></form>';
?>
<style>
.CodeMirror-wrapping{
	border: 1px solid #000;
}
</style>
<script src="/ww.plugins/theme-editor/j/CodeMirror-0.67/js/codemirror.js"></script>
<script type="text/javascript">
	var editor = CodeMirror.fromTextArea("theme-body", {
	  parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "parsehtmlmixed.js"],
		reindentOnLoad:true,
		height:450,
	  path: "/ww.plugins/theme-editor/j/CodeMirror-0.67/js/",
		stylesheet: ["/ww.plugins/theme-editor/j/CodeMirror-0.67/css/xmlcolors.css", "/ww.plugins/theme-editor/j/CodeMirror-0.67/css/jscolors.css", "/ww.plugins/theme-editor/j/CodeMirror-0.67/css/csscolors.css"]
	});
</script>
