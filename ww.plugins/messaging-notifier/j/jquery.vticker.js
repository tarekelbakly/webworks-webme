/*
* Tadas Juozapaitis ( kasp3rito@gmail.com )
*/

(function($){
$.fn.vTicker = function(options) {
	var defaults = {
		speed: 700,
		pause: 4000,
		showItems: 2,
		animation: '',
		mousePause: true
	};

	var options = $.extend(defaults, options);

	moveUp = function(obj, height){
		obj = obj.children('ul');
    	first = obj.children('li:first').clone(true);
		
    	obj.animate({top: '-=' + height + 'px'}, options.speed, function() {
        	$(this).children('li:first').remove();
        	$(this).css('top', '0px');
        });
		
		if(options.animation == 'fade')
		{
			obj.children('li:first').fadeOut(options.speed);
			obj.children('li:last').hide().fadeIn(options.speed);
		}

    	first.appendTo(obj);
	};
	
	return this.each(function() {
		obj = $(this);
		maxHeight = 0;

		obj.css({overflow: 'hidden', position: 'relative'})
			.children('ul').css({position: 'absolute', margin: 0, padding: 0})
			.children('li').css({margin: 0, padding: 0});

		obj.children('ul').children('li').each(function(){
			if($(this).height() > maxHeight)
			{
				maxHeight = $(this).height();
			}
		});

		obj.children('ul').children('li').each(function(){
			$(this).height(maxHeight);
		});

		obj.height(maxHeight * options.showItems);
		
    	interval = setInterval('moveUp(obj, maxHeight)', options.pause);
		
		if(options.mousePause)
		{
			obj.bind("mouseenter",function(){
				clearInterval(interval);
			}).bind("mouseleave",function(){
				interval = setInterval('moveUp(obj, maxHeight)', options.pause);
			});
		}
	});
};
})(jQuery);
