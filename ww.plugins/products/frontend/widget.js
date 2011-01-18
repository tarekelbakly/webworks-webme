function products_widget(id, categories) {
	function products_widget_drawText(text, url, x, y, angle){
		while (angle>90) {
			angle-=180;
		}
		var $str=$('<a href="'+url+'">'+text+'</a>')
			.css({
				position:'absolute',
				left:x,
				top:y
			});
		$str.appendTo($image);
		$str[0].style.left=($str[0].offsetLeft-$str[0].offsetWidth/2)+'px';
		$str[0].style.top=($str[0].offsetTop-$str[0].offsetHeight/2)+'px';

		$str[0].style.MozTransform='rotate('+angle+'deg)';
	}
	var $parent=$('#'+id)
		.empty()
		.css('position', 'relative');
	var diameter=$parent.css('width');
	var $image=$('<div>&nbsp;</div>')
		.css({
			width:diameter,
			height:diameter,
			position:'relative'
		})
		.appendTo($parent);
	$image.canvas();
	var radius=diameter.replace(/px/, '')/2;
	var stepat=0;
	var step=(Math.PI*2)/categories.length;
	var stepDeg=360/categories.length;
	var stepDegAt=0;
	var oldX=radius*2;
	var oldY=radius;
	$image.style({
		'strokeStyle' : 'rgba(0,0,0,.9)',
		'fillStyle'   : 'rgba(255,255,255,.9)',
		'lineWidth'   :.5 
	});
	for (var i=0; i<categories.length; ++i) {
		stepat+=step;
		stepDegAt+=stepDeg;
		var x=radius+radius*Math.cos(stepat);
		var y=radius+radius*Math.sin(stepat);
		$image
			.moveTo( [oldX, oldY] )
			.lineTo( [radius+(oldX-radius)/10, radius+(oldY-radius)/10] )
			.lineTo( [radius+(x-radius)/10, radius+(y-radius)/10] )
			.lineTo( [x, y] )
			.stroke()
			.fill();
		products_widget_drawText(
			categories[i].name,
			'/_r?type=products&products_category='+categories[i].id,
			radius+((oldX-radius)*.6+(x-radius)*.6)/2,
			radius+((oldY-radius)*.6+(y-radius)*.6)/2,
			stepDegAt-stepDeg/2
		);
		oldX=x;
		oldY=y;
	}
	$image
		.arc([radius,radius], {
			'radius':radius,
			'startAngle':0,
			'endAngle':360
		})
		.style({'strokeStyle' : 'rgba(0,0,0,.6)'})
		.stroke();
}
