<?php
function show_image_transition($vars){
	if(!is_array($vars) && isset($vars->id) && $vars->id){
		$r=cache_load('image-transitions','id'.$vars->id);
		if ($r===false) {
			$r=dbRow('select * from image_transitions where id='.$vars->id);
			cache_save('image-transitions', 'id'.$vars->id, $r);
		}
		if ($r && is_array($r)) {
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
			asort($imgs);
			if(!count($imgs))return '<em>no images in selected directory</em>';
			if ($r['trans_type']=='3dCarousel') {
				$html.='<div id="k3dCarousel'.$vars->id.'" style="height:'.($height+30).'px">';
				$html.='<img style="display:none" src="/f'.$r['directory'].'/'
					.join('" /><img style="display:none" src="/f'.$r['directory'].'/',$imgs)
					.'" />';
				$html.='</div>';
				WW_addScript('/ww.plugins/image-transition/frontend/jquery.k3dCarousel.js');
				$html.='<script>$(window).load(function(){
					$("#k3dCarousel'.$vars->id.'").k3dCarousel({
					});
				});</script>';
			}
			else {
				if($r['url']){
					$url=PAGE::getInstance($r['url'])->getRelativeUrl();
					$html.='<a href="'.$url.'"';
				}
				else $html.='<div';
				$html.=' style="display:block;width:'.$width.'px;height:'.$height.'px;" id="image_transitions_'.$vars->id.'">';
				$html.='<img src="/f'.$r['directory'].'/'.join('" /><img style="display:none" src="/f'.$r['directory'].'/',$imgs).'" />';
				if($r['url'])$html.='</a>';
				else $html.='</div>';
				WW_addScript('/ww.plugins/image-transition/frontend/jquery.cycle.all.min.js');
				$html.='<script>$(window).load(function(){$("#image_transitions_'.$vars->id.'").cycle({fx:"'.$r['trans_type'].'",speed:'.$r['pause'].'})});</script>';
			}
			return $html;
		}
	}
	return '<p>this Image Transition is not yet defined.</p>';
}
