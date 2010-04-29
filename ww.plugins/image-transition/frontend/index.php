<?php
function show_image_transition($vars){
	if(!is_array($vars) && isset($vars->id) && $vars->id){
		$r=dbRow('select * from image_transitions where id='.$vars->id);
		if($r && is_array($r)){
			$imgs=array();
			$dir=USERBASE.'f'.$r['directory'];
			$fs=new DirectoryIterator($dir);
			$max=array(0,0);
			foreach($fs as $f){
				if($f->isDot())continue;
				if(!preg_match('/\.(jpg|.jpeg|png|gif)$/i',$f->getFilename()))continue;
				list($width, $height) = getimagesize(USERBASE.'f'.$r['directory'].'/'.$f->getFilename());
				if(!$width || !$height)continue;
				if($width>$max[0])$max[0]=$width;
				if($height>$max[1])$max[1]=$height;
				$imgs[]=$f->getFilename();
			}
			if(!count($imgs))return '<em>no images in selected directory</em>';
			$html='<div style="width:'.$width.'px;height:'.$height.'px;" id="image_transitions_'.$vars->id.'">';
			$html.='<img src="/f'.$r['directory'].'/'.join('" /><img style="display:none" src="/f'.$r['directory'].'/',$imgs).'" /></div>';
			$html.='<script src="/ww.plugins/image-transition/j/jquery.cycle.all.min.js"></script><script>$(document).ready(function(){$("#image_transitions_'.$vars->id.'").cycle({fx:"'.$r['trans_type'].'",speed:'.$r['pause'].'})});</script>';
			return $html;
		}
	}
	return '<p>this Image Transition is not yet defined.</p>';
}