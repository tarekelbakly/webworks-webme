<?php
$c.='<div class="ad-gallery"> <div class="ad-image-wrapper"> </div> <div class="ad-controls"> </div> <div class="ad-nav"> <div class="ad-thumbs"> <ul class="ad-thumb-list">';
for($i=0;$i<$n;$i++){
	$c.='<li> <a href="/kfmget/'.$images[$i]['id'].'"> <img src="/kfmget/'.$images[$i]['id'].',width='.$vars['image_gallery_thumbsize'].',height='.$vars['image_gallery_thumbsize'].'" title="'.str_replace('\\\\n','<br />',$images[$i]['caption']).'"> </a> </li>';
}
$c.='</ul> </div> </div> </div>';
$c.='<script src="/ww.plugins/image_gallery/j/ad-gallery/jquery.ad-gallery.pack.js"></script><style type="text/css">@import "/ww.plugins/image_gallery/j/ad-gallery/jquery.ad-gallery.css";.ad-gallery .ad-image-wrapper{	height: 400px;}</style><script>
$("document").ready(function(){
	$(".ad-gallery").adGallery({
		animate_first_image:true,
		slideshow:{';
$slideshowvars=array();
if($vars['image_gallery_autostart']){
	$slideshowvars[]='enable:true';
	$slideshowvars[]='autostart:true';
}
$sp=(int)$vars['image_gallery_slidedelay'];
if($sp)$slideshowvars[]='speed:'.$sp;
$c.=join(',',$slideshowvars);
$c.='}
	});
});</script>';
