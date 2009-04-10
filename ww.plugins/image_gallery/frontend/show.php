<?php
function image_gallery_show($PAGEDATA){
	$vars=$PAGEDATA->vars;
	// {
	global $plugins_to_load;
	$c='';
	$start=getVar('start');
	if(!$start)$start=0;
	$vars=array_merge(array(
		'image_gallery_directory'=>'/',
		'image_gallery_x'=>3,
		'image_gallery_y'=>2,
		'image_gallery_thumbsize'=>150,
		'image_gallery_captionlength'=>100,
		'image_gallery_hoverphoto'=>0
	),$vars);
	$imagesPerPage=$vars['image_gallery_x']*$vars['image_gallery_y'];
	// }
	$dir_id=kfm_api_getDirectoryId(preg_replace('/^\//','',$vars['image_gallery_directory']));
	$images=kfm_loadFiles($dir_id);
	$images=$images['files'];
	$n=count($images);
	if($n){
		$c.='<table id="image_gallery" class="image_gallery {hoverphoto:'.($vars['image_gallery_hoverphoto']?'true':'false').',thumbsize:'.$vars['image_gallery_thumbsize'].',captionlength:'.$vars['image_gallery_captionlength'].',imgAt:'.$start.',dirid:'.$dir_id.',x:'.$vars['image_gallery_x'].',y:'.$vars['image_gallery_y'].'}">';
		if($n>$imagesPerPage){
			$prespage=preg_replace('/\?.*/','',$_SERVER['REQUEST_URI']);
			// { # prev
				$c.='<th class="prev" style="text-align:left" id="image_gallery_prev_wrapper">';
				if($start>0){
					$l=$start-$imagesPerPage;
					if($l<0)$l=0;
					$c.='<a href="'.$prespage.'?start='.$l.'">&lt;-- prev</a>';
				}
				$c.='</th>';
			// }
			for($l=1;$l<$vars['image_gallery_x']-1;++$l)$c.='<th></th>';
			// { # next
				$c.='<th class="next" style="text-align:right" id="image_gallery_next_wrapper">';
				if($start+$imagesPerPage<$n){
					$l=$start+$imagesPerPage;
					$c.='<a href="'.$prespage.'?start='.$l.'">next --&gt;</a>';
				}
				$c.='</th>';
			// }
		}
		$all=array();
		$s=$start+$vars['image_gallery_x']*$vars['image_gallery_y'];
		if($s>$n)$s=$n;
		for($i=$start;$i<$s;++$i){
			$cap=$images[$i]['caption'];
			if(strlen($cap)>$vars['image_gallery_captionlength'])$cap=substr($cap,0,$vars['image_gallery_captionlength']-3).'...';
			$all[]=array(
				'url'=>'/kfmget/'.$images[$i]['id'],
				'thumb'=>'/kfmget/'.$images[$i]['id'].',width='.$vars['image_gallery_thumbsize'].',height='.$vars['image_gallery_thumbsize'],
				'title'=>$images[$i]['caption'],
				'caption'=>htmlspecialchars($cap)
			);
		}
		for($row=0;$row<$vars['image_gallery_y'];++$row){
			$c.='<tr>';
			for($col=0;$col<$vars['image_gallery_x'];++$col){
				$i=$row*$vars['image_gallery_x']+$col;
				$c.='<td id="igCell_'.$row.'_'.$col.'">';
				if(isset($all[$i]))$c.='<div style="text-align:center" class="gallery_image"><a href="'.$all[$i]['url'].'" onclick="return lightbox_show(this.childNodes[0].src.replace(/,.*/,\'\'),\''.$all[$i]['title'].'\')"><img src="'.$all[$i]['thumb'].'" /><br style="clear:both" /><span class="caption">'.htmlspecialchars($all[$i]['caption']).'</span></a></div>';
				$c.='</td>';
			}
			$c.='</td>';
		}
		$c.='</table><script type="text/javascript" src="/ww.plugins/image_gallery/j/image.gallery.php"></script>';
		return $c;
	}else{
		return '<em>gallery "'.$vars['image_gallery_directory'].'" not found.</em>';
	}
}
