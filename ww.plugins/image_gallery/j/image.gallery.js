function ig_setImages(data){
	ig.images=data;
	ig_updateGallery(ig.imgAt);
	window.Lightbox.initialize(data);
}
function ig_updateGallery(at){
	var a,imgNum,img,x,y;
	if(at<0)at=0;
	ig.imgAt=at;
	if(ig.images.length>ig.x*ig.y){
		{ // prev
			var prev=document.getElementById('image_gallery_prev_wrapper');
			prev.innerHTML='';
			if(at)prev.appendChild(newLink('javascript:ig_updateGallery('+(at-ig.x*ig.y)+')','<-- prev','image_gallery_prev','prev'));
		}
		{ // next
			var next=document.getElementById('image_gallery_next_wrapper');
			next.innerHTML='';
			if(at+ig.x*ig.y<=ig.images.length)next.appendChild(newLink('javascript:ig_updateGallery('+(at+ig.x*ig.y)+')','next -->','image_gallery_next','next'));
		}
	}
	for(y=0;y<ig.y;++y){
		for(x=0;x<ig.x;++x){
			imgNum=at+(y*ig.x)+x;
			document.getElementById('igCell_'+y+'_'+x).innerHTML='';
			if(imgNum>=ig.images.length)continue;
			img={
				'src':'/kfmget/'+ig.images[imgNum].id+',width='+ig.thumbsize+',height='+ig.thumbsize,
				'caption':ig.images[imgNum].caption
			};
			var div=newEl('div',0,'gallery_image');
			div.style.textAlign='center';
			a=newLink('javascript:;');
			a.id="image_gallery_thumb_"+imgNum;
			$(a).click((function(at){
				return function(){
					Lightbox.show(at);
				};
			})(imgNum));
			a.appendChild(newImg(img.src,'',img.caption));
			if(ig.hoverphoto)$(a).bind('mouseover',function(){
				ig_update_static_photo(this.id.replace(/image_gallery_thumb_/,''));
			});
			var br=newEl('br');
			br.style.clear='both';
			a.appendChild(br);
			a.appendChild(newEl('span',0,'caption',img.caption));
			div.appendChild(a);
			document.getElementById('igCell_'+y+'_'+x).appendChild(div);
		}
	}
	if(!ig.first_call && document.getElementById('image_gallery_picture')){
		ig_update_static_photo(0);
	}
	ig.first_call=true;
}
function ig_update_static_photo(imgNum){
	var el=document.getElementById('image_gallery_picture');
	var width=el.offsetWidth,height=el.offsetHeight;
	el.style.background='url(/kfmget/'+ig.images[imgNum].id+',width='+width+',height='+height+') no-repeat center center';
}
var ig={
	first_call:0
};
var Lightbox={
	hideFrame:function(){
		Lightbox.frameVisible=0;
		delEl(['lightbox_frame','lightbox_shader']);
	},
	initialize:function(data){
		this.data=data;
	},
	preload:function(){
		this.data[this.at].img=newImg('/kfmget/'+this.data[this.at].id+',width='+this.imageMaxWidth+',height='+this.imageMaxHeight,'lightbox_preloader');
		$(this.data[this.at].img).bind('load',this.showImage);
		$(this.data[this.at].img).css({
			'position':'absolute',
			'left':-4000,
			'top':-4000,
			'visibility':'hidden'
		});
		document.body.appendChild(this.data[this.at].img);
	},
	show:function(at){
		this.at=(at+this.data.length)%this.data.length;
		if(!Lightbox.frameVisible){ // build frame
			this.showFrame();
		}
		{ // show image
			var imgData=this.data[this.at];
			if(!imgData.isLoaded)this.preload();
			else this.showImage();
		}
	},
	showFrame:function(){
		var margin=.05;
		var fixed=window.ie6?'absolute':'fixed';
		var ws=window.getSize().size;
		this.frameMaxWidth=ws.x*(1-margin*2);
		this.frameMaxHeight=ws.y*(1-margin*2);
		this.shader=newEl('div','lightbox_shader');
		$(this.shader).css({
			'position':fixed,
			'top':0,
			'width':ws.x,
			'height':ws.y,
			'left':0,
			'opacity':.7,
			'background':'#000'
		});
		$(this.shader).click(this.hideFrame);
		document.body.appendChild(this.shader);
		this.frame=newEl('div','lightbox_frame');
		$(this.frame).css({
			'position':fixed,
			'top':ws.y*margin,
			'width':this.frameMaxWidth,
			'height':this.frameMaxHeight,
			'left':ws.x*margin,
			'background':'#ccc',
			'z-index':20
		});
		document.body.appendChild(this.frame);
		this.controls=newEl('div','lightbox_controls');
		$(this.controls).css({
			'position':'absolute',
			'left':9,
			'bottom':9,
			'right':9,
			'border':'1px solid #000',
			'background':'#eee',
			'text-align':'center'
		});
		var prev=newLink('javascript:Lightbox.show(Lightbox.at-1);','','lightbox_prev');
		$(prev).css({
			'float':'left',
			'width':64,
			'height':64,
			'background':'url(/i/arrow_left.png) no-repeat'
		});
		this.controls.appendChild(prev);
		var next=newLink('javascript:Lightbox.show(Lightbox.at+1);','','lightbox_next');
		$(next).css({
			'float':'right',
			'width':64,
			'height':64,
			'background':'url(/i/arrow_right.png) no-repeat'
		});
		this.controls.appendChild(next);
		var caption=newEl('div','lightbox_caption');
		$(caption).css({
			'margin':'0 70px',
			'height':64,
			'text-align':'center',
			'font-style':'italic'
		});
		this.controls.appendChild(caption);
		var close=newLink('javascript:Lightbox.hideFrame();','close','image_gallery_close_lightbox');
		$(close).css({
			'position':'absolute',
			'z-index':2,
			'bottom':5,
			'left':100,
			'right':100
		});
		this.controls.appendChild(close);
		this.frame.appendChild(this.controls);
		this.imageMaxWidth=this.frameMaxWidth-20;
		this.imageMaxHeight=this.frameMaxHeight-100;
		this.imageWrapper=newEl('div','lightbox_imageWrapper');
		$(this.imageWrapper).css({
			'position':'absolute',
			'left':9,
			'top':9,
			'right':9,
			'bottom':89,
			'text-align':'center',
			'border':'1px solid #000',
			'background':'#fff no-repeat center center'
		});
		this.frame.appendChild(this.imageWrapper);
		Lightbox.frameVisible=1;
	},
	showImage:function(){
		var ws=window.getSize().size;
		if(document.getElementById('lightbox_preloader')){
			var img=document.getElementById('lightbox_preloader');
			Lightbox.data[Lightbox.at].width=img.offsetWidth;
			Lightbox.data[Lightbox.at].height=img.offsetHeight;
			Lightbox.data[Lightbox.at].isLoaded=1;
			delEl(img);
		}
		Lightbox.imageWrapper.innerHTML='';
		var minwidth=Lightbox.data[Lightbox.at].width<200?200:Lightbox.data[Lightbox.at].width;
		var minheight=Lightbox.data[Lightbox.at].height<200?200:Lightbox.data[Lightbox.at].height;
		Lightbox.imageWrapper.style.backgroundImage='url(/i/ajax-loader.gif)';
		$(Lightbox.frame).animate({
			'left':(ws.x-minwidth-20)/2,
			'top':(ws.y-Lightbox.data[Lightbox.at].height-100)/2,
			'height':Lightbox.data[Lightbox.at].height+100,
			'width':minwidth+20
		},400,'swing',function(){
			if(!document.getElementById('lightbox_caption'))return;
			Lightbox.imageWrapper.appendChild(newImg('/kfmget/'+Lightbox.data[Lightbox.at].id+',width='+Lightbox.imageMaxWidth+',height='+Lightbox.imageMaxHeight));
			Lightbox.imageWrapper.style.backgroundImage='none';
			document.getElementById('lightbox_caption').innerHTML=Lightbox.data[Lightbox.at].caption;
		});
	}
};
function ig_init(){
	ig.gallery=document.getElementById('image_gallery');
	$.extend(ig,eval('('+ig.gallery.className.replace(/^[^{]*/,'')+')'));
	x_ig_getImages(ig.dirid,ig_setImages);
}
$(document).ready(ig_init);
