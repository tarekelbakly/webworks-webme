		</div>
		</div>
		<div id="footer">
			<div class="maintainer"><?php echo __('WebME is created and maintained by <a href="http://webworks.ie/">webworks.ie</a>.'); ?></div>
			<div class="languages">
<?php // {
$p=$_SERVER['PHP_SELF'];
if ($handle = opendir(BASEDIR.'ww.lang')) {
	$files = array('en');
	while(false!==($file = readdir($handle))){
		if(substr($file,0,1)=='.')continue;
		if (is_dir(BASEDIR.'ww.lang/'.$file))$files[] = $file;
	}
	closedir($handle);
	sort($files);
	$available_languages = array();
	foreach($files as $f)echo '<a href="'.$p.'?__webme_language='.$f.'"><img src="/i/flags/'.$f.'.gif" /></a>';
}
// } ?>
			</div>
		</div>
		<script type="text/javascript">var plugins_to_load={<?php echo join(',',$plugins_to_load); ?>};</script>
	</body>
</html>
