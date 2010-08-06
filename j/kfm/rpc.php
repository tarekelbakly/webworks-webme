<?php
require 'initialise.php';

switch($_REQUEST['action']){
	case 'delete_file': // {
		$id=(int)$_REQUEST['id'];
		$file=kfmFile::getInstance($id);
		if($file){
			$file->delete();
			echo 'ok';
			exit;
		}
		else die('file does not exist');
	break; // }


	case 'prune': // {
		global $kfm;
		$root_id = $kfm->setting('root_folder_id');
		$root_directory = kfmDirectory::getInstance($root_id);
		prune($root_directory);
	break; // }
}

function prune ($dir) {
	global $root_id;
	$files = $dir->getFiles();
	$subDirs = $dir->getSubdirs();
	// { If the directory contains nothing and is not the root delete it
	if (!($files&&$subDirs)&&($dir->id!=$root_id)) {
		return $dir->delete();
	}
	// }
	elseif ($dir->hasSubdirs()) {
		foreach ($subDirs as $sub) {
			prune($sub);
		}
	}
}
