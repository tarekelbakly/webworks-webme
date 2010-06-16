<?php
$dir=$_REQUEST['image_gallery_directory'];

echo '<form action="/j/kfm/upload.php" method="POST" enctype="multipart/form-data">
	<input type="file" id="kfm_file" name="kfm_file[]" multiple="multiple">
	<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="9999999999">
	<input type="hidden" name="directory_name" value="'.htmlspecialchars($dir).'" />
	<input type="hidden" name="js" value="parent.location=\'/\';" />
	<input type="submit" id="upload" class="small" name="upload" value="Upload">
	</form>';
