(function( $ ){
	$.fn.k3dCarousel= function( options ) {  
		return this.each(function() {
			var $this=$(this);
			var settings = {
				'radius' : $this.width()*.3,
				'centerX': $this.width()/2,
				'centerY': $this.height()/2,
				'rotateDuration': 1000,
				'pauseDuration' : 2000
			};
			if ( options ) { 
				$.extend( settings, options );
			}
			var items=$this.find('img').css({
					'position': 'absolute',
					'opacity': 0,
					'display': 'block'
				});
			items.each(function(index,el){
				el.style.left=(settings.centerX-el.offsetWidth/2)+'px';
				el.style.top='0px';
				el.origWidth=el.offsetWidth;
				el.origHeight=el.offsetHeight;
			});
			var numitems=items.length;
			var positions=[];
			var degs=Math.PI/(numitems/2);
			var iter=0;
			for (var i=0; i<numitems; ++i) {
				var sin=Math.sin(degs*i);
				positions.push({
					'left':   settings.centerX+(Math.cos(degs*i)*settings.radius),
					'zIndex': parseInt(50*sin+50),
					'top':    sin*10+10,
					'opacity': .45*sin+.55,
					'zoom': .4*sin+.6
				});
				items[i]=$(items[i]);
			}
			console.log(positions);
			this.style.position='relative';
			function setPositions(){
				for (var i=0; i<numitems; ++i) {
					var newWidth=positions[i].zoom*items[i][0].origWidth;
					var newHeight=positions[i].zoom*items[i][0].origHeight;
					items[(i+iter)%numitems].animate(
						{
							'left':    positions[i].left-newWidth/2,
							'opacity': positions[i].opacity,
							'top':     positions[i].top,
							'width':   newWidth,
							'height':  newHeight
						},
						settings.rotateDuration
					)
					.css('z-index',positions[i].zIndex);
				}
				iter++;
				setTimeout(setPositions,settings.pauseDuration+settings.rotateDuration);
			}
			setPositions();
		});
	};
})( jQuery );
